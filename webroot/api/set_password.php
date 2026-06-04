<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

if ( ! authorized('set_password') && ! authenticate_api_client() ) {
	output( '<?xml version ="1.0"?><result><state>error</state><flag>ACCESS_DENIED</flag></result>', '', $xml=1 );
	exit;
}

global $GOOGLE_DOMAIN;
$ldap = new LDAP_Wrapper();

$dn = input( 'dn', INPUT_STR );
$uid = input( 'uid', INPUT_STR );

if ( !empty($dn) ) {
    $set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
}
else if ( !empty($uid) ) {
    $set = $ldap->quick_search( array('uid'=>$uid), array() );
}
else {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_INPUT</flag></result>', '', $xml=1 );
    exit;
}

$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$user_lock = get_lock_status( $object['uid'][0] );
if ( !empty($user_lock) && (
   ( $object['businessCategory'][0] == 'Student' && !authorized('lock_student') ) || ( $object['businessCategory'][0] == 'Staff' && !authorized('lock_staff')
) ) ) {
	output( '<?xml version ="1.0"?><result><state>error</state><flag>USER_LOCKED</flag></result>', '', $xml=1 );
	exit;
}

$password = input( 'password', INPUT_STR );
$pwned_bypass = input( 'pwned_bypass', INPUT_STR );

if ( ! empty($password) ) {
	if ( strlen($password) < 8 ) {
		output( '<?xml version ="1.0"?><result><state>error</state><flag>PASS_TOO_SHORT</flag></result>', '', $xml=1 );
		exit;
	}
	if ( !$pwned_bypass && $times = is_pwned_password($password) ) {
		output( '<?xml version ="1.0"?><result><state>error</state><flag>PASS_TOO_COMMON</flag><message>'. $times .'</message></result>', '', $xml=1 );
		exit;
	}

	if ( !empty($object['employeeType'][0]) && strripos($object['mail'][0],'@'.$GOOGLE_DOMAIN) !== False ) {
		$result = call_set_ad_password( $object['uid'][0], $password );
		if ( !empty($result) ) {
            output( '<?xml version ="1.0"?><result><state>error</state><flag>AD_SETPASSWD</flag><message>'. $result .'</message></result>', '', $xml=1 );
            exit;
		}
		$result = google_set_password( $object['mail'][0], $password );
	}
	$result = set_password( $ldap, $objectdn, $password );
	if ( ! $result ) {
		log_attr_change( $objectdn, array('userPassword'=>'') );
        output( '<?xml version ="1.0"?><result><state>success</state></result>', '', $xml=1 );
	}
	else {
        output( '<?xml version ="1.0"?><result><state>error</state><flag>PASS_SET_FAILED</flag></result>', '', $xml=1 );
	}
}
else {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_INPUT</flag></result>', '', $xml=1 );
}
?>
