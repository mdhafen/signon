<?php
include_once( '../lib/data.phpm' );

$ldap = new LDAP_Wrapper();
$ous = $ldap->quick_search( '(objectClass=organizationalUnit)', array() );
$dns = array();

foreach ( $ous as $ou ) {
    $dn = $ou['dn'];
    if ( stripos($ou['ou'][0],'macosxodconfig') !== false ) {
        continue;
    }
    if ( stripos($ou['ou'][0],'ou=wcsd,ou=technology') !== false {
        continue;
    }
    $children = $ldap->quick_search( '(objectClass=*)', array(), 1, $dn );
    if ( ! count($children) ) {
        $dns[] = $dn;
    }
}

foreach ( $dns as $dn ) {
    print "$dn";
    $result = $ldap->do_delete( $dn );
    if ( ! $result ) {
        print $ldap->get_error();
    }
    print "\n";
}

?>
