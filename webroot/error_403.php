<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

global $config;
$message = "";

if ( $_SERVER['REDIRECT_STATUS'] == "403" ) {
    $message = "Access Denied.";
    $url_base = substr( $config['base_url'], stripos($config['base_url'],'/',8) );
    if ( stripos($_SERVER['REDIRECT_URL'],$url_base.'/create') === 0 ) {
        $message .= "  Registration is only allowed on School District networks.";
    }
}

error( array($message) );
?>
