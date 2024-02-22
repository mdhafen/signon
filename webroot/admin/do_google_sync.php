<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

$output = array();
$dn = input( 'dn', INPUT_STR );
$mail = input( 'mail', INPUT_EMAIL );

$ldap = new LDAP_Wrapper();
authorize( 'set_password' );
$can_edit = authorized('manage_objects');
$errors = array();
$object;

if ( empty($dn) && empty($mail) ) {
    error(['SYNC_OBJECT_NO_INPUT']);
}
if ( !empty( input( 'ldap_duplicates', INPUT_STR ) ) ) {
    $primary = input( 'primary', INPUT_STR );
    $dns = input( 'dns[]', INPUT_STR );
    if ( empty($primary) ) {
        error(['SYNC_OBJECT_LDAP_DUP_NO_PRIMARY']);
    }
    $dn = $primary;
    foreach ($dns as $del) {
        if ( $del == $primary ) { continue; }
        $set = $ldap->quick_search( '(objectClass=*)', array(), 0, $del );
        if ( empty($set) ) {
            $errors[] = 'SYNC_OBJECT_DUP_NOT_FOUND';
            continue;
        }
        $object = $set[0];
        if ( $can_edit || ldap_can_edit( $ldap, $object['dn'] ) ) {
            remove_from_groups( $ldap, $object['dn'] );
            $ldap->do_delete( $object['dn'] );
        }
        else {
            $errors[] = 'SYNC_OBJECT_DUP_CANT_EDIT';
        }
    }
}
if ( !empty($dn) ) {
    $set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
}
if ( empty($set) ) {
    if ( empty($mail) ) {
        error(['SYNC_OBJECT_LDAP_NOT_FOUND']);
    }
    $source = google_user_hash_for_ldap( get_user_google( $mail ) );
    if ( empty($source) ) {
        error(['SYNC_OBJECT_NO_GOOGLE']);
    }
    $objectdn = $source['dn'];
    $is_person = 1;
    $can_edit = ( $can_edit ?: ldap_can_edit( $ldap, $objectdn ) );
}
else {
    $object = $set[0];
    $objectdn = $object['dn'];
    $is_person = is_person( $object );
    $can_edit = ( $can_edit ?: ldap_can_edit( $ldap, $objectdn ) );

    if ( ! $is_person ) {
        error(['SYNC_OBJECT_NOT_USER_OBJECT']);
    }
    if ( ! $can_edit ) {
        error(['SYNC_OBJECT_CANT_EDIT']);
    }

    if ( empty($object['mail']) ) {
        error(['SYNC_OBJECT_NO_MAIL']);
    }
    $source = google_user_hash_for_ldap( get_user_google( $object['mail'][0] ) );
    if ( empty($source) ) {
        error(['SYNC_OBJECT_NO_GOOGLE']);
    }
}

$parentdn = $ldap->dn_get_parent( $objectdn );
if ( $parentdn == $ldap->config['base'] ) {
	$parentdn = '';
}

list( $mods, $move, $add, $passwd ) = do_google_sync( $ldap, $object, $source );

$output = array_merge( $output, array(
    'errors' => $errors,
	'object_dn' => $objectdn,
	'can_edit' => $can_edit,
    'mods' => $mods,
    'move' => $move,
    'add' => $add,
    'password_set' => $passwd,
) );

output( $output, 'admin/do_google_sync.tmpl' );

?>
