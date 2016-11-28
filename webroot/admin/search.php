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
	       'o' => 'Building Abbreviation',
);
if ( !empty($attr) && empty($attrs[$attr]) ) {
    error( array('BAD_ATTRIBUTE') );
}

$output = array( 'attributes' => $attrs );

if ( !empty($attrs[$attr]) && !empty($query) ) {
  $query = ldap_escape($query,'',LDAP_ESCAPE_FILTER) .'*';
  $filter = "($attr=$query)";
  if ( strcasecmp( $attr, 'member' ) == 0 ) {
    $set = $ldap->quick_search( array( 'uid' => $query ), array() );
    if ( !empty($set) ) {
      $filter = "(|(memberUid=$query)(member=".$set[0]['dn']."))";
    }
    else {
      $filter = "";
    }
  }
  if ( !empty($filter) ) {
    $set = $ldap->quick_search( $filter, array() );
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
