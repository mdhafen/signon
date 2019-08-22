<?php
include_once( '../lib/data.phpm' );
include_once( '../lib/input.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

global $GOOGLE_DOMAIN;
$email = "";
if ( !empty($argv[1]) ) {
  $email = $argv[1];
  if ( empty( strpos($email,'@') ) ) {
    $email .= '@'. $GOOGLE_DOMAIN;
  }
  else if ( strtolower(strstr($email,'@')) != $GOOGLE_DOMAIN ) {
    $email = substr( $email, 0, strpos($email,'@') ) . '@' . $GOOGLE_DOMAIN;
  }
}

$ldap = new LDAP_Wrapper();
$users = array();
if ( !empty($argv[1]) ) {
  $uid = substr($email,0,strpos($email,'@'));
  $users = $ldap->quick_search( "(|(mail=$email)(uid=$uid))" , array() );
} else {
  $users = $ldap->quick_search( '(&(!(|(employeeType=Guest)(employeeType=Trusted)))(objectClass=inetOrgPerson))' , array() );
}

$change_users = array();
foreach ( $users as $user ) {
  $password = strtolower( substr($user['givenName'][0],0,1) . substr($user['sn'][0],0,1) . $user['employeeNumber'][0] );
  if ( $ldap->do_connect('core',$user['dn'],$password) ) {
    $change_users[] = $user;
  }
}
unset($users);

/* reauth ldap as root */
$ldap->do_connect( 'core', $ldap->config['userdn'], $ldap->config['passwd'] );

foreach ( $change_users as $user ) {
  $password = strtolower( substr($user['givenName'][0],0,1) . substr($user['sn'][0],0,1) . $user['telephoneNumber'][0] . $user['registeredAddress'][0] );

  	if ( !empty($user['employeeType'][0]) && strtolower(strstr($email,'@')) == '@'.$GOOGLE_DOMAIN ) {
		$result = google_set_password( $user['mail'][0], $password );
	}
	$result = set_password( $ldap, $user['dn'], $password );
}

?>
