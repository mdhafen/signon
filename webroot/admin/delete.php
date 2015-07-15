<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

global $config;

do_ldap_connect();
authorize( 'manage_objects' );

$dn = input( 'dn', INPUT_STR );
$op = input( 'op', INPUT_STR );

$set = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$children = ldap_quick_search( array( 'objectClass' => '*' ), array(), 1, $dn );
$groups = ldap_quick_search( array( 'member' => "$objectdn" ), array() );

if ( $op == 'Delete' && count($children) == 0 ) {
	$parentdn = ldap_dn_get_parent( $objectdn );
	remove_from_groups( $objectdn );
	if ( do_ldap_delete( $objectdn ) ) {
		redirect( 'admin/object.php?dn='. urlencode($parentdn ) );
	}
}

$output = array(
	'object_dn' => $objectdn,
	'object' => $object,
	'is_person' => is_person( $object ),
	'groups' => count($groups),
        'children' => count($children),
);

output( $output, 'admin/delete.tmpl' );
?>
