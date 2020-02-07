<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

global $RECAPTCHA_SECRET;

$op = input( 'op', INPUT_HTML_NONE );

if ( $op == 'recaptcha-verify' ) {
    $token = input( 'g-recaptcha-response', INPUT_STR );
    $output = '<?xml version="1.0"?>
<result>';

    if ( !$token ) {
        $output .= '<state>error</state><message>Captcha form empty</message></result>';
        output( $output, '', $xml=1 );
        exit;
    }

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $RECAPTCHA_SECRET, 'response' => $token);
    $options = array( 'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ));
    $context = stream_context_create($options);
    $response = file_get_contents( $url, false, $context );
    $responseKeys = json_decode($response,true);
    if ( $responseKeys["success"] ) {
        $output .= '<state>success</state><message>Captcha passed!</message></result>';
        output( $output, '', $xml=1 );
        exit;
    }
    else {
        $output .= '<state>error</state><message>Captcha failed!</message></result>';
        output( $output, '', $xml=1 );
        exit;
    }
}
$output .= '<state>error</state><message>Undefined Operation</message></result>';
output( $output, '', $xml=1 );
