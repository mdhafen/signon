<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

$c_mac = input( 'client_mac', INPUT_HTML_NONE );
if ( !empty($c_mac) ) {
  $_SESSION['client_mac'] = $c_mac;
}

$output = array();
output( $output, 'index.tmpl' );
?>
