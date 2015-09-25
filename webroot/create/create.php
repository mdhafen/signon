<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/google.phpm' );
include_once( '../../inc/person.phpm' );

global $PROVIDER_MAP,$GOOGLE_A_CLIENT;

$output = array();
$result = '';
$error = 0;
$user = '';
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

$template = ($op == 'Guest') ? 'create-guest' : 'create';

if ( $op != 'Guest' ) {
  $redirect = $config['base_url'] .'create/create.php';
  $GOOGLE_A_CLIENT->setState( urlencode('{"op":"'. $op .'"}') );
  $user = auth_to_google( $redirect );
}

if ( !empty($submitted) ) {
  $email = input( 'email', INPUT_HTML_NONE );

  $password = '';
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
  else if ( !empty($user) ) {
    $email = $user->email;
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
        }

        $entry = google_user_hash_for_ldap( $user );
        $entry['objectclass'] = array('top','inetOrgPerson','sambaSamAccount');
      }
    }
  }
  else {
    $error = 1;
    $result = 'User not signed in';
  }

  if ( !empty($entry['dn']) && !empty($password) ) {
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
$output['providers'] = array_keys( $PROVIDER_MAP );

output( $output, 'create/'.$template );
?>
