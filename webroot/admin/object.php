<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

global $GOOGLE_DOMAIN;

$ldap = new LDAP_Wrapper();
authorize( 'set_password' );

$dn = input( 'dn', INPUT_STR );

$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
$object = $set[0];
$objectdn = $object['dn'];
unset( $object['dn'] );
$groups = array();
$user_lock = array();
$default_passwd = '';
$can_edit = authorized('manage_objects');

$is_person = is_person( $object );
$can_send_token = false;
$reset_token = '';
if ( $is_person ) {
    $groups = get_groups( $ldap, $objectdn );
    $user_lock = get_lock_status( $object['uid'][0] );
    $default_passwd = get_default_password( $object['uid'][0] );
    $can_edit = ( $can_edit ?: ldap_can_edit( $ldap, $objectdn ) );
    $can_send_token = ( !empty($object['labeledURI'][0]) && !empty($object['mail']) && strripos($object['mail'][0],'@'.$GOOGLE_DOMAIN) !== false && !empty($object['employeeType']) && in_array($object['employeeType'][0], array('Staff','Other')) );
    $reset_token = get_password_reset_token(userid:$object['uid'][0]);
}

$is_guest = 0;
$is_guest_expired = 0;
$is_guest_send_notice = 0;
if ( strripos( $objectdn, ",ou=Guest,".$ldap->config['base'] ) !== false ) {
    $is_guest = 1;
    $sig = get_guest_signature( $object['uid'][0] );
    if ( empty($sig) || $sig['aup_expire'] < $sig['now'] ) {
        $is_guest_expired = 1;
        if ( $sig['send_notice'] < $sig['now'] && ( empty($sig['aup_sent']) || ( $sig['aup_signed'] && $sig['aup_sent'] < $row['aup_signed'] ) ) ) {
            $is_guest_send_notice = 1;
        }
    }
}

ksort( $object, SORT_STRING | SORT_FLAG_CASE );

$attr_changes = get_attr_changes( $objectdn );

$parentdn = $ldap->dn_get_parent( $objectdn );
if ( $parentdn == $ldap->config['base'] ) {
	$parentdn = '';
}

$children = $ldap->quick_search( '(objectClass=*)', array(), 1, $dn );
usort( $children, 'sorter' );

$output = array(
	'object_dn' => $objectdn,
	'object' => $object,
	'is_person' => $is_person,
	'is_guest' => $is_guest,
	'is_guest_expired' => $is_guest_expired,
	'is_guest_send_notice' => $is_guest_send_notice,
	'parentdn' => $parentdn,
	'user_lock' => $user_lock,
	'default_passwd' => $default_passwd,
	'password_reset_token' => $reset_token,
	'attr_changes' => $attr_changes,
	'children' => $children,
	'can_edit' => $can_edit,
	'can_send_token' => $can_send_token,
	'can_lock' => ( !empty($object['employeeType']) && ( ( $object['employeeType'][0] == 'Student' && authorized('lock_student') ) || ( $object['employeeType'][0] == 'Staff' && authorized('lock_staff') ) ) ),
	'can_set_password' => authorized('set_password') || ($objectdn == $_SESSION['userid']),
	'can_see_password' => authorized('reset_password') || ($objectdn == $_SESSION['userid']),
);

output( $output, 'admin/object.tmpl' );

function sorter( $a, $b ) {
	$aa = substr( $a['dn'], 0, strpos($a['dn'],'=') );
	$ba = substr( $b['dn'], 0, strpos($b['dn'],'=') );

	return strcasecmp( $a[$aa][0], $b[$ba][0] );
}

?>
