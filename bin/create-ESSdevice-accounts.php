<?php
include_once( '../lib/data.phpm' );
include_once( '../lib/security.phpm' );
include_once( '../inc/person.phpm' );

$output_csv = "";
$accounts = array(
    '102' => 'Bloomington Elementary',
    '103' => 'Dixie Sun Elementary',
    '104' => 'Enterprise Elementary',
    '105' => 'Bloomington Hills Elementary',
    '107' => 'Coral Canyon Elementary',
    '108' => 'Diamond Valley Elementary',
    '110' => 'Coral Cliffs Elementary',
    '111' => 'Arrowhead Elementary',
    '112' => 'Hurricane Elementary',
    '114' => 'LaVerkin Elementary',
    '116' => 'Legacy Elementary',
    '118' => 'Water Canyon School',
    '120' => 'Heritage Elementary',
    '121' => 'Panorama Elementary',
    '122' => 'Sandstone Elementary',
    '123' => 'Red Mountain Elementary',
    '124' => 'Santa Clara Elementary',
    '128' => 'Springdale Elementary',
    '130' => 'Sunset Elementary',
    '134' => 'Three Falls Elementary',
    '140' => 'Washington Elementary',
    '143' => 'Majestic Fields Elementary',
    '144' => 'Riverside Elementary',
    '145' => 'Horizon Elementary',
    '146' => 'Little Valley Elementary',
    '147' => 'Crimson View Elementary',
    '240' => 'Post High',
    '303' => 'Sunrise Ridge Intermediate',
    '304' => 'Tonaquint Intermediate',
    '308' => 'Hurricane Intermediate',
    '320' => 'Fossil Ridge Intermediate',
    '325' => 'Lava Ridge Intermediate',
    '403' => 'Desert Hills Middle',
    '404' => 'Dixie Middle',
    '405' => 'Crimson Cliffs Middle',
    '408' => 'Hurricane Middle',
    '420' => 'Pine View Middle',
    '425' => 'Snow Canyon Middle',
    '703' => 'Desert Hills High',
    '704' => 'Dixie High',
    '712' => 'Enterprise High',
    '716' => 'Hurricane High',
    '718' => 'Millcreek High',
    '720' => 'Pine View High',
    '725' => 'Snow Canyon High',
    '850' => 'Southwest High (Adult Ed)',
    '920' => 'Snow Canyon Preschool',
    '921' => 'Riverside Preschool',
    '922' => 'Bloomington Hills Preschool',
    '924' => 'Hurricane Fields Preschool',
);

foreach ( $accounts as $locid => $locname ) {
  $entry = array(
    'objectclass' => array('top','inetOrgPerson','sambaSamAccount'),
    'uid' => "$locid-ESSdevice",
    'employeeType' => 'Guest',
    'businessCategory' => 'Guest',
    'sn' => 'ESSdevice',
    'givenName' => "$locname",
    'cn' => "$locname ESSdevice",
  );

  $password = create_password();

  if ( !empty($entry['uid']) ) {
      $entry['dn'] = 'uid='. ldap_escape($entry['uid'],'',LDAP_ESCAPE_DN) .',ou=ESS Accounts,ou=No Access Guest-Subs,dc=wcsd';
  }

  if ( !empty($entry['dn']) && !empty($password) ) {
    $ldap = new LDAP_Wrapper();
    $dups = $ldap->quick_search( array( 'uid' => $entry['uid'] ), array() );

    if ( count($dups) > 0 ) {
        print $entry['uid'] ." Account already exists!?\n";
    }
    else if ( count($dups) === 0 ) {
      if ( !empty($entry['dn']) ) {
        $dn = $entry['dn'];
        unset( $entry['dn'] );

        $entry['sambaSID'] = $ldap->get_next_num('sambaSID');
        if ( $ldap->do_add( $dn, $entry ) ) {
          set_password( $ldap, $dn, $password );
          print $dn ." Account created with password: $password\n";
          $output_csv .= "'". $entry['uid'] ."','$password','$locname'\n";
        }
        else {
          print $dn ." Account could not be created\n";
        }
      }
    }
  }
  else {
    if ( empty($error) ) {
      print "$locid-ESSdevice invalid uid or no password generated\n";
    }
  }
}

if ( !empty($output_csv) ) {
    $fh = fopen('/tmp/ESSdevice_accounts.csv','a');
    if ( fwrite($fh, $output_csv) !== false ) {
        print "accounts written to /tmp/ESSdevice_accounts.csv\n";
    }
}

?>
