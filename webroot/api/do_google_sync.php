<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

if ( ! authorized('set_password') && ! authenticate_api_client() ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>ACCESS_DENIED</flag></result>', '', $xml=1 );
    exit;
}

global $GOOGLE_DOMAIN;
$ldap = new LDAP_Wrapper();
$output = '<?xml version ="1.0"?><result>';
$object = array();
$source = array();
$query = input( 'query', INPUT_STR );

$search = ldap_escape( $query, '*', LDAP_ESCAPE_FILTER );
$set = $ldap->quick_search( "(|(uid=$search)(employeeNumber=$search))", array() );
if ( empty($set) ) {
    if ( strpos($query,'@') !== False ) {
        if ( strripos($query,'@'.$GOOGLE_DOMAIN) === False ) {
            output( '<?xml version ="1.0"?><result><state>error</state><flag>BAD_QUERY</flag></result>', '', $xml=1 );
            exit;
        }
    }
    else {
        $query .= '@'.$GOOGLE_DOMAIN;
    }
    $source = get_user_google( $query );
    if ( empty($source) ) {
        output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_FOUND</flag></result>', '', $xml=1 );
        exit;
    }
    $source = google_user_hash_for_ldap( $source );
}
if ( count($set) > 1 ) {
    output( '<?xml version ="1.0"?><result><state>error</state><flag>LDAP_DUPLICATES</flag></result>', '', $xml=1 );
    exit;
}
if ( !empty($set) ) {
    $object = $set[0];
    $is_person = is_person($object);
    if ( ! $is_person ) {
        output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_USER_OBJECT</flag></result>', '', $xml=1 );
        exit;
    }

    list($errors,$objects,$all_keys,$diff_keys) = check_google_sync($ldap,$object);
    if ( !empty($errors) ) {
        $result = '<?xml version ="1.0"?><result><state>error</state>';
        foreach ( $errors as $err ) {
            $result .= "<flag>$err</flag>";
        }
        $result .= '</result>';
        output( $result, '', $xml=1 );
        exit;
    }
    if ( empty($diff_keys) ) {
        output( '<?xml version ="1.0"?><result><state>success</state><flag>NO_CHANGES</flag></result>', '', $xml=1 );
        exit;
    }
    unset( $object );
    unset( $source );
    foreach ( $objects as &$obj ) {
        if ( $obj['_source'] == 'LDAP' ) {
            $object = $obj;
        }
        if ( $obj['_source'] == 'Google' ) {
            $source = $obj;
        }
        if ( !empty($object) && !empty($source) ) {
            break;
        }
    }
    if ( empty($object) ) {
        output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_FOUND_LDAP</flag></result>', '', $xml=1 );
        exit;
    }
    if ( empty($source) ) {
        output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_FOUND_GOOGLE</flag></result>', '', $xml=1 );
        exit;
    }
    unset($object['_source']);
    unset($source['_source']);
}

list( $mods, $move, $add, $passwd ) = do_google_sync( $ldap, $object, $source );
if ( !empty($mods) ) {
    $results = '<changed>';
    foreach ( $mods as $attr ) {
        $results .= "<attribute>$attr</attribute>";
    }
    $results .= '</changed>';
}
if ( $move ) {
    $results .= '<flag>MOVED</flag>';
}
if ( $add ) {
    $results .= '<flag>CREATED</flag>';
}
if ( $passwd ) {
    $results .= '<flag>PASSWORD_SET</flag>';
}

$output .= $results;
$output .= '<state>success</state></result>';
output( $output, '', $xml=1 );

?>
