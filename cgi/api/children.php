<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );

do_ldap_connect();
if ( ! authenticate() ) {
	output( '<?xml version ="1.0"?><error>ACCESS_DENIED</error>', '', $xml=1 );
	exit;
}

$dn = input( 'dn', INPUT_STR );
$objects = ldap_quick_search( array( 'objectClass' => '*' ), array(), 1, $dn );

usort( $objects, "sorter" );

$output = '<?xml version="1.0"?>
<children>
';
foreach ( $objects as $obj ) {
	$output .= "	<object>\n";
	foreach ( $obj as $attr => $vals ) {
		if ( is_array( $vals ) ) {
			foreach ( $vals as $v ) {
				$output .= "	<$attr>".$v."</$attr>\n";
			}
		}
		else {
			$output .= "	<$attr>".$vals."</$attr>\n";
		}
	}
	$output .= "	</object>\n";
}
$output .= '</children>';

output( $output, '', $xml=1 );

function sorter( $a, $b ) {
	$av = empty($a['cn']) ? $a['ou'][0] : $a['cn'][0];
	$bv = empty($b['cn']) ? $b['ou'][0] : $b['cn'][0];

	return strcasecmp( $av, $bv );
}
?>
