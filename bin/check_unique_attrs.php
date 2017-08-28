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
            $matches[$attr][ $user[$attr][0] ] = 0;
        }
        $matches[$attr][ $user[$attr][0] ]++;
    }
}

foreach ( $matches as $attr => $values ) {
    foreach ( $values as $key => $value ) {
        if ( $value <= 1 ) { continue; }
        print "$attr $key: $value\n";
    }
}
?>
