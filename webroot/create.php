<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/google.phpm' );

$result = '';
$op = input( 'op', INPUT_STR );
$submitted = input( 'submit', INPUT_STR );

$entry = array(
	       'objectclass' => array('top','inetOrgPerson'),
	       'uid' => '',
	       'employeeType' => '',
	       'sn' => '',
	       'givenName' => '',
	       'cn' => '',
	       'mail' => '',
	       'street' => '',
	       'l' => '',
	       'st' => '',
	       'postalCode' => '',
	       'mobile' => '',
	       );

switch ($op) {
  case "Employee":
    $entry['employeeType'] = 'Staff';
    break;

  case "Student":
    $entry['employeeType'] = 'Student';
    break;

  case "Guest":
    $entry['employeeType'] = 'Guest';
    break;
}

$template = ($op == 'Guest') ? 'create-guest' : 'create';

if ( !empty($submitted) ) {
  if ( $op == 'Guest' ) {
    $entry['uid'] = $entry['mobile'] = input( 'mobile', INPUT_HTML_NONE );
    $entry['sn'] = input( 'lastName', INPUT_HTML_NONE );
    $entry['givenName'] = input( 'firstName', INPUT_HTML_NONE );
    $entry['cn'] = $entry['givenName'] .' '. $entry['sn'];
    $entry['mail'] = input( 'email', INPUT_HTML_NONE );
    $entry['street'] = input( 'street', INPUT_HTML_NONE );
    $entry['l'] = input( 'city', INPUT_HTML_NONE );
    $entry['st'] = input( 'state', INPUT_HTML_NONE );
    $entry['postalCode'] = input( 'zip', INPUT_HTML_NONE );
  }
  else {
    $entry['mail'] = input( 'email', INPUT_HTML_NONE );
    $password = input( 'password', INPUT_STR );
    if ( auth_to_google( $entry['mail'], $password ) ) {
      $user = get_user_google( $entry['mail'] );

      if ( !empty($user) ) {
	$entry['uid'] = strtolower(substr($user['primaryEmail'],0,strpos($user['primaryEmail'],'@')));
	$entry['sn'] = $user['name']['familyName'];
	$entry['givenName'] = $user['name']['givenName'];
	$entry['cn'] = $user['name']['fullName'];
	$entry['mail'] = strtolower($user['primaryEmail']);
	$entry['l'] = google_org_to_loc( $user['orgUnitPath'] );
      }
    }
  }
  if ( !empty($entry['uid']) ) {
    //FIXME process entry for ldap add
    //  do ldap_search for uid to make sure it's unique (ignore race condition)
    //  do ldap_add
    $result = 'Account created';
  }
}

$output = array(
		'op' => $op,
		'result' => $result,
);
output( $output, $template );
?>
