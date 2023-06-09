<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'manage_objects' );

$op = input( 'action', INPUT_STR );
$dn = input( 'dn', INPUT_STR );
$object = array();

if ( $op == 'Add' ) {
	$classes = input( 'classes', INPUT_HTML_NONE );
	$object['objectClass'] = explode( ' ', $classes );
	$objectdn = input( 'dn', INPUT_HTML_NONE );
}
else {
	$set = $ldap->quick_search( '(objectClass=*)', array(), 0, $dn );
	$object = $set[0];
	$objectdn = $object['dn'];
	unset( $object['dn'] );
}
$rdn_attr = substr( $objectdn, 0, strpos( $objectdn, '=' ) );

ksort( $object, SORT_STRING | SORT_FLAG_CASE );

list( $must, $may ) = $ldap->schema_get_object_requirements($object['objectClass']);

$errors = array();

if ( $objectdn == $ldap->config['base'] ) {
	$errors[] = 'EDIT_BASE_DENIED';
}

// gather input
$count = input( 'count', INPUT_PINT );
$input = array();
for ( $i = 1; $i < $count; $i++ ) {
	$attr = input( "${i}_attr", INPUT_HTML_NONE );
	$vals = input( "${i}_val", INPUT_HTML_NONE );
	$vals = array_values(array_filter($vals));
	if ( ! empty($vals) ) {
		array_walk($vals,function(&$val,$key){
			$val = htmlspecialchars_decode( $val, ENT_QUOTES|ENT_HTML5 );
		});
		$input[ $attr ] = $vals;
	}
    if ( $attr == 'objectClass' && count($vals) != count($object['objectClass']) ) {
        list( $must, $may ) = $ldap->schema_get_object_requirements($vals);
    }
}
$input_attrs = array_keys( $input );
foreach ( $must as $attr ) {
	if ( ! in_array( $attr, $input_attrs ) ) {
		if ( $op != 'Add' && ( $attr != 'sambaSID' || $attr != 'uidNumber' ) ) {
			$errors[] = "EDIT_MISSING_ATTR $attr";
		}
	}
}
foreach ( $input as $attr => $vals ) {
	if ( ! in_array( $attr, $must ) && ! in_array( $attr, $may ) ) {
		$errors[] = "EDIT_UNKNOWN_ATTR $attr";
	}
}

// compare to object
$adds = array();
$dels = array();
$reps = array();
$object_attrs = array_keys( $object );
$all_attrs = array_unique( array_merge( $object_attrs, $input_attrs ) );
foreach ( $all_attrs as $attr ) {
	if ( ! in_array( $attr, $object_attrs ) ) {
		$adds[ $attr ] = $input[ $attr ];
	}
	else if ( ! in_array( $attr, $input_attrs ) ) {
		$dels[ $attr ] = $object[ $attr ];
	}
	else {
		foreach ( $input[$attr] as $val ) {
			if ( ! in_array($val,$object[$attr]) ) {
				$reps[ $attr ] = $input[ $attr ];
			}
		}
		foreach ( $object[$attr] as $val ) {
			if ( ! in_array($val,$input[$attr]) ) {
				$reps[ $attr ] = $input[ $attr ];
			}
		}
	}
}

if ( !empty($adds) || !empty($dels) || !empty($reps) ) {
	if ( $op == 'Add' ) {
		$password = '';
		if ( in_array( 'userPassword', array_keys($adds) ) ) {
			$password = $adds['userPassword'][0];
			unset( $adds['userPassword'] );
		}
		$adds['objectClass'] = $object['objectClass'];
		if ( in_array( 'sambaSamAccount', $adds['objectClass'] ) ) {
			$adds['sambaSID'] = $ldap->get_next_num('sambaSID');
			$adds['sambaPwdLastSet'] = time();
			$adds['sambaAcctFlags'] = '[U ]';
			$new_uid = explode('-',$adds['sambaSID']);
			$adds['uidNumber'] = end($new_uid);
		}
		if ( $ldap->do_add( $objectdn, $adds ) ) {
			if ( !empty($password) ) {
				if ( strlen($password) < 8 ) {
					$errors[] = "The password is too short";
				}
                else if ( $times = is_pwned_password($password) ) {
					$errors[] = "Password compromised, you can not use this password.  This password has been seen $times times before.  This password has previously appeared in a data breach and should never be used.  If you've ever used it anywhere before, you should change it as soon as possible.";
                }
				else {
					set_password( $ldap, $objectdn, $password );
				}
			}
		}
		else {
			$errors[] = "There was an error creating the account";
		}
	}
	else {
		// watch for $rdn_attr in particular
		$new_rdn = '';
		if ( in_array( $rdn_attr, array_keys($adds) ) || in_array( $rdn_attr, array_keys($dels) ) ) {
			$new_rdn = $adds[ $rdn_attr ][0];
			unset( $adds[ $rdn_attr ] );
			unset( $dels[ $rdn_attr ] );
		}
		if ( in_array( $rdn_attr, array_keys($reps) ) ) {
			$new_rdn = $reps[ $rdn_attr ][0];
			unset( $reps[ $rdn_attr ] );
		}

		$password = '';
		if ( in_array( 'userPassword', array_keys($adds) ) ) {
			$password = $adds['userPassword'][0];
			unset( $adds['userPassword'] );
		}

		$ldap->do_attr_replace( $objectdn, array_merge($adds,$reps) );
		$ldap->do_attr_del( $objectdn, $dels );
		//$ldap->do_attr_add( $objectdn, $adds );

		$mods = array_keys(array_merge($dels,$adds,$reps));
		$changes = array();
		foreach ( $mods as $attr ) {
			$changes[$attr] = $object[$attr][0];
		}
		log_attr_change( $objectdn, $changes );

		if ( !empty($password) ) {
			if ( strlen($password) < 8 ) {
				$errors[] = "The password is too short";
			}
            else if ( $times = is_pwned_password($password) ) {
                $errors[] = "Password compromised, you can not use this password.  This password has been seen $times times before.  This password has previously appeared in a data breach and should never be used.  If you've ever used it anywhere before, you should change it as soon as possible.";
            }
			else {
				set_password( $ldap, $objectdn, $password );
				log_attr_change( $objectdn, array('userPassword'=>'') );
			}
		}

		if ( $new_rdn ) {
			$new_parent = $ldap->dn_get_parent( $objectdn );
			$ldap->do_rename( $objectdn, $rdn_attr ."=". ldap_escape($new_rdn,'',LDAP_ESCAPE_DN), $new_parent );
			$objectdn = $rdn_attr ."=". $new_rdn .','. $new_parent;
		}
	}
}

if ( ! empty($errors) ) {
	error( $errors );
}
else {
	redirect( 'admin/object.php?dn='. urlencode($objectdn ) );
}
?>
