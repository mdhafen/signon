<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

authenticate();

$return = input( 'return', INPUT_STR );

if ( ! ( authorized('reset_password') || ( !empty($_SESSION['loggedin_user']) && strcasecmp($dn,$_SESSION['loggedin_user']['userid']) == 0 ) ) ) {
	if ( empty($return) ) {
		output( '<?xml version ="1.0"?><result><state>error</state><message>ACCESS_DENIED</message></result>', '', $xml=1 );
	} else {
		error(array('ACCESS_DENIED'));
	}
	exit;
}

$ldap = new LDAP_Wrapper();
global $GOOGLE_DOMAIN;

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

if ( ! authorized('manage_objects') && ! ldap_can_edit( $ldap, $objectdn ) ) {
    if ( empty($return) ) {
        output( '<?xml version ="1.0"?><result><state>error</state><message>ACCESS_DENIED</message></result>', '', $xml=1 );
    } else {
        error(array('ACCESS_DENIED'));
    }
    exit;
}

$set = $ldap->quick_search( '(&(objectClass=posixGroup)(cn=vpn2_access))', array() );
$group = $set[0];
$groupdn = $group['dn'];
unset( $group['dn'] );

$output = '<?xml version="1.0"?>
<confine>';

$input = input( 'toggle', INPUT_STR );
( $class = input( 'class', INPUT_STR ) ) || ( $class = 'Confinement' );

if ( ! empty($input) ) {
    switch ($class) {
    case 'Confinement':
    case 'Banned':
        $state = $object['businessCategory'][0];
        if ( $input == 'off' ) {
            if ( $object['businessCategory'][0] == $class ) {
                $state = !empty($object['employeeType'][0])?$object['employeeType'][0]:'Other';
            }
        } else {
            if ( $class == 'Confinement' || $class == 'Banned' ) {
                $state = $class;
            }
        }

        if ( empty($object['businessCategory'][0]) || $state != $object['businessCategory'][0] ) {
            $results = $ldap->do_modify( $objectdn, array('businessCategory'=>$state) );
        }
        break;

    case 'VPN':
        if ( $input == 'off' ) {
            if ( array_search($object['uid'][0],$group['memberUid']) !== false ) {
                $results = $ldap->do_attr_del( $groupdn, array('memberUid'=>$object['uid'][0]) );
            }
        }
        else if ( array_search($object['uid'][0],$group['memberUid']) === false ) {
            $results = $ldap->do_attr_add( $groupdn, array('memberUid'=>$object['uid'][0]) );
        }
        break;

    case 'Lock':
        if ( ( $object['employeeType'][0] == 'Student' && !authorized('lock_student') ) || ( $object['employeeType'][0] == 'Staff' && !authorized('lock_staff') ) ) {
            if ( empty($return) ) {
                output( '<?xml version ="1.0"?><result><state>error</state><message>ACCESS_DENIED</message></result>', '', $xml=1 );
            } else {
                error(array('ACCESS_DENIED'));
            }
            exit;
        }

        if ( $input == 'off' ) {
            $results = unlock_user($ldap,$objectdn);
        }
        else {
            $password = create_password();
            lock_user( $ldap, $objectdn, $password );
            if ( !empty($object['employeeType'][0]) && strripos($object['mail'][0],'@'.$GOOGLE_DOMAIN) !== False ) {
                google_set_password( $object['mail'][0], $password );
            }
            $results = set_password( $ldap, $objectdn, $password );
        }
        break;
    }

    if ( $results ) {
        $output .= "\n<result>Success</result>";
    }
    else {
        if ( empty($return) ) {
            output( '<?xml version ="1.0"?><result><state>error</state><message>'. $ldap->get_error() .'</message></result>', '', $xml=1 );
        } else {
            redirect('admin/object.php?dn='.urlencode($dn) );
        }
        exit;
    }
}

$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
$object = $set[0];
$state = !empty($object['businessCategory'])?$object['businessCategory'][0]:"";
$output .= "\n<state>$state</state>\n";

$output .= '</confine>';

if ( empty($return) ) {
    output( $output, '', $xml=1 );
} else {
    redirect('admin/object.php?dn='.urlencode($dn) );
}
?>
