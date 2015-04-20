<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/schema.phpm' );

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
	output( $output, 'profile' );
}
?>