<?php
include_once( '../lib/data.phpm' );

$ldap = do_ldap_connect();
$groups = ldap_quick_search( $ldap, '(objectClass=groupOfNames)', array() );
$dns = array();

foreach ( $groups as $group ) {
  foreach ( $group['member'] as $member ) {
    if ( empty($dns[$member]) ) {
      $dns[ $member ] = 1;
    }
    else {
      $dns[ $member ]++;
    }
  }
}

foreach ( $dns as $dn => $count ) {
  $user = ldap_quick_search( $ldap, array( 'objectClass' => '*' ), array(), 0, $dn );
  if ( empty($user) ) {
    $user = preg_split('/(?<!\\\\),/',$dn);
    $user = explode( '=', $user[0] );
    $uid = $user[1];
    $users = ldap_quick_search( $ldap, array( 'uid' => $uid ), array() );
    if ( !empty($users) ) {
      $user = $users[0];
      ldap_fix_group_memberships( $ldap, $dn, $user['dn'] );
      print "fixed $dn => ". $user['dn'] ."\n";
    }
  }
}

?>
