<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'manage_objects' );

$dn = input( 'dn', INPUT_STR );
$op = input( 'op', INPUT_STR );

$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$children = $ldap->quick_search( '(objectClass=*)', array(), 1, $dn );
$groups = $ldap->quick_search( array( 'member' => "$objectdn" ), array() );

if ( $op == 'Delete' && count($children) == 0 ) {
	$parentdn = $ldap->dn_get_parent( $objectdn );
	remove_from_groups( $ldap, $objectdn );
	if ( $ldap->do_delete( $objectdn ) ) {
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
