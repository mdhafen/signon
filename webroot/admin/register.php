<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/labs.phpm' );

do_ldap_connect();
authorize( 'reset_password' );

$output = array();
$mac = input( 'client_mac', INPUT_STR );
$location = input( 'loc', INPUT_PINT );
$description = input( 'desc', INPUT_HTML_NONE );
$op = input( 'op', INPUT_STR );

if ( empty($mac) ) {
    $mac = $_SESSION['client_mac'];
}

$locations = lab_get_locations();
$ip = get_remote_ip();
$curr_loc = lab_get_locationid_for_ip( $ip );

foreach ( $locations as $loc ) {
  if ( $loc['id'] == $curr_loc ) {
    $loc['selected'] = true;
  }
}

$output['client_mac'] = $mac;
$output['locations'] = $locations;
$output['desc'] = $description;

if ( !empty($op) && !empty($mac) ) {  // force other values here too?
    $dn = $_SESSION['userid'];
    $set = ldap_quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
    $object = $set[0];
    $user = $object['uid'][0];
    labs_register_mac( $mac, $location, $description, $user, $ip );
}

output( $output, 'admin/register.tmpl' );
?>
