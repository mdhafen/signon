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

    populate_static_user_attrs($entry);

    $dn = $thisUser['dn'];
    $rdn_attr = substr( $dn, 0, strpos($dn,'=') );
    $ignored_fields = array($rdn_attr,'dn','objectClass','userPassword','sambaNTPassword');
    $dynamic_fields = array('sambaSID','sambaPwdLastSet','uidNumber');
    $mod_add = array();
    $mod_del = array();
    $mod_rep = array();
    foreach ( $entry as $field => $value ) {
        if ( array_search($field,$ignored_fields) !== false ) { continue; }
        if ( array_search($field,$dynamic_fields) !== false ) { continue; }
        if ( empty($thisUser[$field]) && !empty($value) ) {
            $mod_add[$field] = $value;
        }
        else if ( !empty($value) && $thisUser[$field][0] != $value ) {
            $mod_rep[$field] = $value;
        }
    }
    $dynamic_generated = false;
    foreach ( $dynamic_fields as $field ) {
        if ( empty($thisUser[$field]) ) {
            if ( empty($dynamic_generated) ) {
                populate_dynacic_user_attrs($ldap,$entry);
                $dynamic_generated = 1;
            }
            if ( !empty($entry[$field]) ) {
                $mod_add[$field] = $entry[$field];
            }
        }
    }
    foreach ( $thisUser as $field => $values ) {
        if ( array_search($field,$ignored_fields) !== false ) { continue; }
        if ( array_search($field,$dynamic_fields) !== false ) { continue; }
        if ( empty($entry[$field]) ) {
            $mod_del[$field] = array();
        }
    }

    if ( !empty($mod_add) || !empty($mod_del) || !empty($mod_rep) ) {
      $ldap->do_attr_del( $dn, $mod_del );
      $ldap->do_modify( $dn, $mod_add );
      $ldap->do_attr_replace( $dn, $mod_rep );
      $mods = array_keys(array_merge($mod_del,$mod_add,$mod_rep));
      sort($mods);
      $output .= "mod (". implode(',',$mods) .") ";
    }

    if ( strcasecmp( $dn, $entry['dn'] ) != 0 ) {
      $ldap->do_rename( $dn, "$rdn_attr=". ldap_escape($entry[$rdn_attr],'',LDAP_ESCAPE_DN), $ldap->dn_get_parent($entry['dn']) );
      $output .= "move ";
    }

    if ( $entry['employeeType'] == 'Student' && empty($thisUser['userPassword']) && !empty($st_passwds[ $entry['mail'] ]) ) {
      set_password( $ldap, $entry['dn'], $st_passwds[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  else {
    $dn = $entry['dn'];
    unset( $entry['dn'] );

    populate_static_user_attrs($entry);
    populate_dynamic_user_attrs($ldap,$entry);

    $result = $ldap->do_add( $dn, $entry );
    $entry['dn'] = $dn;
    $output .= "Add ";

    if ( $result ) {
      $def_passwd = get_default_password( $entry['uid'] ) ?? generate_default_password($entry);
      if ( $entry['employeeType'] == 'Student' && ( !empty($st_passwds[ $entry['mail'] ]) || !empty($def_passwd) ) ) {
        $passwd = $st_passwds[ $entry['mail'] ] ?? $def_passwd;
        set_password( $ldap, $dn, $passwd );
        $output .= "And set Password ";
      }
    }
    else {
      error_log( $ldap->get_error() );
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
