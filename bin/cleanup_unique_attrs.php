<?php
include_once( '../lib/data.phpm' );

$unique_attrs = array('sambaSID','employeeNumber','mail','uid',);
$matches = array();

$ldap = new LDAP_Wrapper();

$attrs = $ldap->quick_search( '(&(objectClass=sambaSamAccount)(!(employeeType=Guest)))', $unique_attrs );
foreach ( $attrs as $user ) {
    foreach ( $unique_attrs as $attr ) {
	if ( empty($user[$attr][0]) ) { continue; }
        if ( empty($matches[$attr]) ) {
            $matches[$attr] = array();
        }
        if ( empty($matches[$attr][ $user[$attr][0] ]) ) {
            $matches[$attr][ $user[$attr][0] ] = array('count'=>0,'dns'=>array());
        }
        $matches[$attr][ $user[$attr][0] ]['count']++;
        $matches[$attr][ $user[$attr][0] ]['dns'][ $user['dn'] ] = 0;
    }
}

$remove = array();
foreach ( $matches as $attr => $values ) {
    foreach ( $values as $key => $vals ) {
        if ( $vals['count'] <= 1 ) { continue; }
        $objects = array();
        $passwd_set = array();
        foreach ( $vals['dns'] as $dn => $val ) {
            $set = $ldap->quick_search( array( 'objectClass' => 'sambaSamAccount' ), array(), 0, $dn );
            if ( !empty($set[0]['userPassword']) ) {
                $passwd_set[ $set[0]['dn'] ] = 1;
            }
        }
        if ( !empty($passwd_set) ) {
            $vals['dns'] = array_diff_key( $vals['dns'], $passwd_set );
        }
        foreach ( $vals['dns'] as $dn => $val ) {
            $remove[$dn] = 1;
        }
    }
}
foreach ( $remove as $dn => $val ) {
    print "Removing $dn\n";
    $ldap->do_delete($dn);
}
?>
