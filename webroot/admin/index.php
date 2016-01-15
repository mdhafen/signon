<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );

$ldap = do_ldap_connect();
authorize( 'reset_password' );

$children = ldap_quick_search( $ldap, array( 'objectClass' => '*' ), array(), 1, $ldap['base'] );
usort( $children, 'sorter' );

$output = array(
	'root' => $ldap['base'],
	'can_edit' => authorized('manage_objects'),
	'children' => $children,
);

output( $output, 'admin/index.tmpl' );

function sorter( $a, $b ) {
	$av = empty($a['cn']) ? $a['ou'][0] : $a['cn'][0];
	$bv = empty($b['cn']) ? $b['ou'][0] : $b['cn'][0];

	return strcasecmp( $av, $bv );
}
?>
