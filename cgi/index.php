<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

do_ldap_connect();
$user = ldap_quick_search( array( 'uid' => 'michael.hafen' ), array() );

$output = array(
	'user' => $user[0],
	'next' => ldap_get_next_SID(),
);

output( $output, 'index.tmpl' );
?>
