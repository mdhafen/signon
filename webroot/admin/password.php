<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

$ldap = new LDAP_Wrapper();
global $GOOGLE_DOMAIN;

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$output = array(
	'object_dn' => $objectdn,
);

authenticate();

if ( ! authorized('set_password') ) {
	$output['error'] = 'ACCESS_DENIED';
	output( $output, 'admin/password.tmpl' );
	exit;
}
$user_lock = get_lock_status( $object['uid'][0] );
if ( !empty($user_lock) && (
   ( $object['businessCategory'][0] == 'Student' && !authorized('lock_student') ) || ( $object['businessCategory'][0] == 'Staff' && !authorized('lock_staff')
) ) ) {
	$output['error'] = 'USER_LOCKED';
	output( $output, 'admin/password.tmpl' );
	exit;
}

$password = input( 'password', INPUT_STR );
$confirm = input( 'confirm', INPUT_STR );
$default = input( 'default', INPUT_STR );
$pwned_bypass = 0;

if ( !empty($default) ) {
	if ( !empty($objectdn) ) {
		$password = get_default_password($object['uid'][0]);
		$confirm = $password;
		$pwned_bypass = 1;
		if ( empty($password) ) {
			$output['error'] = 'NO_DEFAULT_PASS';
			output( $output, 'admin/password.tmpl' );
			exit;
		}
	}
}

if ( ! empty($password) ) {
	if ( $password !== $confirm ) {
		$output['error'] = 'PASS_NO_MATCH';
		output( $output, 'admin/password.tmpl' );
		exit;
	}
	if ( strlen($password) < 8 ) {
		$output['error'] = 'PASS_TOO_SHORT';
		output( $output, 'admin/password.tmpl' );
		exit;
	}
	if ( !$pwned_bypass && $times = is_pwned_password($password) ) {
		$output['error'] = 'PASS_TOO_COMMON';
        $output['error_times'] = $times;
		output( $output, 'admin/password.tmpl' );
		exit;
	}

	if ( !empty($object['employeeType'][0]) && strripos($object['mail'][0],'@'.$GOOGLE_DOMAIN) !== False ) {
		$result = google_set_password( $object['mail'][0], $password );
		$output['google_result'] = $result;
	}
	$result = set_password( $ldap, $objectdn, $password );
	if ( $result ) {
		log_attr_change( $objectdn, array('userPassword'=>'') );
		$output['success'] = 1;
		output( $output, 'admin/password.tmpl' );
	}
	else {
		$output['error'] = 'PASS_SET_FAILED';
		output( $output, 'admin/password.tmpl' );
	}
}
else {
	output( $output, 'admin/password.tmpl' );
}
?>
