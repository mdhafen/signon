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
$category = input( 'cat', INPUT_HTML_NONE );
$drop_first = input( 'drop_first', INPUT_STR );
$mac_column = input( 'mac_column', INPUT_PINT );
$loc_column = input( 'loc_column', INPUT_PINT );
$desc_column = input( 'desc_column', INPUT_PINT );
$cat_column = input( 'cat_column', INPUT_PINT );
$op = input( 'op', INPUT_STR );
$search_term = input( 'search_term', INPUT_HTML_NONE );

if ( empty($mac) && !empty($_SESSION['client_mac']) ) {
    $mac = $_SESSION['client_mac'];
}

$locations = labs_get_locations();
$ip = get_remote_ip();
$curr_loc = lab_get_locationid_for_ip( $ip );
$selected_loc = empty($location) ? $curr_loc : $location;

foreach ( $locations as &$loc ) {
  if ( $loc['id'] == $selected_loc ) {
    $loc['selected'] = true;
  }
}

$output['client_mac'] = $mac;
$output['locations'] = $locations;
$output['desc'] = $description;
$output['category'] = $category;

if ( !empty($op) ) {  // force other values here too?
    $dn = $_SESSION['userid'];
    $set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
    $object = $set[0];
    $user = $object['uid'][0];
    if ( ! in_array( $location, array_column($locations,'id') ) ) { $location = 0; }

/*
 * vlans:
 * 10(Lan), 20(Labs), 50(Facilities), 70(Phone), 99(Guest)
 */
    switch ( $category ) {
        case 'Lan' :
        case 'Labs' :
        case 'Facilities' :
        case 'Phone' :
        case 'Guest' :
            break;
        default : $category = '';
    }

    if ( $op == 'Register' ) {
        if ( !empty($mac) && !empty($location) && !empty($category) ) {
            $mac = labs_normalize_mac( $mac );
            $error = labs_register_mac( $mac, $location, $description, $category, $user, $ip );
            if ( !$error ) {
                $output['success'] = true;
            }
            else {
                $output['error'] = 1;
                $output['err_msg'] = "Database Error: $error";
            }
        }
        else {
            $output['error'] = 1;
            $output['err_msg'] = "MAC Address ($mac) or Location invalid";
        }
    } else if ( $op == 'Import' && !empty($mac_column) ) {
        $line_number = 0;
        if ( isset($_FILES['importfile']['error']) &&
             $_FILES['importfile']['error'] == UPLOAD_ERR_OK &&
             !empty($_FILES['importfile']['size']) ) {
            $in_file = $_FILES['importfile']['tmp_name'];
            $h = fopen( $in_file, 'r' );
            $registered = 0;
            while ( ! feof($h) ) {
                $row = fgetcsv($h);
                if ( empty($row) ) {
                    continue;
                }
                $line_number++;
                if ( !empty($drop_first) ) {
                    $drop_first = '';
                    continue;
                }

                $mac = labs_normalize_mac( $row[ $mac_column - 1 ] );
                $desc = empty($desc_column) && empty($row[ $desc_column - 1 ]) ? $description : trim($row[ $desc_column - 1 ]);
                $loc = empty($loc_column) && empty($row[ $loc_column - 1 ]) ? $location : trim($row[ $loc_column - 1 ]);
                $cat = empty($cat_column) && empty($row[ $cat_column - 1 ]) ? $category : trim($row[ $cat_column - 1 ]);
                if ( ! in_array( $loc, array_column($locations,'id') ) ) { $loc = 0; }
                switch ( $cat ) {
                    case 'Lan' :
                    case 'Labs' :
                    case 'Facilities' :
                    case 'Phone' :
                    case 'Guest' :
                        break;
                    default : $cat = 'Labs';
                }
                if ( !empty($mac) && !empty($loc) && !empty($cat) ) {
                    labs_register_mac( $mac, $loc, $desc, $cat, $user, $ip );
                    $registered++;
                }
                else {
                    $output['error'] = 1;
                    $output['err_msg'] = "Import file contains lines with invalid MAC Address ($mac) or Location.  Line $line_number: ". implode( ',', $row );
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
        $output['mac_list'] = labs_get_macs($search_term);
	$template = 'admin/mac_list.tmpl';
    }
    else if ( $op == 'List' ) {
        $output['mac_list'] = labs_get_macs($search_term);
	$template = 'admin/mac_list.tmpl';
    }
    else {
        $output['error'] = 1;
        switch ($op) {
            case 'Delete': $err_msg = 'No MAC Address given'; break;
            case 'Import': $err_msg = 'No column selected for MAC Addresses'; break;
        }
        if ( !empty($err_msg) ) {
            $output['err_msg'] = $err_msg;
        }
    }
}

output( $output, $template );
?>
