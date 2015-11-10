<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

global $config;

do_ldap_connect();

$dn = input( 'dn', INPUT_STR );

$set = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$output = array(
	'object_dn' => $objectdn,
);

authenticate();

if ( ! ( authorized('reset_password') || ( !empty($_SESSION['loggedin_user']) && strcasecmp($dn,$_SESSION['loggedin_user']['userid']) == 0 ) ) ) {
	$output['error'] = 'ACCESS_DENIED';
	output( $output, 'admin/password.tmpl' );
	exit;
}

$password = input( 'password', INPUT_STR );
$confirm = input( 'confirm', INPUT_STR );

if ( ! empty($password) ) {
	if ( $password !== $confirm ) {
		$output['error'] = 'PASS_NO_MATCH';
		output( $output, 'admin/password.tmpl' );
		exit;
	}

	if ( set_password( $objectdn, $password ) ) {
		if ( !empty($object['employeeType'][0]) && $object['employeeType'][0] != 'Guest' ) {
			google_set_password( $object['mail'][0], $password );
		}
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
