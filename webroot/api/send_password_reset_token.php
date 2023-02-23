<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

authenticate();

$return = input( 'return', INPUT_STR );

if ( ! ( authorized('set_password') || ( !empty($_SESSION['loggedin_user']) && strcasecmp($dn,$_SESSION['loggedin_user']['userid']) == 0 ) ) ) {
	if ( empty($return) ) {
		output( '<?xml version ="1.0"?><result><state>error</state><message>ACCESS_DENIED</message></result>', '', $xml=1 );
	} else {
		error(array('ACCESS_DENIED'));
	}
	exit;
}

$ldap = new LDAP_Wrapper();
global $GOOGLE_DOMAIN;

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
$uid = $object['uid'][0];
unset( $object['dn'] );

$token_obj = create_password_reset_token($uid);
if ( empty($token_obj) ) {
    if ( empty($return) ) {
        output( '<?xml version ="1.0"?><result><state>error</state><message>CREATE_TOKEN_FAILED</message></result>', '', $xml=1 );
    } else {
        error(array('CREATE_TOKEN_FAILED'));
    }
    exit;
}

$message_data = array(
    'uid' => $object['uid'][0],
    'token' => $token_obj['token'],
);
send_message( 'PASSWD_RESET', $message_data, $object['labeledURI'][0] );

/*
 Date format below converts MySQL date to string that javascript will convert
 to the browser's local time. (Timezone is explicitly dropped as we assume
 the browsers timezone is the same as the servers.)
 */
$output = '<?xml version="1.0"?>
<reset_token>
<state>Success</state>
<token>'. $token_obj['token'] .'</token>
<expire>'. date('Y-m-d\TH:i:s', strtotime($token_obj['timestamp'])) .'</expire>
</reset_token>';

if ( empty($return) ) {
    output( $output, '', $xml=1 );
} else {
    redirect('admin/object.php?dn='.urlencode($objectdn) );
}
?>
