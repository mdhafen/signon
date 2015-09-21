<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/schema.phpm' );
include_once( '../inc/google.phpm' );

if ( empty($_SESSION['REFERER']) && !empty($_SERVER['HTTP_REFERER']) ) {
	$_SESSION['REFERER'] = $_SERVER['HTTP_REFERER'];
}

global $GOOGLE_DOMAIN,$GOOGLE_CLIENT;
$errors = array();
$output = array();

do_ldap_connect();
$g_token = input( 'token', INPUT_STR );
$password = input( 'password', INPUT_STR );
$password2 = input( 'password2', INPUT_STR );
$username = '';
$email = '';

if ( !empty($g_token) ) {
  $ticket = $GOOGLE_CLIENT->verifyIdToken($g_token);
  if ( !empty($ticket) ) {
    $data = $ticket->getAttributes();
    $email = $data['payload']['email'];
    if ( strripos($email,'@'.$GOOGLE_DOMAIN) !== False ) {
      $username = substr($email,0,strpos($email,'@'));
    }
    else {
      $errors[] = 'WRONG_EMAIL_DOMAIN';
    }
  }
}

if ( !empty($username) ) {
	$dn = '';
	$set = ldap_quick_search( array( 'uid' => $username ), array() );
	if ( empty($set) ) {
		$user = get_user_google( $email );

		if ( !empty($user) ) {
			if ( stripos($user['orgUnitPath'],'nonusers') !== FALSE ) {
				$errors[] = 'REGISTERING_SERVICE_ACCOUNT';
			}
			else {
				$entry = google_user_hash_for_ldap( $user );
			}


			if ( !empty($entry['dn']) ) {
				$dn = $entry['dn'];
				unset( $entry['dn'] );
				$entry['objectClass'] = array('top','inetOrgPerson','sambaSamAccount');
				$entry['sambaSID'] = ldap_get_next_SID();
				do_ldap_add( $dn, $entry );
			}
		}
	}
	else {
		$object = $set[0];
		$dn = $object['dn'];
	}

	if ( ! empty($dn) ) {
		if ( $password === $password2 && strlen($password) >=8 ) {
			set_password( $dn, $password );
			google_set_password( $email, $password, false );
			$output['success'] = true;
		}
		else {
			$errors[] = 'PASSWORDS_NO_MATCH';
		}
	}
	else {
		$errors[] = 'USER_NOT_FOUND';
	}
}
else { // Present Google oAuth page
  output( $output, 'change_password_oauth' );
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	if ( !empty($output['success']) && !empty($_SESSION['REFERER']) && stripos($_SESSION['REFERER'],'https://myaccount.google.com') !== False ) {
		redirect( 'https://myaccount.google.com' );
	}
	else {
		output( $output, 'change_password' );
	}
}
?>
