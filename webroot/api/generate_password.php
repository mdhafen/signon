<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );

$password = create_password();
$output = '<?xml version="1.0"?>
<result><state>success</state><password>'. $password .'</password></result>';

output( $output );
?>
