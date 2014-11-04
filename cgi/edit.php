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

$parentdn = substr( $objectdn, strpos( $objectdn, ',' ) +1 );
if ( $parentdn == $config['ldap']['base'] ) {
	$parentdn = '';
}

$schema = get_schema( $_SESSION['schema'] );

$output = array(
	'object_dn' => $objectdn,
	'object' => $object,
	'is_person' => is_person( $object ),
	'parentdn' => $parentdn,
	'schema' => $schema,
);

output( $output, 'edit.tmpl' );
?>
