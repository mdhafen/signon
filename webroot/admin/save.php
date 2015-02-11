<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/schema.phpm' );

global $config, $ldap;

do_ldap_connect();
authorize( 'manage_objects' );

$dn = input( 'dn', INPUT_STR );

$set = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );
$rdn_attr = substr( $objectdn, 0, strpos( $objectdn, '=' ) );

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

// gather input
$count = input( 'count', INPUT_PINT );
$input = array();
for ( $i = 1; $i < $count; $i++ ) {
	$attr = input( "${i}_attr", INPUT_HTML_NONE );
	$vals = input( "${i}_val", INPUT_HTML_NONE );
	$vals = array_filter( $vals );
	if ( ! empty($vals) ) {
		$input[ $attr ] = $vals;
	}
}
$input_attrs = array_keys( $input );
foreach ( $must as $attr ) {
	if ( ! in_array( $attr, $input_attrs ) ) {
		$errors[] = "EDIT_MISSING_ATTR $attr";
	}
}
foreach ( $input as $attr => $vals ) {
	if ( ! in_array( $attr, $must ) && ! in_array( $attr, $may ) ) {
		$errors[] = "EDIT_UNKNOWN_ATTR $attr";
	}
}

// compare to object
$adds = array();
$dels = array();
$object_attrs = array_keys( $object );
$all_attrs = array_unique( array_merge( $object_attrs, $input_attrs ) );
foreach ( $input_attrs as $attr ) {
	if ( ! in_array( $attr, $object_attrs ) ) {
		$adds[ $attr ] = $input[ $attr ];
	}
	else if ( ! in_array( $attr, $input_attrs ) ) {
		$dels[ $attr ] = $object[ $attr ];
	}
	else {
		foreach ( $input[ $attr ] as $val ) {
			if ( ! in_array( $val, $object[ $attr ] ) ) {
				$adds[ $attr ][] = $val;
			}
		}
		foreach ( $object[ $attr ] as $val ) {
			if ( ! in_array( $val, $input[ $attr ] ) ) {
				$dels[ $attr ][] = $val;
			}
		}
	}
}

// watch for $rdn_attr in particular
$new_rdn = '';
if ( in_array( $rdn_attr, array_keys($adds) ) || in_array( $rdn_attr, array_keys($dels) ) ) {
	$new_rdn = $adds[ $rdn_attr ][0];
	unset( $adds[ $rdn_attr ] );
	unset( $dels[ $rdn_attr ] );
}

// FIXME do deletes
// FIXME do adds
// FIXME do ldap_rename if necessary

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	redirect( 'object.php?dn='. urlencode($objectdn ) );
}
?>
