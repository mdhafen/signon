<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

$output = array();
$dn = input( 'dn', INPUT_STR );

$ldap = new LDAP_Wrapper();
authorize( 'set_password' );
$can_edit = authorized('manage_objects');

if ( empty($dn) ) {
    $query = input( 'query', INPUT_STR );
    if ( !empty($query) ) {
        $search = ldap_escape( $query, '*', LDAP_ESCAPE_FILTER );
        $set = $ldap->quick_search( "(|(uid=$search)(employeeNumber=$search))", array() );
        if ( empty($set) ) {
            if ( strpos($query,'@') !== False ) {
                if ( strripos($query,'@'.$GOOGLE_DOMAIN) === False ) {
                    $output['empty_result'] = 1;
                    output( $output, 'admin/sync_check_search.tmpl' );
                    exit;
                }
            }
            else {
                $query .= '@'.$GOOGLE_DOMAIN;
            }
            $source = google_user_hash_for_ldap( get_user_google( $query ) );
            if ( empty($source) ) {
                $output['empty_result'] = 1;
                output( $output, 'admin/sync_check_search.tmpl' );
                exit;
            }
            $source['_source'] = 'Google';
            $objects[] = $source;
            $output['mail'] = $source['mail'];
            $can_edit = ( $can_edit ?: ldap_can_edit( $ldap, $source['dn'] ) );
            $parentdn = $ldap->dn_get_parent( $source['dn'] );
            if ( $parentdn == $ldap->config['base'] ) {
	            $parentdn = '';
            }
        }
        else {
            $dn = $set[0]['dn'];
            $set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
        }
    }
    else {
        output( $output, 'admin/sync_check_search.tmpl' );
        exit;
    }
}
else {
    $set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
}

if ( !empty($set) ) {
    $set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
    $object = $set[0];
    $objectdn = $object['dn'];
    unset( $object['dn'] );
    $is_person = is_person( $object );
    $can_edit = ( $can_edit ?: ldap_can_edit( $ldap, $objectdn ) );

    $fatal_errors = array('CHECK_OBJECT_NOT_USER_OBJECT','CHECK_OBJECT_NO_UID_EMPLOYEENUMBER','CHECK_OBJECT_NO_GOOGLE');

    if ( ! $is_person ) {
        error(['CHECK_OBJECT_NOT_USER_OBJECT']);
    }

    ksort( $object, SORT_STRING | SORT_FLAG_CASE );

    $parentdn = $ldap->dn_get_parent( $objectdn );
    if ( $parentdn == $ldap->config['base'] ) {
	    $parentdn = '';
    }

    list($errors,$objects,$all_keys,$diff_keys) = check_google_sync($ldap,$object);
    if ( array_intersect($errors,array('CHECK_OBJECT_LDAP_DUPLICATES')) ) {
        $output['ldap_duplicates'] = 1;
    }
    $fatals = array_intersect($errors,$fatal_errors);
    if ( $fatals ) {
        error($fatals);
    }
}

$diff = array( 0 => array('_Source'), 1 => array('_DN') );
foreach ( $objects as $obj ) {
    switch ($obj['_source']) {
        case 'Google':
            $diff[0][] = 'Google';
            $diff[1][] = $obj['dn'];
        break;
        case 'LDAP':
            $diff[0][] = 'LDAP';
            $diff[1][] = $obj['dn'];
        break;
    }
}

foreach ( $diff_keys as $key ) {
    $line = array($key);
    foreach ( $objects as $obj ) {
        $val = '';
        switch ($obj['_source']) {
            case 'Google':
                $val = !empty($obj[$key]) ? $obj[$key] : "";
                $line[] = $val;
            break;
            case 'LDAP':
                $val = implode( ' | ', (!empty($obj[$key]) ? $obj[$key] : array() ) );
                $line[] = $val;
                break;
        }
    }
    $diff[] = $line;
}

$output = array_merge( $output, array(
	'object_dn' => $objectdn,
	'object' => $object,
	'can_edit' => $can_edit,
	'diff' => $diff,
) );

output( $output, 'admin/sync_check.tmpl' );

?>
