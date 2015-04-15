<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/google.phpm' );
include_once( '../inc/person.phpm' );

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
  }
  else if ( !empty($email) && !empty($password) ) {
    if ( auth_to_google( $email, $password ) ) {
      $user = get_user_google( $email );

      if ( !empty($user) ) {
	if ( stripos($user['orgUnitPath'],'nonusers') !== FALSE ) {
	  $error = 1;
	  $result = 'Trying to register service account';
	}
	else if ( stripos($user['orgUnitPath'],'student') !== FALSE ) {
	  $entry['employeeType'] = 'Student';
	}
	else {
	  $entry['employeeType'] = 'Staff';
	}

	if ( !empty($entry['employeeType']) ) {
	  $entry['uid'] = strtolower(substr($user['primaryEmail'],0,strpos($user['primaryEmail'],'@')));
	  $entry['sn'] = $user['name']['familyName'];
	  $entry['givenName'] = $user['name']['givenName'];
	  $entry['cn'] = $user['name']['fullName'];
	  $entry['mail'] = strtolower($user['primaryEmail']);
	  $entry['l'] = google_org_to_loc( $user['orgUnitPath'] );
	  $ou = google_org_to_ou( $user['orgUnitPath'], $entry['l'] );
	  if ( !empty($ou) && !empty($entry['uid']) ) {
	    $entry['dn'] = 'uid='. ldap_escape($entry['uid'],'',LDAP_ESCAPE_DN) .','. $ou;
	  }
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
	google_send_password( $entry['uid'], $password );
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
	  google_send_password( $entry['uid'], $password );
	}
	$result = 'Account created';
      }
    }
  }
  else {
    $error = 1;
    $result = 'No Location for DN';
  }
}

$output['op'] = $op;
$output['result'] = $result;
$output['error'] = $error;

output( $output, $template );
?>
