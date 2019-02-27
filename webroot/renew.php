<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

$token = input( 't', INPUT_HTML_NONE );
$submit = input( 'submit', INPUT_STR );

if ( !empty($token) && !empty($submit) ) {
    $sig = get_guest_signature( null, $token );
    $uid = $sig['guest_uid'];

    record_guest_signature( $uid );
}

$output = array('token'=>$token);
output( $output, 'renew.tmpl' );
?>
