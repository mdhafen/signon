<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'reset_password' );

$attr = input( 'attrib', INPUT_STR );
$query = input( 'query', INPUT_STR );
$results = array();
$attrs = array(
	       'uid' => 'Username',
	       'sn' => 'Last Name',
	       'givenName' => 'First Name',
	       'member' => 'Group memberships by Username',
	       'l' => 'Building Abbreviation',
);
if ( !empty($attr) && empty($attrs[$attr]) ) {
    error( array('BAD_ATTRIBUTE') );
}

$output = array( 'attributes' => $attrs );

if ( !empty($attrs[$attr]) && !empty($query) ) {
  $query = ldap_escape($query,'',LDAP_ESCAPE_FILTER) .'*';
  if ( strcasecmp( $attr, 'member' ) == 0 ) {
    $set = $ldap->quick_search( array( 'uid' => $query ), array() );
    if ( !empty($set) ) {
      $query = $set[0]['dn'];
    }
    else {
      $query = '';
    }
  }
  if ( !empty($query) ) {
    $set = $ldap->quick_search( array( $attr => $query ), array() );
    foreach ( $set as $user ) {
      $results[] = $user['dn'];
    }
  }
  if ( ! empty($results) ) {
    sort( $results );
    $output['search_results'] = $results;
  }
  else {
    $output['no_results'] = 1;
  }
}

output( $output, 'admin/search.tmpl' );
?>
