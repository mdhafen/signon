<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/google.phpm' );
include_once( '../../inc/person.phpm' );

global $PROVIDER_MAP;

$output = array();
$result = '';
$error = 0;
$op = input( 'op', INPUT_STR );
$submitted = input( 'submit', INPUT_STR );

$template = ($op == 'Guest') ? 'create-guest' : 'create';

if ( !empty($submitted) ) {
  $password = input( 'password', INPUT_STR );
  $email = input( 'email', INPUT_HTML_NONE );

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

  if ( $op == 'Guest' ) {
    $password = create_password();
    $entry['uid'] = $entry['mobile'] = input( 'mobile', INPUT_HTML_NONE );
    $entry['sn'] = input( 'lastName', INPUT_HTML_NONE );
    $entry['givenName'] = input( 'firstName', INPUT_HTML_NONE );
    $entry['cn'] = $entry['givenName'] .' '. $entry['sn'];
    $entry['mail'] = $email;
    $entry['street'] = input( 'street', INPUT_HTML_NONE );
    $entry['l'] = input( 'city', INPUT_HTML_NONE );
    $entry['st'] = input( 'state', INPUT_HTML_NONE );
    $entry['postalCode'] = input( 'zip', INPUT_HTML_NONE );
    $entry['employeeType'] = 'Guest';
    $entry['dn'] = 'uid='. ldap_escape($entry['uid'],'',LDAP_ESCAPE_DN) .',ou=Guest,dc=wcsd';
    $provider = input( 'provider', INPUT_HTML_NONE );
  }
  else if ( !empty($email) && !empty($password) ) {
    if ( auth_to_google( $email, $password ) ) {
      $user = get_user_google( $email );

      if ( !empty($user) ) {
	if ( stripos($user['orgUnitPath'],'nonusers') !== FALSE ) {
	  $error = 1;
	  $result = 'Trying to register service account';
	}
	else {
	  $entry = google_user_hash_for_ldap( $user );
	  $entry['objectclass'] = array('top','inetOrgPerson','sambaSamAccount');
	}
      }
    }
    else {
      $error = 1;
      $result = 'Bad email address or password!';
    }
  }
  if ( !empty($entry['dn']) ) {
    do_ldap_connect();
    $dups = ldap_quick_search( array( 'uid' => $entry['uid'] ), array() );

    if ( count($dups) == 1 ) {
      set_password( $dups[0]['dn'], $password );
      if ( $entry['employeeType'] == 'Guest' ) {
	google_send_password( $entry['uid'], $provider, $password );
      }
      $result = 'Account created';
    }
    else if ( count($dups) === 0 ) {
      if ( !empty($entry['dn']) ) {
	$dn = $entry['dn'];
	unset( $entry['dn'] );

	$entry['sambaSID'] = ldap_get_next_SID();
	do_ldap_add( $dn, $entry );
	set_password( $dn, $password );
	if ( $entry['employeeType'] == 'Guest' ) {
	  google_send_password( $entry['uid'], $provider, $password );
	}
	$result = 'Account created';
      }
    }
  }
  else {
    if ( empty($error) ) {
      $error = 1;
      $result = "Couldn't find folder to create account";
    }
  }
}

$output['op'] = $op;
$output['result'] = $result;
$output['error'] = $error;
$output['providers'] = array_keys( $PROVIDER_MAP );

output( $output, 'create/'.$template );
?>
