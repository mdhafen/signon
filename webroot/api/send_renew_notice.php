<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();

$uid = input( 'uid', INPUT_STR );

$set = $ldap->quick_search( array( 'uid' => $uid ) );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );

$result = 'NOOP';
$error = '';

if ( !empty($object['uid']) ) {
    $row = get_guest_signature($object['uid'][0]);
    $send = 0;

    if ( empty($row) ) {
        $send = 1;
    }
    else if ( $row['aup_expire'] < $row['now'] ) {
        $send = 0;
    }
    else if ( $row['send_notice'] < $row['now'] ) {
        if ( empty($row['aup_sent']) || ( $row['aup_signed'] && $row['aup_sent'] < $row['aup_signed'] ) ) {
            $send = 1;
        }
    }

    if ( $send ) {
        // FIXME enable after signon/renew.php page is built
        //$result = sms_send_renew_notice( $object['uid'][0] );
        $result = 1;
        if ( $result === 1 ) {
            $result = "Success";
        }
        else {
            $error = '<message>'. $result .'</message>';
            $result = "Error";
        }
    }
}

$output = '<?xml version="1.0"?><result><state>'. $result .'</state>'. $error .'</result>';

output( $output, '', $xml=1 );
?>
