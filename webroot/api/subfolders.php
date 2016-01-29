<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );

$ldap = new LDAP_Wrapper();
if ( ! authenticate() ) {
	output( '<?xml version="1.0"?><error>ACCESS_DENIED</error>', '', $xml=1 );
	exit;
}

$dn = input( 'dn', INPUT_STR );
$set = $ldap->quick_search( array( 'objectClass' => 'organizationalUnit' ), array(), 1, $dn );
$folders = array();
foreach ( $set as $ou ) {
	$folders[] = array( 'ou' => $ou['ou'][0], 'dn' => $ou['dn'] );
}

usort( $folders, function($a,$b){return strcasecmp($a['ou'],$b['ou']);} );

$output = '<?xml version="1.0"?>
<folders>
';
foreach ( $folders as $ou ) {
	$output .= '	<folder>
	<dn>'.$ou['dn'].'</dn>
	<ou>'.$ou['ou'].'</ou>
	</folder>
';
}
$output .= '</folders>';

output( $output, '', $xml=1 );
?>
