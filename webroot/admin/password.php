<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

$ldap = new LDAP_Wrapper();

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
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
$default = input( 'default', INPUT_STR );

if ( !empty($default) ) {
	if ( !empty($object['employeeNumber'][0]) && !empty($object['givenName'][0]) && !empty($object['sn'][0]) ) {
		$password = strtolower( substr($object['givenName'][0],0,1) . substr($object['sn'][0],0,1) . $object['employeeNumber'][0] );
		$confirm = $password;
	}
}

if ( ! empty($password) ) {
	if ( $password !== $confirm ) {
		$output['error'] = 'PASS_NO_MATCH';
		output( $output, 'admin/password.tmpl' );
		exit;
	}
	if ( strlen($password) < 8 ) {
		$output['error'] = 'PASS_TO_SHORT';
		output( $output, 'admin/password.tmpl' );
		exit;
	}
	if ( $times = is_pwned_password($password) ) {
		$output['error'] = 'PASS_TO_COMMON';
        $output['error_times'] = $times;
		output( $output, 'admin/password.tmpl' );
		exit;
	}

	if ( !empty($object['employeeType'][0]) ) {
		$result = google_set_password( $object['mail'][0], $password );
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
