<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/google.phpm' );
include_once( '../inc/person.phpm' );

$result = '';
$error = 0;
$op = input( 'op', INPUT_STR );
$submitted = input( 'submit', INPUT_STR );

$template = ($op == 'Guest') ? 'create-guest' : 'create';

if ( !empty($submitted) ) {
  $password = input( 'password', INPUT_STR );

  $entry = array(
    'objectclass' => array('top','inetOrgPerson','sambaSamAccount'),
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

  if ( $entry['employeeType'] == 'Guest' ) {
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
  else if ( !empty($entry['employeeType']) ) {
    $entry['mail'] = input( 'email', INPUT_HTML_NONE );
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
    do_ldap_connect();
    $dups = ldap_quick_search( array( 'uid' => $entry['uid'] ), array() );

    if ( count($dups) == 1 ) {
      if ( empty($dups[0]['userPassword'][0]) ) {
	set_password( $dups[0]['dn'], $password );
	$result = 'Account created';
      }
    }
    else if ( count($dups) === 0 ) {
      $dn = '';
      if ( $entry['employeeType'] == 'Guest' ) {
	  $dn = 'uid='. $entry['uid'] .',ou=Guest,dc=wcsd';
      }
      else if ( !empty($entry['l']) && !empty(loc_to_ou($entry['l'])) ) {
	$dn = 'uid='. $entry['uid'] .','. loc_to_ou( $entry['l'] );
      }
      else {
	$error = 1;
	$result = 'No Location for DN';
      }
      if ( !empty($dn) ) {
	$entry['sambaSID'] = ldap_get_next_SID();
	do_ldap_add( $dn, $entry );
	set_password( $dn, $password );
	$result = 'Account created';
      }
    }
  }
}

$output = array(
		'op' => $op,
		'result' => $result,
		'error' => $error,
);
output( $output, $template );
?>
