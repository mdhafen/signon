<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );

$output = array();
$output['password'] = create_password();

output( $output, 'passwords.tmpl' );
?>
