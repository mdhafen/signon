<?php
include_once( '../lib/data.phpm' );

$unique_attrs = array('sambaSID','employeeNumber','mail','uid',);
$matches = array();

$ldap = new LDAP_Wrapper();

$attrs = $ldap->quick_search( '(objectClass=sambaSamAccount)', $unique_attrs );
foreach ( $attrs as $user ) {
    foreach ( $unique_attrs as $attr ) {
        $matches[$attr][ $user[$attr][0] ]++;
    }
}

foreach ( $matches as $attr ) {
    foreach ( $attr as $value ) {
        if ( $value <= 1 ) { continue; }
        print "$attr: $value\n";
    }
}
?>
