<?php
include_once( '../lib/data.phpm' );

$groups = ldap_quick_search( '(objectClass=groupOfNames)', array() );
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
  $user = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
  if ( empty($user) ) {
    $user = preg_split('/(?<!\\\\),/',$dn);
    $user = explode( '=', $user[0] );
    $uid = $user[1];
    $users = ldap_quick_search( array( 'uid' => $uid ), array() );
    if ( !empty($users) ) {
      $user = $users[0];
      ldap_fix_group_memberships( $dn, $user['dn'] );
      print "fixed $dn => ". $user['dn'] ."\n";
    }
  }
}

?>