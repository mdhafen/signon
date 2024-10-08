<?php
include_once( '../lib/data.phpm' );

$ldap = new LDAP_Wrapper();
$dom_sid = $ldap->quick_search( '(objectClass=sambaDomain)', array('sambaSID') );
if ( !empty($dom_sid[0]['sambaSID'][0]) ) {
    $dom_sid = $dom_sid[0]['sambaSID'][0];
}
else {
    $dom_sid = '';
}
$users = $ldap->quick_search( '(&(objectClass=inetOrgPerson)(!(employeeType=Guest)))', array() );
$dns = array();

foreach ( $users as $user ) {
  $mods = array();
  if ( !empty($user['homeDirectory'][0]) && stripos($user['homeDirectory'][0],'/home') === 0 ) {
    $home = str_ireplace('/home/','/Users/',$user['homeDirectory'][0]);
    $mods['homeDirectory'] = $home;
    $user['homeDirectory'][0] = $home;
  }
  if ( !empty($user['loginShell'][0]) && $user['loginShell'][0] == '/bin/bash/' ) {
    $mods['loginShell'] = '/bin/bash';
    $user['homeDirectory'][0] = '/bin/bash';
  }
  if ( !empty($user['employeeType'][0]) && $user['employeeType'][0] == 'Other' && stripos($user['dn'],'students') !== false ) {
    $mods['employeeType'] = 'Student';
    if ( empty($user['businessCategory'][0]) || $user['businessCategory'][0] != 'Confinement' || $user['businessCategory'][0] != 'Banned' ) {
      $mods['businessCategory'] = 'Student';
    }
  }
  if ( empty($user['businessCategory'][0]) ) {
    switch ( $user['employeeType'][0] ) {
      case 'Staff':
      case 'Student':
      case 'Guest':
      case 'Trusted':
      case 'Other':
// Confinement
// Banned
        $mods['businessCategory'] = $user['employeeType'][0];
        break;
      case 'offboarding-Staff':
      case 'suspended-Staff':
      case 'Transfer':
      case 'Transfer-Staff':
        $mods['businessCategory'] = 'Staff';
        break;
      case 'offboarding-Student':
      case 'suspended-Student':
      case 'Transfer-Student':
        $mods['businessCategory'] = 'Student';
        break;
      default:
        $mods['businessCategory'] = 'Other';
    }
  }
  if ( in_array('sambaSamAccount',$user['objectClass']) ) {
    if ( empty($user['sambaPwdLastSet'][0]) ) {
      $mods['sambaPwdLastSet'] = time();
    }
    if ( empty($user['sambaAcctFlags'][0]) ) {
      $mods['sambaAcctFlags'] = '[U ]';
    }
    if ( empty($user['sambaSID'][0]) || ( !empty($dom_sid) && strpos($user['sambaSID'][0],$dom_sid) === false ) ) {
      if ( empty($user['sambaSID'][0]) ) {
        $new_sid = $ldap->get_next_num( 'sambaSID' );
      }
      else {
        $sid_parts = explode('-',$user['sambaSID'][0]);
        !empty($sid_parts) && $new_sid = $dom_sid .'-'. end($sid_parts);
      }
      if ( !empty($new_sid) ) {
        $mods['sambaSID'] = $new_sid;
        $user['sambaSID'][0] = $new_sid;
      }
    }
  }
  else if ( $user['employeeType'][0] == 'Student' || $user['employeeType'][0] == 'Staff' ) {
    $mods['objectClass'] = $user['objectClass'];
    $mods['objectClass'][] = 'sambaSamAccount';
    $user['objectClass'] = $mods['objectClass'];
    $mods['sambaAcctFlags'] = '[U ]';
    $user['sambaPwdLastSet'][0] = $mods['sambaPwdLastSet'] = time();
    $user['sambaSID'][0] = $mods['sambaSID'] = $ldap->get_next_num('sambaSID');
  }
  if ( ! in_array('posixAccount',$user['objectClass']) ) {
    $mods['objectClass'] = $user['objectClass'];
    $mods['objectClass'][] = 'posixAccount';
    $mods['gidNumber'] = '65534';
    $mods['loginShell'] = '/bin/bash';
    if ( !empty($user['uid'][0]) && empty($user['homeDirectory'][0]) ) {
      $mods['homeDirectory'] = '/Users/'. $user['uid'][0];
    }
    if ( empty($user['sambaSID'][0]) ) {
      $user['sambaSID'][0] = $mods['sambaSID'] = $ldap->get_next_num('sambaSID');
    }
    $new_uid = explode('-',$user['sambaSID'][0]);
    $mods['uidNumber'] = end($new_uid);
  }
  if ( !empty($mods) ) {
    $ldap->do_modify( $user['dn'], $mods );
    print $user['uid'][0] .":". implode(',', array_keys($mods) ) ."\n";
  }
}
?>
