<?php
include_once( '../lib/data.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

$email = "";
if ( !empty($argv[1]) ) {
  $email = $argv[1];
  if ( stripos($email,'@washk12') === false ) {
      $email .= '@washk12.org';
  }
}

$user = get_user_google( $email );

if ( empty($user) ) {
    print "Couldn't get a user for $email\n";
    exit;
}
$entry = google_user_hash_for_ldap( $user );
$entry['objectclass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');
$entry['gidNumber'] = '65534';
$entry['loginShell'] = '/bin/bash';
$entry['homeDirectory'] = '/Users/'. $entry['uid'];
$entry['sambaPwdLastSet'] = time();
$entry['sambaAcctFlags'] = '[U ]';

$ldap = new LDAP_Wrapper();
$filter = array( 'uid' => $entry['uid'] );
if ( empty($entry['uid']) ) {
	$filter = array( 'mail' => $email );
}
$dup = $ldap->quick_search( $filter, array() );

if ( empty($dup[0]['sambaSID'][0]) ) {
        $entry['sambaSID'] = $ldap->get_next_num('sambaSID');
        $new_uid = explode('-',$entry['sambaSID']);
        $entry['uidNumber'] = end($new_uid);
}

print "Google entry: ". print_r($user,true);
print "ldap entry: ". print_r($dup,true);
print "New entry: ". print_r($entry,true);
?>
