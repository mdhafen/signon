<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

do_ldap_connect();
if ( ! authenticate() ) {
	output( array(), 'login' );
	exit;
}

$user = empty($_SESSION['loggedin_user']) ? array() : $_SESSION['loggedin_user'];
//$user = ldap_quick_search( array( 'uid' => 'michael.hafen' ), array() );

$output = array(
	'user' => ( count($user) || ! count($user) ) ? array() : $user[0],
	//'dump' => print_r( $user, true ),
	'dump' => print_r( $_SESSION, true ),
);

output( $output, 'index.tmpl' );
?>
