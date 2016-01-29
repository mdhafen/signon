<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/labs.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'reset_password' );

$output = array();
$template = 'admin/register.tmpl';
$mac = input( 'client_mac', INPUT_STR );
$location = input( 'loc', INPUT_PINT );
$description = input( 'desc', INPUT_HTML_NONE );
$drop_first = input( 'drop_first', INPUT_STR );
$mac_column = input( 'mac_column', INPUT_PINT );
$loc_column = input( 'loc_column', INPUT_PINT );
$desc_column = input( 'desc_column', INPUT_PINT );
$op = input( 'op', INPUT_STR );

if ( empty($mac) && !empty($_SESSION['client_mac']) ) {
    $mac = $_SESSION['client_mac'];
}

$locations = labs_get_locations();
$ip = get_remote_ip();
$curr_loc = lab_get_locationid_for_ip( $ip );

foreach ( $locations as &$loc ) {
  if ( $loc['id'] == $curr_loc ) {
    $loc['selected'] = true;
  }
}

$output['client_mac'] = $mac;
$output['locations'] = $locations;
$output['desc'] = $description;

if ( !empty($op) ) {  // force other values here too?
    $dn = $_SESSION['userid'];
    $set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
    $object = $set[0];
    $user = $object['uid'][0];
    if ( $op == 'Register' && !empty($mac) ) {
        $mac = labs_normalize_mac( $mac );
        labs_register_mac( $mac, $location, $description, $user, $ip );
        $output['success'] = true;
    } else if ( $op == 'Import' && !empty($mac_column) ) {
        if ( isset($_FILES['importfile']['error']) &&
             $_FILES['importfile']['error'] == UPLOAD_ERR_OK &&
             !empty($_FILES['importfile']['size']) ) {
            $in_file = $_FILES['importfile']['tmp_name'];
            $h = fopen( $in_file, 'r' );
            $registered = 0;
            while ( ! feof($h) ) {
                $row = fgetcsv($h);
                if ( !empty($drop_first) ) {
                    $drop_first = '';
                    continue;
                }

                $mac = labs_normalize_mac( $row[ $mac_column - 1 ] );
                $desc = empty($desc_column) && empty($row[ $desc_column - 1 ]) ? $description : $row[ $desc_column - 1 ];
                $loc = empty($loc_column) && empty($row[ $loc_column - 1 ]) ? $location : $row[ $loc_column - 1 ];
                if ( !empty($mac) ) {
                    labs_register_mac( $mac, $loc, $desc, $user, $ip );
                    $registered++;
                }
            }
            $output['registered'] = $registered;
            $output['success'] = true;
        }
        else {
            $output['error'] = 1;
            $output['err_msg'] = 'Import file seems empty';
        }
    }
    else if ( $op == 'Delete' && !empty($mac) ) {
        $mac = labs_normalize_mac( $mac );
        labs_unregister_mac( $mac );
        $output['deleted'] = true;
        $output['mac_list'] = labs_get_macs();
	$template = 'admin/mac_list.tmpl';
    }
    else if ( $op == 'List' ) {
        $output['mac_list'] = labs_get_macs();
	$template = 'admin/mac_list.tmpl';
    }
    else {
        $output['error'] = 1;
        switch ($op) {
            case 'Register':
            case 'Delete': $err_msg = 'No MAC Address given'; break;
            case 'Import': empty($output['registered']) ? $err_msg = 'No MAC Addresses found in file' : "";break;
        }
        if ( !empty($err_msg) ) {
            $output['err_msg'] = $err_msg;
        }
    }
}

output( $output, $template );
?>
