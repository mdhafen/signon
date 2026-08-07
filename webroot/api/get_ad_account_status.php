<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

if ( ! authorized('set_password') && ! authenticate_api_client() ) {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>ACCESS_DENIED</flag></result>', '', $xml=1 );
  exit;
}

$output = '<?xml version ="1.0"?><result>';

$uid = input( 'uid', INPUT_STR );
if ( empty($uid) ) {
  output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_INPUT</flag></result>', '', $xml=1 );
  exit;
}

$ad = new LDAP_Wrapper('AD');
$obj = array('uid'=>array($uid));
list($has_ad_pass,$has_ad_enable) = has_ad_account($obj,$ad);

$output .= '<state>success</state><user><uid>'. $uid .'</uid><password>'. ( $has_ad_pass ? 'Yes' : 'No' ) .'</password><enabled>'. ( $has_ad_enable ? 'Yes' : 'No' ) .'</enabled></user>';

$output .= '</result>';
output( $output, '', $xml=1 );

?>
