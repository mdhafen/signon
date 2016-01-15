<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/schema.phpm' );

$ldap = do_ldap_connect();
authorize( 'manage_objects' );

$op = input( 'action', INPUT_STR );
$dn = input( 'dn', INPUT_STR );
$object = array();

if ( $op == 'Add' ) {
	$classes = input( 'classes', INPUT_HTML_NONE );
	$object['objectClass'] = explode( ' ', $classes );
	$objectdn = input( 'dn', INPUT_HTML_NONE );
}
else {
	$set = ldap_quick_search( $ldap, array( 'objectClass' => '*' ), array(), 0, $dn );
	$object = $set[0];
	$objectdn = $object['dn'];
	unset( $object['dn'] );
}
$rdn_attr = substr( $objectdn, 0, strpos( $objectdn, '=' ) );

ksort( $object, SORT_STRING | SORT_FLAG_CASE );

list( $must, $may ) = schema_get_object_requirements($ldap,$object['objectClass']);

$errors = array();

if ( $objectdn == $ldap['base'] ) {
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
		if ( $op != 'Add' && $attr != 'sambaSID' ) {
			$errors[] = "EDIT_MISSING_ATTR $attr";
		}
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

if ( !empty($adds) || !empty($dels) ) {
	if ( $op == 'Add' ) {
		$password = '';
		if ( in_array( 'userPassword', array_keys($adds) ) ) {
			$password = $adds['userPassword'][0];
			unset( $adds['userPassword'] );
		}
		$adds['objectClass'] = $object['objectClass'];
		if ( in_array( 'sambaSamAccount', $adds['objectClass'] ) ) {
			$adds['sambaSID'] = ldap_get_next_num($ldap,'sambaSID');
		}
		if ( do_ldap_add( $ldap, $objectdn, $adds ) ) {
			if ( !empty($password) ) {
				set_password( $ldap, $objectdn, $password );
			}
		}
		else {
			$errors[] = "There was an error creating the account";
		}
	}
	else {
		// watch for $rdn_attr in particular
		$new_rdn = '';
		if ( in_array( $rdn_attr, array_keys($adds) ) || in_array( $rdn_attr, array_keys($dels) ) ) {
			$new_rdn = $adds[ $rdn_attr ][0];
			unset( $adds[ $rdn_attr ] );
			unset( $dels[ $rdn_attr ] );
		}

		$password = '';
		if ( in_array( 'userPassword', array_keys($adds) ) ) {
			$password = $adds['userPassword'][0];
			unset( $adds['userPassword'] );
		}

		do_ldap_attr_del( $ldap, $objectdn, $dels );
		do_ldap_attr_add( $ldap, $objectdn, $adds );

		if ( !empty($password) ) {
			set_password( $ldap, $objectdn, $password );
		}

		if ( $new_rdn ) {
			$new_parent = ldap_dn_get_parent( $objectdn );
			do_ldap_rename( $ldap, $objectdn, $rdn_attr ."=". ldap_escape($new_rdn,'',LDAP_ESCAPE_DN), $new_parent );
			$objectdn = $rdn_attr ."=". $new_rdn .','. $new_parent;
		}
	}
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	redirect( 'admin/object.php?dn='. urlencode($objectdn ) );
}
?>
