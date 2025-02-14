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
  else if ( strtolower(strstr($email,'@')) != '@'.$GOOGLE_DOMAIN ) {
    print "Error: email not in google domain!\n";
    exit;
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
  if ( $user['employeeType'][0] == 'Student' ) {
    if ( empty($user['givenName']) || empty($user['sn']) || empty($user['employeeNumber']) ) {
      print "Could not compute student old default password for ". $user['dn'] ."\n";
      continue;
    }
    $password = mb_strtolower(mb_substr($user['givenName'][0],0,1))
       . mb_strtolower(mb_substr($user['sn'][0],0,1))
       . $user['employeeNumber'][0];
    if ( @$ldap->do_connect('core',$user['dn'],$password) ) {
      $change_users[] = $user;
      continue;
    }
  }

  $password = get_default_password($user['uid'][0]);
  if ( empty($password) ) {
    print "No default password for ". $user['uid'][0] ."\n";
    continue;
  }

  if ( @$ldap->do_connect('core',$user['dn'],$password) ) {
    $change_users[] = $user;
  }
}
unset($users);

/* reauth ldap as root */
$ldap->do_connect( 'core', $ldap->config['userdn'], $ldap->config['passwd'] );
print "Continuing\n";

foreach ( $change_users as $user ) {
  $password = generate_default_password($user);
  set_default_password( $user['uid'][0], $password );

  print "Changing password for ". $user['dn'] ."\n";

  if ( strtolower(strstr($user['mail'][0],'@')) == '@'.$GOOGLE_DOMAIN ) {
    $result = google_set_password( $user['mail'][0], $password );
  }
  $result = set_password( $ldap, $user['dn'], $password );
}

?>
