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

$ldap = new LDAP_Wrapper();
$dn = $_SESSION['userid'];
$set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
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
			set_password( $ldap, $dn, $password );
                        if ( !empty($object['employeeType'][0]) && $object['employeeType'][0] != 'Guest' ) {
				google_set_password( $object['mail'][0], $password, false );
			}
			$output['result'] = 'Password Set';
		}
		else {
			$output['result'] = 'Passwords do not match';
		}
	}

	output( $output, 'profile' );
}
?>
