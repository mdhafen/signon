<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );

$token = input( 't', INPUT_HTML_NONE );
$submit = input( 'submit', INPUT_STR );

$output = array('token'=>$token);

if ( !empty($token) && !empty($submit) ) {
    $sig = get_guest_signature( null, $token );
    $uid = $sig['guest_uid'];

    if ( !empty($uid) ) {
        record_guest_signature( $uid );
    }
    else {
        $output['error'] = "token not found";
    }
}

output( $output, 'renew.tmpl' );
?>
