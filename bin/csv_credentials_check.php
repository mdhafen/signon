<?php
include_once( '../lib/data.phpm' );
include_once( '../lib/input.phpm' );
global $config;

/*
    params:
    file      : csv file name
    no-header : csv file has a header row
    password  : 1-based index in csv file to password column
    uid       : 1-based index in csv file to optional uid column
    email     : 1-based index in csv file to optional email column
    either uid or email is required as are file and password
 */

$shortopts = 'hf:u:e:p:';
$longopts = array( 'no-header', 'file:', 'uid:', 'email:', 'password:' );
$params = getopt($shortopts,$longopts);

$c_file = $params['f'] ?? $params['file'];
$c_uid = $params['u'] ?? $params['uid'] ?? null;
$c_email = $params['e'] ?? $params['email'] ?? null;
$c_password = $params['p'] ?? $params['password'];
$c_noheader = $params['h'] ?? $params['header'] ?? null;

// fgetcsv returns a '0' indexed array
$c_file--;
$c_password--;
if ( !empty($c_uid) ) { $c_uid--; }
if ( !empty($c_email) ) { $c_email--; }

if ( empty($c_file) || empty($c_password) || ( empty($c_uid) && empty($c_email) ) ) {
    $missing = array();
    print 'Error, Missing paramaters! (';
    if ( empty($c_file) ) {
        $missing[] = "file name";
    }
    if ( empty($c_password) ) {
        $missing[] = "password column";
    }
    if ( empty($c_uid) && empty($c_email) ) {
        $missing[] = "either uid or email";
    }
    print implode( ', ', $missing ) . ")\n";
}

$users = array();
$in_file = $c_file;
// $auto_detect = ini_get('auto_detect_line_endings');
// ini_set('auto_detect_line_endings',TRUE);
$bom = "\xef\xbb\xbf";
$h = fopen( $in_file, 'r' );
if ( fgets($h,4) !== $bom ) {
    rewind($h);
}
while ( ! feof($h) ) {
    $row = fgetcsv($h);
    $id = '';
    if ( !empty($c_email) ) {
        $id = $row[$c_email];
        $id = substr($id,0,strpos($id,'@'));
    }
    else {
        $id = $row[ $c_uid ];
    }
    $users[] = array( $id, $row[$c_password] );
}
// ini_set('auto_detect_line_endings',$auto_detect);
// drop the csv header
if ( empty($c_noheader) ) { array_shift($users); }

$ldap = new LDAP_Wrapper();
$module = $config['authen_modules']['ldap'];
$root_dn = $ldap->config['userdn'];
$root_pass = $ldap->config['passwd'];

foreach ( $users as $pair ) {
    list($uid, $pass) = $pair;
    $found = 0;
    $search = $ldap->quick_search( "(&(objectClass=inetOrgPerson)(uid=$uid))" , array() );
    if ( empty($search) ) {
        print "Account not found: $uid\n";
    }
    else {
        print "Account found: $uid";
        if ( count($search) > 1 ) {
            print '  (multiple found)';
        }
        print "\n";
        foreach ( $search as $thisUser) {
            if ( $ldap->do_connect( $module, $thisUser['dn'], $pass ) ) {
//               $found++;
                print '  Match!: ' . $pass . ' : ' . $thisUser['dn'] . "\n";
            }
        }
/*
        if ( $found ) {
            print "  $found matches found\n";
        }
 */
        // re-bind as root for the next search
        $ldap->do_connect( $module, $root_dn, $root_pass );
    }
}

?>
