<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/google.phpm' );

if ( ! authorized('reset_password') && ! authenticate_api_client() ) {
	output( '<?xml version ="1.0"?><result><state>error</state><flag>ACCESS_DENIED</flag></result>', '', $xml=1 );
	exit;
}

global $GOOGLE_DOMAIN;
$ldap = new LDAP_Wrapper();
$users = array();
$output = '<?xml version ="1.0"?><result>';
$warnings = array();
$dn = input( 'dn', INPUT_STR );
$uid = input( 'uid', INPUT_STR );
$new_passwd = input( 'password', INPUT_STR );
if ( !empty($dn) ) {
	$users = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
} else if ( !empty($uid) ) {
	$users = $ldap->quick_search( array( 'uid' => $uid ), array() );
} else {
	output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_INPUT</flag></result>', '', $xml=1 );
	exit;
}

if ( count($users) < 1 ) {
	output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_FOUND</flag></result>', '', $xml=1 );
	exit;
}

$change_users = array();
foreach ( $users as $user ) {
	if ( empty($user['givenName']) || empty($user['sn']) || empty($user['employeeNumber']) ) {
		output( '<?xml version ="1.0"?><result><state>error</state><flag>DOES_NOT_COMPUTE</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
		exit;
	}
	if ( empty($user['employeeType']) || $user['employeeType'][0] != 'Student' ) {
		// limit to students for now.
		output( '<?xml version ="1.0"?><result><state>error</state><flag>NOT_STUDENT</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
		exit;
	}
	// old password scheme
	$old_password = mb_strtolower(mb_substr($user['givenName'][0],0,1))
		. mb_strtolower(mb_substr($user['sn'][0],0,1))
		. $user['employeeNumber'][0];

	$def_password = get_default_password($user['uid'][0]);
	if ( empty($def_password) || !empty($new_passwd) ) {
        if ( empty($new_passwd) ) {
            $def_password = generate_default_password($user);
        }
        else {
            $def_password = $new_passwd;
        }
        set_default_password( $user['uid'][0], $def_password );
		$warnings[] = '<warning><flag>CREATED_DEFAULT</flag><message>'. htmlspecialchars($user['uid'][0],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></warning>';
	}
	$change_users[] = $user;
}
unset($users);
if ( empty($change_users) ) {
	output( '<?xml version ="1.0"?><result><state>error</state><flag>NO_CHANGES</flag><message>'. htmlspecialchars($user['dn'],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</message></result>', '', $xml=1 );
	exit;
}

if ( $warnings ) {
  $output .= '<warnings>'. implode( '', $warnings ) .'</warnings>';
}
$output .= '<messages>';
foreach ( $change_users as $user ) {
	$def_password = get_default_password($user['uid'][0]);
	if ( !empty($user['employeeType'][0]) && strripos($user['mail'][0],'@'.$GOOGLE_DOMAIN) !== False ) {
		$result = google_set_password( $user['mail'][0], $def_password );
	}
	$result = set_password( $ldap, $user['dn'], $def_password );
	if ( $result ) {
		log_attr_change( $user['dn'], array('userPassword'=>'') );

		$output .= '<message><uid>'. htmlspecialchars($user['uid'][0],ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</uid><password>'. htmlspecialchars($def_password,ENT_QUOTES|ENT_XML1|ENT_SUBSTITUTE) .'</password></message>';
	}
}

$output .= '</messages><state>success</state></result>';
output( $output, '', $xml=1 );

?>
