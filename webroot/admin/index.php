<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );

do_ldap_connect();
if ( ! authenticate() ) {
	output( array(), 'login' );
	exit;
}

global $config;
$set = ldap_quick_search( array( 'objectClass' => 'organizationalUnit' ), array(), 1, $config['ldap']['base'] );
$folders = array();
foreach ( $set as $ou ) {
	$folders[] = array( 'ou' => $ou['ou'][0], 'dn' => $ou['dn'] );
}

usort( $folders, function($a,$b){return strcasecmp($a['ou'],$b['ou']);} );

$children = ldap_quick_search( array( 'objectClass' => '*' ), array(), 1, $config['ldap']['base'] );
usort( $children, 'sorter' );

$output = array(
	'folders' => $folders,
	'children' => $children,
);

output( $output, 'index.tmpl' );

function sorter( $a, $b ) {
	$av = empty($a['cn']) ? $a['ou'][0] : $a['cn'][0];
	$bv = empty($b['cn']) ? $b['ou'][0] : $b['cn'][0];

	return strcasecmp( $av, $bv );
}
?>
