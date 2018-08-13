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
            $matches[$attr][ $user[$attr][0] ] = array('count'=>0,'uids'=>array());
        }
        $matches[$attr][ $user[$attr][0] ]['count']++;
        $matches[$attr][ $user[$attr][0] ]['uids'][ $user['uid'][0] ] = 0;
    }
}

foreach ( $matches as $attr => $values ) {
    foreach ( $values as $key => $values ) {
        if ( $values['count'] <= 1 ) { continue; }
        print "$attr $key: ". $values['count'] ." ". implode(',',array_keys($values['uids'])) ."\n";
    }
}
?>
