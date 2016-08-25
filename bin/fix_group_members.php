<?php
include_once( '../lib/data.phpm' );

$ldap = new LDAP_Wrapper();
$groups = $ldap->quick_search( '(objectClass=groupOfNames)', array() );
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
  $user = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
  if ( empty($user) ) {
    $user = preg_split('/(?<!\\\\),/',$dn);
    $user = explode( '=', $user[0] );
    $uid = $user[1];
    $users = $ldap->quick_search( array( 'uid' => $uid ), array() );
    if ( !empty($users) ) {
      $user = $users[0];
      $ldap->fix_group_memberships( $dn, $user['dn'] );
      print "fixed $dn => ". $user['dn'] ."\n";
    }
    else {
      $groups = $ldap->quick_search( '(|(member='. $dn .')(memberUid='. $uid .'))', array() );
      foreach ( $groups as $group ) {
        foreach ( $group['objectClass'] as $class ) {
          if ( strcasecmp($class,'groupofnames') === 0 ) {
            if ( count($group['groupOfNames']) > 1 ) {
              $ldap->do_attr_del( $group['dn'], array('member'=>$dn) );
            }
            else {  // because groupOfNames->member can not be empty
              $ldap->do_modify( $group['dn'], array('member'=>$ldap->config['userdn']);
            }
            print "removed $dn from ". $group['dn'] ."\n";
          }
          if ( strcasecmp($class,'posixgroup') === 0 ) {
            $ldap->do_attr_del( $group['dn'], array('memberUid'=>$uid) );
            print "removed $uid from ". $group['dn'] ."\n";
          }
        }
      }
    }
  }
}

?>
