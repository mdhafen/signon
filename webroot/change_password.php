<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

if ( empty($_SESSION['REFERER']) && !empty($_SERVER['HTTP_REFERER']) ) {
	$_SESSION['REFERER'] = $_SERVER['HTTP_REFERER'];
}

$op = input( 'op', INPUT_STR );
$uid = input( 'uid', INPUT_STR );
$token = input( 'token', INPUT_STR );

if ( empty($op) ) {
	$op = 'Change';
}

global $GOOGLE_DOMAIN,$GOOGLE_A_CLIENT;
$ldap = new LDAP_Wrapper();
$errors = array();
$output = array( 'op' => $op );
$username = '';
$email = '';

if ( $op == 'Reset' && !empty($uid) && !empty($token) ) {
	$set = $ldap->quick_search( array( 'uid' => $uid ), array() );
	if ( ! empty($set) ) {
		$user = $set[0];
	}
	$token_info = get_password_reset_token($token);
	if ( empty($user) || empty($token_info) || empty($token_info['uid']) || $token_info['uid'] != $uid ) {
		$op = 'Change';
		$output['op'] = $op;
		$errors[] = 'INVALID_TOKEN';
	}
	else {
		$output['uid'] = $uid;
		$output['token'] = $token;
		$email = $user['mail'][0];
	}
}
else if ( $op != 'Reset' ) {
	$GOOGLE_A_CLIENT->setState( urlencode('{"op":"'. $op .'"}') );
	$user = auth_to_google( $config['base_url'] .'change_password.php' );
}

$password = input( 'password', INPUT_STR );
$password2 = input( 'password2', INPUT_STR );

if ( !empty($password) && !empty($password2) ) {
	if ( !empty($user) ) {
		if ( $op != 'Reset' ) {
			$email = $user->email;
		}
		if ( strripos($email,'@'.$GOOGLE_DOMAIN) !== False ) {
			$username = substr($email,0,strpos($email,'@'));

			$dn = '';
			$set = $ldap->quick_search( array( 'uid' => $username ), array() );
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
						populate_static_user_attrs($entry);
						populate_dynamic_user_attrs($ldap,$entry);
						$dn = $entry['dn'];
						unset( $entry['dn'] );
						if ( ! $ldap->do_add( $dn, $entry ) ) {
							$errors[] = 'There was an error creating an account for '. $username;
						}
					}
				}
			}
			else {
				$object = $set[0];
				$dn = $object['dn'];
			}

			if ( ! empty($dn) ) {
				$user_lock = get_lock_status( $username );
				if ( $password !== $password2 ) {
					$errors[] = 'PASSWORDS_DONT_MATCH';
				} else if ( strlen($password) < 8 ) {
					$errors[] = 'PASS_TOO_SHORT';
				} else if ( $times = is_pwned_password($password) ) {
					$errors[] = 'PASS_TOO_COMMON';
					$output['error_times'] = $times;
				} else if ( !empty($user_lock) ) {
					$errors[] = 'USER_LOCKED';
				} else {
					$result = google_set_password( $email, $password );
					set_password( $ldap, $dn, $password );
					log_attr_change( $dn, array('userPassword'=>'') );
					$output['success'] = true;
					if ( $op == 'Reset' ) {
						clean_password_reset_tokens($token);
					}
				}
			}
			else {
				$errors[] = 'USER_NOT_FOUND';
			}
		}
		else {
			$errors[] = 'WRONG_EMAIL_DOMAIN';
		}
	}
	else {
		$errors[] = 'USER_NOT_SIGNED_IN';
	}
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	if ( !empty($output['success']) ) {
		if ( $op != 'Reset' ) {
			google_oauth_signout();
			if ( !empty($_SESSION['REFERER']) && stripos($_SESSION['REFERER'],'https://myaccount.google.com') !== False ) {
				redirect( 'https://myaccount.google.com' );
				exit;
			}
		}
        else {
		    redirect( $config['base_url'] );
		    exit;
        }
	}
	output( $output, 'change_password' );
}
?>
