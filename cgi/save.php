<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/schema.phpm' );

global $config;

do_ldap_connect();
authorize( 'manage_objects' );

$dn = input( 'dn', INPUT_STR );

$set = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

ksort( $object, SORT_STRING | SORT_FLAG_CASE );

if ( empty( $_SESSION['schema_objects'] ) ) {
	$schema_objects = get_schema_objects();
	$_SESSION['schema_objects'] = $schema_objects;
}
else {
	$schema_objects = $_SESSION['schema_objects'];
}

if ( empty( $_SESSION['schema_attrs'] ) ) {
	$schema_attrs = get_schema_attributes();
	$_SESSION['schema_attrs'] = $schema_attrs;
}
else {
	$schema_attrs = $_SESSION['schema_attrs'];
}

$must = array();
$may = array();
$classes = $object['objectClass'];
for ( $i = 0; $i < count($classes); $i++ ) {
	$oc = $classes[$i];

	if ( ! empty($schema_objects[$oc]['SUP']) ) {
		foreach ( $schema_objects[$oc]['SUP'] as $sup_oc ) {
			$classes[] = $sup_oc;
		}
	}

	if ( ! empty($schema_objects[$oc]['MUST']) ) {
		$must = array_merge( $must, $schema_objects[$oc]['MUST'] );
	}
	if ( ! empty($schema_objects[$oc]['MAY']) ) {
		$may = array_merge( $may, $schema_objects[$oc]['MAY'] );
	}
}

$must = array_unique( $must );
$may = array_unique( $may );
$may = array_diff( $may, $must );

$errors = array();

if ( $objectdn == $config['ldap']['base'] ) {
	$errors[] = 'EDIT_BASE_DENIED';
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	redirect( 'object.php?dn='. urlencode($objectdn ) );
}
?>
