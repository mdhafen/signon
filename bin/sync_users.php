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

$ldap = new LDAP_Wrapper();
$users = array();
$users = $ldap->quick_search( '(&(|(employeeType=Student)(employeeType=Staff))(objectClass=inetOrgPerson))' , array() );
$users_lookup = array();

while ( !empty($users) ) {
  $thisUser = array_shift($users);
  $lookup = empty($thisUser['employeeNumber']) ? $thisUser['mail'][0] : $thisUser['employeeNumber'][0];
  $users_lookup[ $lookup ] = $thisUser;
}
unset($users);

foreach ( $google_cache as $g_user ) {
  if ( stripos($g_user['orgUnitPath'],'nonusers') !== FALSE ) continue; //service account
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
      $mod['departmentNumber'] = $entry['departmentNumber'];
      $mod['description'] = $entry['l'] .'-'. $entry['employeeType'];
    }
    if ( !empty($entry['departmentNumber']) && ( empty($thisUser['departmentNumber']) || $thisUser['departmentNumber'][0] != $entry['departmentNumber'] ) ) {
      $mod['departmentNumber'] = $entry['departmentNumber'];
    }
    if ( empty($thisUser['uidNumber'][0]) && !empty($thisUser['sambaSID'][0]) ) {
      $new_uid = explode('-',$thisUser['sambaSID'][0]);
      $mod['uidNumber'] = $new_uid[4];
      $mod['gidNumber'] = '65534';
      $mod['homeDirectory'] = '/home/'. $thisUser['uid'][0];
      $mod['loginShell'] = '/bin/bash';
      $mod['objectclass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');
    }

    if ( !empty($mod) ) {
      $ldap->do_attr_del( $dn, array_keys($mod) );
      $ldap->do_modify( $dn, $mod );
      $mods = array_keys($mod);
      sort($mods);
      $output .= "mod (". implode(',',$mods) .") ";
    }

    if ( strcasecmp( $dn, $entry['dn'] ) != 0 ) {
      $ldap->do_rename( $dn, "uid=". ldap_escape($entry['uid'],'',LDAP_ESCAPE_DN), $ldap->dn_get_parent($entry['dn']) );
      $output .= "move ";
    }

    if ( $entry['employeeType'] == 'Student' && empty($thisUser['userPassword']) && !empty($students[ $entry['mail'] ]) ) {
      set_password( $ldap, $entry['dn'], $students[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  else {
    $entry['objectclass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');

    $dn = $entry['dn'];
    unset( $entry['dn'] );

    $entry['sambaSID'] = $ldap->get_next_num( 'sambaSID' );
    $new_uid = explode('-', $entry['sambaSID']);
    $entry['uidNumber'] = $new_uid[4];
    $entry['gidNumber'] = '65534';
    $entry['homeDirectory'] = '/home/'. $entry['uid'];
    $entry['loginShell'] = '/bin/bash';

    $ldap->do_add( $dn, $entry );
    $entry['dn'] = $dn;
    $output .= "Add ";

    if ( $entry['employeeType'] == 'Student' && !empty($students[ $entry['mail'] ]) ) {
      set_password( $ldap, $dn, $students[ $entry['mail'] ] );
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
  $ldap->do_delete( $dn );
  print "Deleted\n";
}

?>
