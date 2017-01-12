<?php
include_once( '../lib/data.phpm' );

$ldap = new LDAP_Wrapper();
$users = $ldap->quick_search( '(objectClass=inetOrgPerson)', array() );
$dns = array();

foreach ( $users as $user ) {
  if ( !empty($user['homeDirectory'][0]) && stripos($user['homeDirectory'][0],'/home') === 0 ) {
    $home = str_ireplace('/home/','/Users/',$user['homeDirectory'][0]);
    $ldap->do_modify( $user['dn'], array('homeDirectory'=>$home) );
  }
  if ( empty($user['businessCategory'][0]) ) {
    switch ( $user['employeeType'][0] ) {
      case 'Staff':
      case 'Student':
      case 'Guest':
      case 'Trusted':
      case 'Other':
// Confinement
        $ldap->do_modify( $user['dn'], array('businessCategory'=>$user['employeeType'][0]) );
        break;
      default:
        $ldap->do_modify( $user['dn'], array('businessCategory'=>'Other') );
    }
  }
}
?>
