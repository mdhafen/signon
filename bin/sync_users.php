<?php
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
    $mod_add = array();
    $mod_del = array();

    if ( !empty($entry['title']) && ( empty($thisUser['title']) || $thisUser['title'][0] != $entry['title'] ) ) {
      $mod_add['title'] = $entry['title'];

      !empty($thisUser['title']) && $mod_del['title'] = $thisUser['title'];
    }
    if ( !empty($entry['employeeType']) && ( empty($thisUser['employeeType']) || $thisUser['employeeType'][0] != $entry['employeeType'] ) ) {
      $mod_add['employeeType'] = $entry['employeeType'];

      !empty($thisUser['employeeType']) && $mod_del['employeeType'] = $thisUser['employeeType'];
    }
    if ( empty($thisUser['businessCategory'][0]) ) {
      $mod_add['businessCategory'] = $entry['businessCategory'];
    }
    if ( !empty($entry['employeeNumber']) && ( empty($thisUser['employeeNumber']) || $thisUser['employeeNumber'][0] != $entry['employeeNumber'] ) ) {
      $mod_add['employeeNumber'] = $entry['employeeNumber'];

      !empty($thisUser['employeeNumber']) && $mod_del['employeeNumber'] = $thisUser['employeeNumber'];
    }
    if ( empty($thisUser['description'][0]) ) {
      $mod_add['description'] = $entry['o'] .'-'. $entry['employeeType'];
    }
    if ( $thisUser['o'][0] != $entry['o'] ) {
      $mod_add['o'] = $entry['o'];
      $mod_add['departmentNumber'] = $entry['departmentNumber'];
      $mod_add['description'] = $entry['o'] .'-'. $entry['employeeType'];

      $mod_del['o'] = $thisUser['o'];
      $mod_del['departmentNumber'] = $thisUser['departmentNumber'];
      $mod_del['description'] = $thisUser['description'];
    }
    if ( !empty($entry['departmentNumber']) && ( empty($thisUser['departmentNumber']) || $thisUser['departmentNumber'][0] != $entry['departmentNumber'] ) ) {
      $mod_add['departmentNumber'] = $entry['departmentNumber'];
    }
    if ( !empty($entry['street']) && ( empty($thisUser['street']) || $thisUser['street'][0] != $entry['street'] ) ) {
      $mod_add['street'] = $entry['street'];
      !empty($entry['l']) && $mod_add['l'] = $entry['l'];
      !empty($entry['st']) && $mod_add['st'] = $entry['st'];
      !empty($entry['postalCode']) && $mod_add['postalCode'] = $entry['postalCode'];

      !empty($thisUser['street']) && $mod_del['street'] = $thisUser['street'];
      !empty($thisUser['l']) && $mod_del['l'] = $thisUser['l'];
      !empty($thisUser['st']) && $mod_del['st'] = $thisUser['st'];
      !empty($thisUser['postalCode']) && $mod_del['postalCode'] = $thisUser['postalCode'];
    }
    if ( !empty($entry['pager']) && ( empty($thisUser['pager'][0]) || $thisUser['pager'][0] != $entry['pager'] ) ) {
      $mod_add['pager'] = $entry['pager'];
      !empty($entry['telephoneNumber']) && $mod_add['telephoneNumber'] = $entry['telephoneNumber'];

      !empty($thisUser['pager']) && $mod_del['pager'] = $thisUser['pager'];
      !empty($thisUser['telephoneNumber']) && $mod_del['telephoneNumber'] = $thisUser['telephoneNumber'];
    }
    if ( !empty($entry['labeledURI']) && ( empty($thisUser['labeledURI'][0]) || $thisUser['labeledURI'][0] != $entry['labeledURI'] ) ) {
      $mod_add['labeledURI'] = $entry['labeledURI'];

      !empty($thisUser['labeledURI']) && $mod_del['labeledURI'] = $thisUser['labeledURI'];
    }
    if ( !empty($entry['destinationIndicator']) && ( empty($thisUser['destinationIndicator'][0]) || $thisUser['destinationIndicator'][0] != $entry['destinationIndicator'] ) ) {
      $mod_add['destinationIndicator'] = $entry['destinationIndicator'];
    }
    if ( empty($thisUser['uidNumber'][0]) && !empty($thisUser['sambaSID'][0]) ) {
      $new_uid = explode('-',$thisUser['sambaSID'][0]);
      $mod_add['uidNumber'] = end($new_uid);
      $mod_add['gidNumber'] = '65534';
      $mod_add['homeDirectory'] = '/Users/'. $thisUser['uid'][0];
      $mod_add['loginShell'] = '/bin/bash';
      $mod_add['objectclass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');
    }

    if ( !empty($mod_add) || !empty($mod_del) ) {
      $ldap->do_attr_del( $dn, array_keys($mod_del) );
      $ldap->do_modify( $dn, $mod_add );
      $mods = array_keys(array_merge($mod_del,$mod_add));
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
    $entry['sambaPwdLastSet'] = time();
    $entry['sambaAcctFlags'] = '[U ]';
    $new_uid = explode('-', $entry['sambaSID']);
    $entry['uidNumber'] = end($new_uid);
    $entry['gidNumber'] = '65534';
    $entry['homeDirectory'] = '/Users/'. $entry['uid'];
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
