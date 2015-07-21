<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

global $config;

do_ldap_connect();
if ( ! authenticate() ) {
	output( array(), 'login' );
	exit;
}

$attr = input( 'attrib', INPUT_STR );
$query = input( 'query', INPUT_STR );
$results = array();
$attrs = array(
	       'givenName' => 'First Name',
	       'sn' => 'Last Name',
	       'uid' => 'Username',
	       'l' => 'Building Abbreviation',
);
if ( !empty($attr) && empty($attrs[$attr]) ) {
    error( array('BAD_ATTRIBUTE') );
}

$output = array( 'attributes' => $attrs );

if ( !empty($attrs[$attr]) && !empty($query) ) {
  $set = ldap_quick_search( array( $attr => ldap_escape($query,'',LDAP_ESCAPE_FILTER) .'*' ), array() );
  foreach ( $set as $user ) {
    $results[] = $user['dn'];
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
