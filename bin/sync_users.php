<?php
include_once( '../lib/data.phpm' );
include_once( '../inc/person.phpm' );
include_once( '../inc/google.phpm' );

$email = "";
if ( !empty($argv[1]) ) {
  if ( !empty($argv[1]) ) {
    $email = $argv[1];
    if ( stripos($email,'@washk12') === false ) {
      $email .= '@washk12.org';
    }
  }

  $google_cache = array( get_user_google( $email ) );
}
else {
  $google_cache = google_get_all_users();
}

$students = array();
//$in_file = 'PowerSchool_QuickInfo_Export_Students-to-Google-Apps.csv';
if ( !empty($argv[2]) ) {
  $in_file = $argv[2];
  $h = fopen( $in_file, 'r' );
  while ( ! feof($h) ) {
    $row = fgetcsv($h);
    $students[ strtolower($row[0]) ] = $row[3];
  }
  array_shift($students);  // drop the csv header
  $students = array_filter($students);
}

$ldap = new LDAP_Wrapper();
$users = array();
if ( !empty($argv[1]) ) {
  $uid = substr($email,0,strpos($email,'@'));
  $users = $ldap->quick_search( "(|(mail=$email)(uid=$uid))" , array() );
} else {
  $users = $ldap->quick_search( '(&(!(|(employeeType=Guest)(employeeType=Trusted)))(objectClass=inetOrgPerson))' , array() );
}
$users_lookup = array();

while ( !empty($users) ) {
  $thisUser = array_shift($users);
  $lookup = empty($thisUser['employeeNumber']) ? $thisUser['mail'][0] : $thisUser['employeeNumber'][0];
  $users_lookup[ $lookup ] = $thisUser;
}
unset($users);

foreach ( $google_cache as $g_user ) {
  if ( stripos($g_user['orgUnitPath'],'nonusers') !== FALSE ) continue; //service account
  $entry = google_user_hash_for_ldap( $g_user );
  $output = "";
  if ( ( !empty($entry['employeeNumber']) && !empty($users_lookup[ $entry['employeeNumber'] ]) ) || !empty($users_lookup[ $entry['mail'] ]) ) {
    if ( empty($entry['employeeNumber']) || empty($users_lookup[ $entry['employeeNumber'] ]) ) {
      $thisUser = $users_lookup[ $entry['mail'] ];
      unset($users_lookup[ $entry['mail'] ]);
    }
    else {
      $thisUser = $users_lookup[ $entry['employeeNumber'] ];
      unset($users_lookup[ $entry['employeeNumber'] ]);
    }

    $entry['objectClass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');
    $entry['gidNumber'] = '65534';
    $entry['homeDirectory'] = '/Users/'. $entry['uid'];
    $entry['loginShell'] = '/bin/bash';
    $entry['sambaAcctFlags'] = '[U ]';

    $dn = $thisUser['dn'];
    $rdn_attr = substr( $dn, 0, strpos($dn,'=') );
    $ignored_fields = array($rdn_attr,'dn','objectClass','userPassword','sambaNTPassword');
    $dynamic_fields = array('sambaSID','sambaPwdLastSet','uidNumber');
    $mod_add = array();
    $mod_del = array();
    foreach ( $entry as $field => $value ) {
        if ( array_search($field,$ignored_fields) !== false ) { continue; }
        if ( array_search($field,$dynamic_fields) !== false ) { continue; }
        if ( empty($thisUser[$field]) ) {
            $mod_add[$field] = $value;
        }
        else if ( $thisUser[$field][0] != $value ) {
            $mod_del[$field] = array();
            $mod_add[$field] = $value;
        }
    }
    $dynamic_generated = false;
    foreach ( $dynamic_fields as $field ) {
        if ( empty($thisUser[$field]) ) {
            if ( empty($dynamic_generated) ) {
                if ( empty($thisUser['sambaSID']) ) {
                    $entry['sambaSID'] = $ldap->get_next_num('sambaSID');
                }
                else {
                    $entry['sambaSID'] = $thisUser['sambaSID'][0];
                }
                $entry['sambaPwdLastSet'] = time();
                $new_uid = explode('-',$entry['sambaSID']);
                $entry['uidNumber'] = end($new_uid);
                $dynamic_generated = 1;
            }
            if ( !empty($entry[$field]) ) {
                $mod_add[$field] = $entry[$field];
            }
        }
    }
    foreach ( $thisUser as $field => $values ) {
        if ( array_search($field,$ignored_fields) !== false ) { continue; }
        if ( array_search($field,$dynamic_fields) !== false ) { continue; }
        if ( empty($entry[$field]) ) {
            $mod_del[$field] = array();
        }
    }

    if ( !empty($mod_add) || !empty($mod_del) ) {
      $ldap->do_attr_del( $dn, $mod_del );
      $ldap->do_modify( $dn, $mod_add );
      $mods = array_keys(array_merge($mod_del,$mod_add));
      sort($mods);
      $output .= "mod (". implode(',',$mods) .") ";
    }

    if ( strcasecmp( $dn, $entry['dn'] ) != 0 ) {
      $ldap->do_rename( $dn, "uid=". ldap_escape($entry['uid'],'',LDAP_ESCAPE_DN), $ldap->dn_get_parent($entry['dn']) );
      $output .= "move ";
    }

    if ( $entry['employeeType'] == 'Student' && empty($thisUser['userPassword']) && !empty($students[ $entry['mail'] ]) ) {
      set_password( $ldap, $entry['dn'], $students[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  else {
    $entry['objectClass'] = array('top','inetOrgPerson','posixAccount','sambaSamAccount');

    $dn = $entry['dn'];
    unset( $entry['dn'] );

    $entry['sambaSID'] = $ldap->get_next_num( 'sambaSID' );
    $entry['sambaPwdLastSet'] = time();
    $entry['sambaAcctFlags'] = '[U ]';
    $new_uid = explode('-', $entry['sambaSID']);
    $entry['uidNumber'] = end($new_uid);
    $entry['gidNumber'] = '65534';
    $entry['homeDirectory'] = '/Users/'. $entry['uid'];
    $entry['loginShell'] = '/bin/bash';

    $ldap->do_add( $dn, $entry );
    $entry['dn'] = $dn;
    $output .= "Add ";

    if ( $entry['employeeType'] == 'Student' && !empty($students[ $entry['mail'] ]) ) {
      set_password( $ldap, $dn, $students[ $entry['mail'] ] );
      $output .= "And set Password ";
    }
  }
  if ( !empty($output) ) {
    print "Working ". $entry['mail'] ." : ". $output ."\n";
  }
}
foreach ( $users_lookup as $lookup => $thisUser ) {
  print "Working ". $thisUser['mail'][0] ." : isn't in google! ";
  $dn = $thisUser['dn'];
  $ldap->do_delete( $dn );
  print "Deleted\n";
}

?>
