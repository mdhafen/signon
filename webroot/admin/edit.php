<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'manage_objects' );

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

ksort( $object, SORT_STRING | SORT_FLAG_CASE );

$parentdn = $ldap->dn_get_parent( $objectdn );
if ( $parentdn == $ldap->config['base'] ) {
	$parentdn = '';
}

if ( empty( $_SESSION['schema_attrs'] ) ) {
	$schema_attrs = $ldap->get_schema_attributes();
	$_SESSION['schema_attrs'] = $schema_attrs;
}
else {
	$schema_attrs = $_SESSION['schema_attrs'];
}

list( $must, $may ) = $ldap->schema_get_object_requirements($object['objectClass']);

$output = array(
	'object_dn' => $objectdn,
	'object' => $object,
	'is_person' => is_person( $object ),
	'parentdn' => $parentdn,
	'must' => $must,
	'may' => $may,
	'attrs' => $schema_attrs,
);

output( $output, 'admin/edit.tmpl' );
?>
