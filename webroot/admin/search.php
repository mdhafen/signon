<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'set_password' );

$attr = input( 'attrib', INPUT_STR );
$query = input( 'query', INPUT_STR );
$results = array();
$filter = "";
$attrs = array(
    'uid' => 'Username',
    'member' => 'Group memberships by Username',
    'employeeNumber' => 'Staff/Student Number',
    'uidNumber' => 'Unix User Number',
    'businessCategory' => 'WiFi Category',
    'sn' => 'Last Name',
    'givenName' => 'First Name',
    'o' => 'Building Abbreviation',
);

if ( !empty($attr) ) {
  switch ($attr) {
    case 'uid':
    case 'sn':
    case 'givenName':
    case 'o':
    case 'employeeNumber':
    case 'businessCategory': $filter = "($attr=$query*)"; break;
    case 'uidNumber': $filter = "($attr=$query)"; break;
    case 'member':
      $set = $ldap->quick_search( array( 'uid' => $query ), array() );
      if ( !empty($set) ) {
        $filter = "(|(memberUid=$query)(member=".$set[0]['dn']."))";
      }
      else {
        $filter = "";
      }
      break;

    default: error( array('BAD_ATTRIBUTE') );
  }
}

if ( !empty($filter) ) {
  $set = $ldap->quick_search( $filter, array() );
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

$output['attributes'] = $attrs;
$output['attr'] = empty($attr) ? "" : $attr;

output( $output, 'admin/search.tmpl' );
?>
