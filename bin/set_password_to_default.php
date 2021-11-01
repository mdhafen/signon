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
  // FIXME limit to students for now
  $users = $ldap->quick_search( '(&(!(|(employeeType=Guest)(employeeType=Trusted)))(objectClass=inetOrgPerson)(employeeType=Student))' , array() );
}

$change_users = array();
foreach ( $users as $user ) {
  $password = get_default_password($user['uid'][0]);
  if ( empty($password) ) {
    print "default password not set for ". $user['uid'][0] ."\n";

    if ( empty($user['givenName']) || empty($user['sn']) || empty($user['employeeNumber']) ) {
      print "Could not compute old default password for ". $user['dn'] ."\n";
      continue;
    }

    $password = mb_strtolower(mb_substr($user['givenName'][0],0,1))
	 . mb_strtolower(mb_substr($user['sn'][0],0,1))
	 . $user['employeeNumber'][0];

/*
    $password = create_password();
    // turn the three-word passphrase into a two-word passphrase
    $password = substr( $password, 0, strrpos($s,'-') );
 */

    print "Setting default password for ". $user['dn'] ."\n";
    set_default_password( $user['uid'][0], $password );
  }

/* FIXME - for now only set password where there isn't one
  if ( ! @$ldap->do_connect('core',$user['dn'],$password) ) {
    $change_users[] = $user;
  }
 */
  if ( empty($user['userPassword']) ) {
    $change_users[] = $user;
  }
}
unset($users);

/* reauth ldap as root */
$ldap->do_connect( 'core', $ldap->config['userdn'], $ldap->config['passwd'] );
print "Continuing\n";

foreach ( $change_users as $user ) {
  $password = get_default_password($user['uid'][0]);

  print "Setting password for ". $user['dn'] ."\n";
  if ( !empty($user['employeeType'][0]) && strtolower(strstr($email,'@')) == '@'.$GOOGLE_DOMAIN ) {
    $result = google_set_password( $user['mail'][0], $password );
  }
  $result = set_password( $ldap, $user['dn'], $password );
}

?>
