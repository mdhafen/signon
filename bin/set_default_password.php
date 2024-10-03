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
  $users = $ldap->quick_search( '(&(|(employeeType=Staff)(employeeType=Student)(employeeType=Other))(objectClass=inetOrgPerson))' , array() );
}

$change_users = array();
foreach ( $users as $user ) {
  if ( empty($user['givenName']) || empty($user['sn']) || empty($user['employeeNumber']) ) {
    print "Could not compute old default password for ". $user['dn'] ."\n";
    continue;
  }
  if ( empty($user['employeeType']) || $user['employeeType'][0] != 'Student' ) {
    // limit to students for now.
    continue;
  }
  $password = get_default_password($user['uid'][0]);
  if ( ! empty($password) ) {
    print "default password already set for ". $user['uid'][0] ."\n";
    continue;
  }
  $change_users[] = $user;
}
unset($users);

/* reauth ldap as root */
$ldap->do_connect( 'core', $ldap->config['userdn'], $ldap->config['passwd'] );
print "Continuing\n";

foreach ( $change_users as $user ) {
  // old password scheme
  $password = generate_default_password($user);

/*
  $password = create_password();
  // turn the three-word passphrase into a two-word passphrase
  $password = substr( $password, 0, strrpos($s,'-') );
 */

  print "Setting default password for ". $user['dn'] ."\n";

  set_default_password( $user['uid'][0], $password );
}

?>
