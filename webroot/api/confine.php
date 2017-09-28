<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

authenticate();

if ( ! ( authorized('reset_password') || ( !empty($_SESSION['loggedin_user']) && strcasecmp($dn,$_SESSION['loggedin_user']['userid']) == 0 ) ) ) {
	output( '<?xml version ="1.0"?><error>ACCESS_DENIED</error>', '', $xml=1 );
	exit;
}

$ldap = new LDAP_Wrapper();

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$output = '<?xml version="1.0"?>
<confine>';

$input = input( 'toggle', INPUT_STR );
( $class = input( 'class', INPUT_STR ) ) || ( $class = 'Confinement' );
$return = input( 'return', INPUT_STR );

if ( ! empty($input) ) {
    if ( $input == 'off' ) {
	if ( $class == 'Confinement' && $object['businessCategory'][0] == 'Confinement' ) {
          $state = !empty($object['employeeType'][0])?$object['employeeType'][0]:'Other';
        } else if ( $class == 'Banned' && $object['businessCategory'][0] == 'Banned' ) {
          $state = !empty($object['employeeType'][0])?$object['employeeType'][0]:'Other';
        }
    } else {
	if ( $class == 'Confinement' ) {
            $state = 'Confinement';
        } else if ( $class == 'Banned' ) {
            $state = 'Banned';
        }
    }

    if ( empty($object['businessCategory'][0]) || $state != $object['businessCategory'][0] ) {
        $results = $ldap->do_modify( $objectdn, array('businessCategory'=>$state) );
        if ( $results ) {
            $output .= "\n<result>Success</result>";
        }
        else {
            if ( empty($return) ) {
                output( '<?xml version ="1.0"?><error>'. $ldap->get_error() .'</error>', '', $xml=1 );
            } else {
                redirect('admin/object.php?dn='.urlencode($dn) );
            }
            exit;
        }
    }
}

$set = $ldap->quick_search( array( 'objectClass' => '*' ), array(), 0, $dn );
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
