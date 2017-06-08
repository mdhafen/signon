<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'reset_password' );

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

$children = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 1, $dn );
usort( $children, 'sorter' );

$output = array(
	'object_dn' => $objectdn,
	'object' => $object,
	'is_person' => is_person( $object ),
	'parentdn' => $parentdn,
	'children' => $children,
	'can_edit' => authorized('manage_objects'),
	'can_password' => authorized('reset_password') || ($objectdn == $_SESSION['userid']),
);

output( $output, 'admin/object.tmpl' );

function sorter( $a, $b ) {
	$aa = substr( $a['dn'], 0, strpos($a['dn'],'=') );
	$ba = substr( $b['dn'], 0, strpos($b['dn'],'=') );

	return strcasecmp( $a[$aa][0], $b[$ba][0] );
}
?>
