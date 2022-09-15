<?php
include_once( '../lib/input.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../lib/data.phpm' );
include_once( '../lib/output.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

authorize( 'login' );

$errors = array();
$object = array();

$ldap = new LDAP_Wrapper();
$dn = $_SESSION['userid'];
$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );

if ( empty($set) ) {
	$errors[] = 'PROFILE_USER_NOT_FOUND';
}
else {
	$object = $set[0];

	if ( ! is_person( $object ) ) {
		$errors[] = 'PROFILE_NOT_USER';
	}
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	$output = array(
		'object' => $object,
	);

	$password = input( 'password', INPUT_STR );
	$password2 = input( 'password2', INPUT_STR );
	$user_lock = get_lock_status( $dn );

	if ( !empty($password) && !empty($password2) ) {
		if ( strlen($password) < 8 ) {
			$output['error'] = 'Password is too short';
		}
        else if ( $times = is_pwned_password($password) ) {
			$output['error'] = 'PASS_TOO_COMMON';
            $output['error_times'] = $times;
        }
        else if ( ! empty($user_lock) ) {
            $output['error'] = 'USER_LOCKED';
        }
		else if ( $password === $password2 ) {
            if ( !empty($object['employeeType'][0]) && $object['employeeType'][0] != 'Guest' ) {
				google_set_password( $object['mail'][0], $password );
			}
			set_password( $ldap, $dn, $password );
			log_attr_change( $dn, array('userPassword'=>'') );
			$output['result'] = 'Password Set';
		}
		else {
			$output['error'] = 'Passwords do not match';
		}
	}

	output( $output, 'profile' );
}
?>
