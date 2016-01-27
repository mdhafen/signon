<?php
// spoof these for lib/config.phpm
$_SERVER['REQUEST_URI'] = '';
$_SERVER['SERVER_NAME'] = '';
$_SERVER['SERVER_PORT'] = '';

include_once( '../lib/data.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

$google_cache = google_get_all_users();

$students = array();
//$in_file = 'PowerSchool_QuickInfo_Export_Students-to-Google-Apps.csv';
if ( !empty($argv[1]) ) {
  $in_file = $argv[1];
  $h = fopen( $in_file, 'r' );
  while ( ! feof($h) ) {
    $row = fgetcsv($h);
    $students[ strtolower($row[0]) ] = $row[3];
  }
  array_shift($students);  // drop the csv header
  $students = array_filter($students);
}

$ldap = do_ldap_connect();
$users = array();
$users = ldap_quick_search( $ldap, '(&(!(employeeType=Guest))(objectClass=inetOrgPerson))' , array() );
$users_lookup = array();

while ( !empty($users) ) {
  $thisUser = array_shift($users);
  $lookup = empty($thisUser['employeeNumber']) ? $thisUser['mail'][0] : $thisUser['employeeNumber'][0];
  $users_lookup[ $lookup ] = $thisUser;
}
unset($users);

foreach ( $google_cache as $g_user ) {
  if ( stripos($g_user['orgUnitPath']),'nonusers') !== FALSE ) continue; //service account
  $entry = google_user_hash_for_ldap( $g_user );
  $output = "";
  if ( ( !empty($entry['employeeNumber']) && !empty($users_lookup[ $entry['employeeNumber'] ]) ) || !empty($users_lookup[ $entry['mail'] ]) ) {
    if ( empty($entry['employeeNumber']) || empty($users_lookup[ $entry['employeeNumber'] ]) ) {
      $thisUser = $users_lookup[ $entry['mail'] ];
      unset($users_lookup[ $entry['mail'] ]);
    }
    else {
      $thisUser = $users_lookup[ $entry['employeeNumber'] ];
      unset($users_lookup[ $entry['employeeNumber'] ]);
    }
    $dn = $thisUser['dn'];
    $mod = array();
    if ( !empty($entry['title']) && ( empty($thisUser['title']) || $thisUser['title'][0] != $entry['title'] ) ) {
      $mod['title'] = $entry['title'];
    }
    if ( !empty($entry['employeeType']) && ( empty($thisUser['employeeType']) || $thisUser['employeeType'][0] != $entry['employeeType'] ) ) {
      $mod['employeeType'] = $entry['employeeType'];
    }
    if ( !empty($entry['employeeNumber']) && ( empty($thisUser['employeeNumber']) || $thisUser['employeeNumber'][0] != $entry['employeeNumber'] ) ) {
      $mod['employeeNumber'] = $entry['employeeNumber'];
    }
    if ( empty($thisUser['description'][0]) ) {
      $mod['description'] = $thisUser['l'][0] .'-'. $entry['employeeType'];
    }
    if ( $thisUser['l'][0] != $entry['l'] ) {
      $mod['l'] = $entry['l'];
      $mod['description'] = $entry['l'] .'-'. $entry['employeeType'];
    }

    if ( !empty($mod) ) {
      do_ldap_attr_del( $ldap, $dn, array_keys($mod) );
      do_ldap_modify( $ldap, $dn, $mod );
      $output .= "mod ";
    }

    if ( strcasecmp( $dn, $entry['dn'] ) != 0 ) {
      do_ldap_rename( $ldap, $dn, "uid=". ldap_escape($entry['uid'],'',LDAP_ESCAPE_DN), ldap_dn_get_parent($entry['dn']) );
      $output .= "move ";
    }

    if ( $entry['employeeType'] == 'Student' && empty($thisUser['userPassword']) && !empty($students[ $entry['mail'] ]) ) {
      set_password( $entry['dn'], $students[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  else {
    $entry['objectclass'] = array('top','inetOrgPerson','sambaSamAccount');

    $dn = $entry['dn'];
    unset( $entry['dn'] );

    $entry['sambaSID'] = ldap_get_next_num( $ldap, 'sambaSID' );

    do_ldap_add( $ldap, $dn, $entry );
    $entry['dn'] = $dn;
    $output .= "Add ";

    if ( $entry['employeeType'] == 'Student' && !empty($students[ $entry['mail'] ]) ) {
      set_password( $dn, $students[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  if ( !empty($output) ) {
    print "Working ". $entry['mail'] ." : ". $output ."\n";
  }
}
foreach ( $users_lookup as $lookup => $thisUser ) {
  print "Working ". $thisUser['mail'][0] ." : isn't in google! ";
  $dn = $thisUser['dn'];
  do_ldap_delete( $ldap, $dn );
  print "Deleted\n";
}

?>
