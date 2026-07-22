<?php
include_once( '../lib/data.phpm' );

$ldap = new LDAP_Wrapper();
$ous = $ldap->quick_search( '(objectClass=organizationalUnit)', array() );
$dns = array();
uasort( $ous, function( $a, $b ) { return mb_strlen($b['dn']) <=> mb_strlen($a['dn']); } );

foreach ( $ous as $ou ) {
    $dn = $ou['dn'];
    if ( stripos($ou['ou'][0],'macosxodconfig') !== false ) {
        continue;
    }
    if ( stripos($ou['ou'][0],'ou=wcsd,ou=technology') !== false ) {
        continue;
    }
    $dns[$dn] = 0;
    $children = $ldap->quick_search( '(objectClass=*)', array(), 1, $dn );
    foreach ( $children as $child ) {
        if ( ! array_key_exists($child['dn'],$dns) ||  $dns[$child['dn']] > 0 ) {
            $dns[$dn]++;
        }
    }
}

foreach ( $dns as $dn => $children ) {
    if ( $children > 0 ) {
        continue;
    }

    print "$dn";
    $result = $ldap->do_delete( $dn );
    if ( $result ) {
        print " (Error:$result)";
    }
    print "\n";
}

?>
