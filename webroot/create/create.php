<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/google.phpm' );
include_once( '../../inc/person.phpm' );

global $GOOGLE_DOMAIN,$GOOGLE_A_CLIENT;

$output = array();
$result = '';
$error = 0;
$user = '';
$user_email = '';
$submitted = input( 'submit', INPUT_STR );
$op = input( 'op', INPUT_HTML_NONE );
if ( empty($op) ) {
  $op = urldecode( input('state',INPUT_HTML_NONE) );
  $op = json_decode( $op, true );
  if ( !empty($op) ) {
    $op = $op['op'];
  }
  else {
    $op = '';
  }
}

$redirect = $config['base_url'] .'create/create.php';
$GOOGLE_A_CLIENT->setState( urlencode('{"op":"'. $op .'"}') );
$user = auth_to_google( $redirect );
if ( !empty($user) && strripos($user->email,'@'.$GOOGLE_DOMAIN) === False ) {
  $error = 1;
  $result = 'Wrong Google Domain';
}

if ( !empty($submitted) ) {
  $email = input( 'email', INPUT_HTML_NONE );
  $user_email = $email;
  $entry = array();
  $password = '';

  if ( !empty($user) ) {
    $email = $user->email;
    $user_email = $email;
    $user = get_user_google( $email );

    if ( !empty($user) ) {
      if ( stripos($user['orgUnitPath'],'nonusers') !== FALSE ) {
        $error = 1;
        $result = 'Trying to register service account';
      }
      else {
        $password = input( 'password', INPUT_STR );
        $password2 = input( 'password2', INPUT_STR );
        if ( $password != $password2 ) {
          unset($password);
          $error = 1;
          $result = 'Passwords do not match';
        }
        if ( empty($password) || strlen($password) < 8 ) {
          unset($password);
          $error = 1;
          $result = 'Password to short';
        }

        $entry = google_user_hash_for_ldap( $user );
        $entry['objectclass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');
        $entry['gidNumber'] = '65534';
        $entry['loginShell'] = '/bin/bash';
        $entry['homeDirectory'] = '/Users/'. $entry['uid'];
      }
    }
  }
  else {
    $error = 1;
    $result = 'User not signed in';
  }

  if ( !empty($entry['dn']) && !empty($password) ) {
    $ldap = new LDAP_Wrapper();
    $dups = $ldap->quick_search( array( 'uid' => $entry['uid'] ), array() );

    if ( count($dups) == 1 ) {
      set_password( $ldap, $dups[0]['dn'], $password );
      $result = 'Account created';
    }
    else if ( count($dups) === 0 ) {
      if ( !empty($entry['dn']) ) {
        $dn = $entry['dn'];
        unset( $entry['dn'] );

        $entry['sambaSID'] = $ldap->get_next_num('sambaSID');
        $entry['sambaPwdLastSet'] = time();
        $entry['sambaAcctFlags'] = '[U ]';
        $new_uid = explode('-',$entry['sambaSID']);
        $entry['uidNumber'] = end($new_uid);
        if ( $ldap->do_add( $dn, $entry ) ) {
          set_password( $ldap, $dn, $password );
          $result = 'Account created';
        }
        else {
          $error = 1;
          $result = 'There was an Error creating your account';
        }
      }
    }
    if ( $result == 'Account created' ) {
      google_oauth_signout();
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
$output['username'] = (empty($user_email))? "" : $user_email;

output( $output, 'create/create' );
?>
