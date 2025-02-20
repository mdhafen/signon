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

$found_users = array();
foreach ( $users as $user ) {
  if ( empty($user['employeeType']) || $user['employeeType'][0] != 'Student' ) {
    // limit to students for now.
    $output .= '<user><state>error</state><flag>NOT_STUDENT</flag><message>'. htmlspecialchars($user['uid'][0],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></user>';
    continue;
  }
  $def_password = get_default_password($user['uid'][0]);
  if ( empty($def_password) ) {
    $output .= '<user><state>error</state><flag>NO_DEFAULT</flag><message>'. htmlspecialchars($user['uid'][0],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></user>';
    continue;
  }
  $output .= '<user><state>success</state><dn>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</dn><uid>'. htmlspecialchars($user['uid'][0],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</uid><message>'. htmlspecialchars($def_password,ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></user>';
}

$output .= '</result>';
output( $output, '', $xml=1 );

?>
