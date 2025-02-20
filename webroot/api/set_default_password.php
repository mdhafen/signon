<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

if ( ! authorized('reset_password') && ! authenticate_api_client() ) {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>ACCESS_DENIED</flag></result>', '', $xml=1 );
  exit;
}

global $GOOGLE_DOMAIN;
$ldap = new LDAP_Wrapper();
$users = array();
$output = '<?xml version ="1.0"?><result>';
$dn = input( 'dn', INPUT_STR );
$uid = input( 'uid', INPUT_STR );
$override = input( 'override', INPUT_STR );
$new_passwd = input( 'password', INPUT_STR );
if ( !empty($dn) ) {
  $users = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
} else if ( !empty($uid) ) {
  $users = $ldap->quick_search( array( 'uid' => $uid ), array() );
} else {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_INPUT</flag></result>', '', $xml=1 );
  exit;
}

if ( count($users) < 1 ) {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_FOUND</flag></result>', '', $xml=1 );
  exit;
}

$change_users = array();
foreach ( $users as $user ) {
  if ( empty($user['givenName']) || empty($user['sn']) || empty($user['employeeNumber']) ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>DOES_NOT_COMPUTE</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    exit;
  }
  if ( empty($user['employeeType']) || $user['employeeType'][0] != 'Student' ) {
    // limit to students for now.
    output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_STUDENT</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    exit;
  }
  // old password scheme
  $old_password = mb_strtolower(mb_substr($user['givenName'][0],0,1))
	 . mb_strtolower(mb_substr($user['sn'][0],0,1))
	 . $user['employeeNumber'][0];

  $def_password = get_default_password($user['uid'][0]);
  if ( ! empty($def_password) && ( ! $override || $def_password != $old_password ) ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>ALREADY_SET</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    exit;
  }
  if ( ! @$ldap->do_connect('core',$user['dn'],$old_password) ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>ALREADY_CHANGED</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    exit;
  }
  $ldap->do_connect( 'core', $ldap->config['userdn'], $ldap->config['passwd'] );
  $change_users[] = $user;
}
unset($users);
if ( empty($change_users) ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_CHANGES</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    exit;
}

$output .= '<messages>';
foreach ( $change_users as $user ) {
  if ( $new_passwd ) {
    $password = $new_passwd;
  }
  else {
    $password = generate_default_password($user);
  }

  set_default_password( $user['uid'][0], $password );
  $output .= '<message><uid>'. htmlspecialchars($user['uid'][0],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</uid><password>'. htmlspecialchars($password,ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</password></message>';
}

$output .= '</messages><state>success</state></result>';
output( $output, '', $xml=1 );

?>
