<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

if ( ! authenticate_api_client() ) {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>ACCESS_DENIED</flag></result>', '', $xml=1 );
  exit;
}

global $GOOGLE_DOMAIN;
$ldap = new LDAP_Wrapper();
$users = array();
$output = '<?xml version ="1.0"?><result>';
$dn = input( 'dn', INPUT_STR );
if ( !empty($dn) ) {
  $users = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
} else {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_INPUT</flag></result>', '', $xml=1 );
  exit;
}

$change_users = array();
foreach ( $users as $user ) {
  if ( empty($user['givenName']) || empty($user['sn']) || empty($user['employeeNumber']) ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>DOES_NOT_COMPUTE</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    continue;
  }
  if ( empty($user['employeeType']) || $user['employeeType'][0] != 'Student' ) {
    // limit to students for now.
    continue;
  }
  $password = get_default_password($user['dn']);
  if ( ! empty($password) ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>ALREADY_SET</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
    continue;
  }
  $change_users[] = $user;
}
unset($users);
if ( empty($change_users) ) {
  exit;
}

foreach ( $change_users as $user ) {
  // old password scheme
  $password = mb_strtolower(mb_substr($user['givenName'][0],0,1))
	 . mb_strtolower(mb_substr($user['sn'][0],0,1))
	 . $user['employeeNumber'][0];

/*
  $password = create_password();
  // turn the three-word passphrase into a two-word passphrase
  $password = substr( $password, 0, strrpos($s,'-') );
 */

  set_default_password( $user['dn'], $password );
}

$output .= '<state>success</state></result>';
output( $output, '', $xml=1 );

?>