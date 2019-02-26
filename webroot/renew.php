<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

$token = input( 't', INPUT_HTML_NONE );
if ( !empty($token) ) {

}

$output = array('token'=>$token);
output( $output, 'renew.tmpl' );
?>
