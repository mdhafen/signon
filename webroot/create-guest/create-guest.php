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
$user = '';
$user_email = '';
$submitted = input( 'submit', INPUT_STR );
$op = input( 'op', INPUT_HTML_NONE );

if ( !empty($submitted) ) {
  $email = input( 'email', INPUT_HTML_NONE );
  $user_email = $email;

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

  if ( !empty($entry['dn']) && !empty($password) ) {
    $ldap = new LDAP_Wrapper();
    $dups = $ldap->quick_search( array( 'uid' => $entry['uid'] ), array() );

    if ( count($dups) == 1 ) {
      set_password( $ldap, $dups[0]['dn'], $password );
      google_send_password( $entry['uid'], $provider, $password );
      $result = 'Account created';
    }
    else if ( count($dups) === 0 ) {
      if ( !empty($entry['dn']) ) {
        $dn = $entry['dn'];
        unset( $entry['dn'] );

        $entry['sambaSID'] = $ldap->get_next_num('sambaSID');
        if ( $ldap->do_add( $dn, $entry ) ) {
          set_password( $ldap, $dn, $password );
          google_send_password( $entry['uid'], $provider, $password );
          $result = 'Account created';
        }
        else {
          $error = 1;
          $result = 'There was an Error creating your account';
        }
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
$output['username'] = (empty($user_email))? "" : $user_email;

output( $output, 'create-guest/create-guest' );
?>