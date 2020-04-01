<?php
include_once( '../lib/data.phpm' );
include_once( '../lib/input.phpm' );

$group_name = 'vpn2_access';
$init_group_member = 'jeremy.cox';

$users = array();
if ( !empty($argv[1]) ) {
  $in_file = $argv[1];
  $h = fopen( $in_file, 'r' );
  while ( ! feof($h) ) {
    $row = fgetcsv($h);
    $users[ strtolower($row[5]) ] = $row[1];
  }
  array_shift($users);  // drop the csv header
  $users = array_unique(array_filter($users));
}

$ldap = new LDAP_Wrapper();
$group = $ldap->quick_search( "(&(objectClass=posixGroup)(cn=$group_name))", array() );
$dn = $group[0]['dn'];
$present = $group[0]['memberUid'];
$mod_replace = ['memberUid' => [$init_group_member] ];
$mod = 0;

foreach ( $users as $p_user => $p_email ) {
  $search = $ldap->quick_search( "(&(employeeType=Staff)(|(mail=$p_email)(uid=$p_user)))" , array() );
  if ( !empty($search) ) {
    $thisUser = $search[0];
  }
  if ( ! empty($thisUser['uid']) ) {
    $mod_replace['memberUid'][] = $thisUser['uid'][0];
    $index = array_search($thisUser['uid'][0],$present);
    if ( $index === false ) {
        $mod = 1;
    }
    else {
        unset( $present[ $index ] );
    }
  }
}

if ( $mod || !empty($present) ) {
  $mod_replace['memberUid'] = array_values(array_unique(array_filter($mod_replace['memberUid'])));
  $ldap->do_attr_replace( $dn, $mod_replace );
  print "Finished updating $dn\n";
}
else {
  print "No changes found for $dn\n";
}

?>
