<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/labs.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'reset_password' );

$template = 'admin/register.tmpl';
$mac = input( 'client_mac', INPUT_STR );
$search_term = input( 'search_term', INPUT_HTML_NONE );
$op = input( 'op', INPUT_STR );

$location = input( 'loc', INPUT_PINT );
$description = input( 'desc', INPUT_HTML_NONE );
$labs_category = input( 'labs_cat', INPUT_HTML_NONE );
$fields_category = input( 'fields_cat', INPUT_HTML_NONE );
$iot_category = input( 'iot_cat', INPUT_HTML_NONE );

$drop_first = input( 'drop_first', INPUT_STR );
$mac_column = input( 'mac_column', INPUT_PINT );
$loc_column = input( 'loc_column', INPUT_PINT );
$desc_column = input( 'desc_column', INPUT_PINT );
$lab_cat_column = input( 'lab_cat_column', INPUT_PINT );
$field_cat_column = input( 'field_cat_column', INPUT_PINT );
$iot_cat_column = input( 'iot_cat_column', INPUT_PINT );

if ( empty($mac) && !empty($_SESSION['client_mac']) ) {
    $mac = $_SESSION['client_mac'];
}

$registration = labs_get_registration($mac);
$reg_loc = $registration['device_home'] ?? "";

$locations = labs_get_locations();
$ip = get_remote_ip();
$curr_loc = lab_get_locationid_for_ip( $ip );
$selected_loc = !empty($reg_loc) ? $reg_loc : !empty($location) ? $location : $curr_loc;

foreach ( $locations as &$loc ) {
  if ( $loc['id'] == $selected_loc ) {
    $loc['selected'] = true;
  }
}

$output = array(
    'desc' => $description ?? "",
    'client_mac' => $mac,
    'locations' => $locations,
    'search_term' => $search_term,
    'labs_cat' => $labs_category,
    'fields_cat' => $fields_category,
    'iot_cat' => $iot_category,
);

if ( !empty($op) ) {  // force other values here too?
    $dn = $_SESSION['userid'];
    $set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
    $object = $set[0];
    $user = $object['uid'][0];
    if ( ! in_array( $location, array_column($locations,'id') ) ) { $location = 0; }

    list($labs_category,$fields_category,$iot_category) = labs_normalize_categories($labs_category,$fields_category,$iot_category);

    if ( $op == 'List' ) {
        $output['mac_list'] = labs_get_macs($search_term);
        $template = 'admin/mac_list.tmpl';
    } else if ( $op == 'Edit' ) {
        $output = array(
            'locations' => $locations,
            'search_term' => $search_term,
            'client_mac' => $mac,
            'desc' => $registration['submitted_desc'],
            'labs_cat' => $registration['labs_category'],
            'fields_cat' => $registration['fields_category'],
            'iot_cat' => $registration['iot_category'],
        );
        $template = 'admin/registration_edit.tmpl';
    } else if ( $op == 'Save' ) {
        $template = 'admin/registration_edit.tmpl';
        if ( !empty($mac) && !empty($location) &&
             ( !empty($labs_category) || !empty($fields_category) || !empty($iot_category) ) ) {
            $mac = labs_normalize_mac( $mac );
            $error = labs_update_mac( $mac, $location, $description, $labs_category, $fields_category, $iot_category, $user, $ip );
            if ( !$error ) {
                $output['edited'] = true;
                if ( !empty($search_term) ) {
                    $output['mac_list'] = labs_get_macs($search_term);
                }
                $template = 'admin/mac_list.tmpl';
            }
            else {
                $output['error'] = 1;
                $output['err_msg'] = "Database Error: $error";
            }
        }
        else {
            $output['error'] = 1;
            $output['err_msg'] = "MAC Address ($mac), Location, or Category invalid";
        }
    } else if ( $op == 'Register' ) {
        if ( !empty($mac) && !empty($location) &&
             ( !empty($labs_category) || !empty($fields_category) || !empty($iot_category) ) ) {
            $mac = labs_normalize_mac( $mac );
            $error = labs_register_mac( $mac, $location, $description, $labs_category, $fields_category, $iot_category, $user, $ip );
            if ( !$error ) {
                $output['success'] = true;
                unset( $output['client_mac'] );
            }
            else {
                $output['error'] = 1;
                $output['err_msg'] = "Database Error: $error";
            }
        }
        else {
            $output['error'] = 1;
            $output['err_msg'] = "MAC Address ($mac), Location, or Category invalid";
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
                $lab_cat = empty($lab_cat_column) && empty($row[ $lab_cat_column - 1 ]) ? $labs_category : trim($row[ $lab_cat_column - 1 ]);
                $field_cat = empty($field_cat_column) && empty($row[ $field_cat_column - 1 ]) ? $fields_category : trim($row[ $field_cat_column - 1 ]);
                $iot_cat = empty($iot_cat_column) && empty($row[ $iot_cat_column - 1 ]) ? $iot_category : trim($row[ $iot_cat_column - 1 ]);
                if ( ! in_array( $loc, array_column($locations,'id') ) ) { $loc = 0; }
                list($lab_cat,$field_cat,$iot_cat) = labs_normalize_categories($lab_cat,$field_cat,$iot_cat);

                if ( !empty($mac) && !empty($loc) && ( !empty($lab_cat) || !empty($field_cat) || !empty($iot_cat) ) ) {
                    $error = labs_register_mac( $mac, $loc, $desc, $lab_cat, $field_cat, $iot_cat, $user, $ip );
                    if ( !$error ) {
                        $registered++;
                    }
                    else {
                        $output['error'] = 1;
                        $output['err_msg'] = "Database Error: $error";
                    }
                }
                else {
                    $output['error'] = 1;
                    $output['err_msg'] = "Import file contains a line with an invalid value.  Line $line_number: ". implode( ',', $row );
                }
            }
            $output['registered'] = $registered;
            $output['success'] = true;
        }
        else {
            $output['error'] = 1;
            $output['err_msg'] = 'Import file seems empty';
        }
    } else if ( $op == 'Delete' && !empty($mac) ) {
        $mac = labs_normalize_mac( $mac );
        labs_unregister_mac( $mac );
        $output['deleted'] = true;
        if ( !empty($search_term) ) {
            $output['mac_list'] = labs_get_macs($search_term);
        }
        $template = 'admin/mac_list.tmpl';
    } else {
        $output['error'] = 1;
        switch ($op) {
            case 'Delete': $err_msg = 'No MAC Address given'; break;
            case 'Import': $err_msg = 'Missing required field'; break;
        }
        if ( !empty($err_msg) ) {
            $output['err_msg'] = $err_msg;
        }
    }
}

output( $output, $template );
?>
