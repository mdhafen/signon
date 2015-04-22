<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/schema.phpm' );
include_once( '../inc/google.phpm' );

authorize( 'set_password' );

$errors = array();

do_ldap_connect();
$dn = $_SESSION['userid'];
$set = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];

if ( ! is_person( $object ) ) {
	$errors[] = 'PROFILE_NOT_USER';
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	$output = array(
		'object' => $object,
	);

	$password = input( 'password', INPUT_STR );
	$password2 = input( 'password2', INPUT_STR );

	if ( !empty($password) && !empty($password2) ) {
		if ( $password === $password2 ) {
			set_password( $dn, $password );
			google_set_password( $object['mail'][0], $password );
			$output['result'] = 'Password Set';
		}
		else {
			$output['result'] = 'Passwords do not match';
		}
	}

	output( $output, 'profile' );
}
?>
