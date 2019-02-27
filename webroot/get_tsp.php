<?php
include_once( '../lib/output.phpm' );

$output = array();

$policy_page = 'https://procedure.washk12.org/policy/3000/3730.txt';
$curl = curl_init();
curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $curl, CURLOPT_URL, $policy_page );
$html = curl_exec($curl);
curl_close($curl);

if (!empty($html)) {
    $output['article'] = $html;

    output( $output, 'get_aup.tmpl' );
}
?>
