<?php
include_once( '../lib/data.phpm' );
include_once( '../lib/input.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

$email = "";
$google_cache = array();
if ( !empty($argv[1]) ) {
  if ( !empty($argv[1]) ) {
    $email = $argv[1];
    if ( stripos($email,'@washk12') === false ) {
      $email .= '@washk12.org';
    }
  }

  $google_cache = array( get_user_google( $email ) );
}
else {
  $tmp_array = google_get_all_users();

  foreach ( $tmp_array as $g_user ) {
    if ( stripos($g_user['orgUnitPath'],'nonusers') !== FALSE ) continue; // service account
    if ( stripos($g_user['orgUnitPath'],'/student') !== 0 ) continue; // only mass sync students for now
    if ( stripos($g_user['orgUnitPath'],'/students/graduates') !== FALSE ) continue; // don't sync graduated students
    if ( stripos($g_user['orgUnitPath'],'/students/lego users') !== FALSE ) continue; // don't sync lego non-users
    if ( stripos($g_user['orgUnitPath'],'/students/transfer pending') !== FALSE ) continue; // don't sync transfered students

    $google_cache[] = $g_user;
  }
}

$st_passwds = array();
//$in_file = 'PowerSchool_QuickInfo_Export_Students-to-Google-Apps.csv';
if ( !empty($argv[2]) ) {
  $in_file = $argv[2];
  $h = fopen( $in_file, 'r' );
  while ( ! feof($h) ) {
    $row = fgetcsv($h);
    $st_passwds[ strtolower($row[0]) ] = $row[3];
  }
  array_shift($st_passwds);  // drop the csv header
  $st_passwds = array_filter($st_passwds);
}

$ldap = new LDAP_Wrapper();
$users = array();
if ( !empty($argv[1]) ) {
  $uid = substr($email,0,strpos($email,'@'));
  $users = $ldap->quick_search( "(&(|(mail=$email)(uid=$uid))(!(|(employeeType=Guest)(employeeType=Trusted))))" , array() );
} else {
  $users = $ldap->quick_search( '(&(employeeType=Student)(objectClass=inetOrgPerson))' , array(), 2, 'ou=Students,dc=wcsd' );
}
$users_lookup = array();
$users_cache = array();

while ( !empty($users) ) {
  $thisUser = array_shift($users);
  $users_cache[ mb_strtolower($thisUser['dn']) ] = $thisUser;
  $users_lookup[ mb_strtolower($thisUser['uid'][0]) ] = mb_strtolower($thisUser['dn']);
}
unset($users);

foreach ( $google_cache as $g_user ) {
  if ( !$g_user ) continue;  // empty array - user not in google
  $entry = google_user_hash_for_ldap( $g_user );
  $output = "";
  $thisUser = array();
  if ( ( !empty($entry['dn']) && !empty($users_cache[ mb_strtolower($entry['dn']) ]) ) ||
       ( !empty($entry['uid']) && !empty($users_lookup[ mb_strtolower($entry['uid']) ]) ) ) {
    if ( !empty($entry['dn']) && !empty($users_cache[ mb_strtolower($entry['dn']) ]) ) {
      $thisUser = $users_cache[ mb_strtolower($entry['dn']) ];
      unset( $users_cache[ mb_strtolower($entry['dn']) ] );
    }
    else {
      $thisDN = $users_lookup[ mb_strtolower($entry['uid']) ];
      $thisUser = $users_cache[ $thisDN ];
      unset( $users_cache[ $thisDN ] );
    }
  }

  list( $mods, $move, $add ) = do_google_sync( $ldap, $thisUser, $entry, set_password:false );
  if ( empty($add) ) {
    $output .= "mod (". implode(',',$mods) .") ";
    if ( !empty($move) ) {
      $output .= "move ";
    }
    if ( $entry['employeeType'] == 'Student' && empty($thisUser['userPassword']) && !empty($st_passwds[ $entry['mail'] ]) ) {
      set_password( $ldap, $entry['dn'], $st_passwds[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  else {
    $output .= "add ";

    $results = $ldap->quick_search( "(&(|(mail=".$entry['mail'].")(uid=".$entry['uid']."))(!(|(employeeType=Guest)(employeeType=Trusted))))" , array() );
    $thisUser = $results[0];
    $dn = $thisUser['dn'];

    $def_passwd = get_default_password( $entry['uid'] ) ?? generate_default_password($thisUser);
    if ( $entry['employeeType'] == 'Student' && ( !empty($st_passwds[ $entry['mail'] ]) || !empty($def_passwd) ) ) {
      $passwd = $st_passwds[ $entry['mail'] ] ?? $def_passwd;
      set_password( $ldap, $dn, $passwd );
      $output .= "And set Password ";
    }
  }
  if ( !empty($output) ) {
    print "Update ". $entry['mail'] ." : ". $output ."\n";
  }
}
foreach ( $users_cache as $dn => $thisUser ) {
  print "Update ". $dn ." : isn't in google! ";
  $ldap->do_delete( $dn );
  print "Deleted\n";
}

?>
