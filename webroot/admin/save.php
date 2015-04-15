<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/schema.phpm' );

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

list( $must, $may ) = schema_get_object_requirements($object['objectClass']);

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
foreach ( $all_attrs as $attr ) {
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

do_ldap_attr_del( $objectdn, $dels );
do_ldap_modify( $objectdn, $adds );
if ( $new_rdn ) {
	$new_parent = ldap_dn_get_parent( $objectdn );
	do_ldap_rename( $objectdn, ldap_escape($new_rdn,'',LDAP_ESCAPE_DN), $new_parent );
        $objectdn = $new_rdn .','. $new_parent;
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	redirect( 'admin/object.php?dn='. urlencode($objectdn ) );
}
?>
