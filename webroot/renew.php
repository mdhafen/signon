<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );

$token = input( 't', INPUT_HTML_NONE );
$submit = input( 'submit', INPUT_STR );

if ( empty($token) && !empty($_SERVER['PATH_INFO']) ) {
    $paths = explode( '/', $_SERVER['PATH_INFO'] );
    if ( !empty($paths) ) {
        $token = !empty($paths[0]) ? $paths[0] : $paths[1];
    }
}

$output = array('token'=>$token);

if ( !empty($token) && !empty($submit) ) {
    $sig = get_guest_signature( null, $token );
    $uid = $sig['guest_uid'];

    if ( !empty($uid) ) {
        $result = record_guest_signature( $uid );
        $output['recorded'] = $result;
    }
    else {
        $output['error'] = "token not found";
    }
}

output( $output, 'renew.tmpl' );
?>
