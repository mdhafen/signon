<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );

$ldap = new LDAP_Wrapper();
authorize( 'manage_objects' );

$parent = input( 'parent', INPUT_HTML_NONE );
$class = input( 'class', INPUT_HTML_NONE );

$output = array();
$rid = '';
$must = array();
$may = array();
$defaults = array();
populate_static_user_attrs($defaults);
populate_dynamic_user_attrs($ldap,$defaults);

switch ( $class ) {
  case 'security': $class = array('person');
    $rid = 'cn';
    break;
  case 'user': $class = array('inetOrgPerson','posixAccount','sambaSamAccount');
    $rid = 'uid';
    break;
  case 'group': $class = array('groupOfNames');
    $rid = 'cn';
    break;
  case 'group2': $class = array('posixGroup');
    $rid = 'cn';
    break;
  case 'folder': $class = array('organizationalUnit');
    $rid = 'ou';
    break;
  case 'object':
    $class = input( 'objects', INPUT_HTML_NONE );
    if ( ! is_array($class) ) {
      $class = array($class);
    }
	list( $must, $may ) = $ldap->schema_get_object_requirements($class);
	foreach ( array('uid','cn','ou') as $attr ) {
		if ( in_array( $attr, $must ) ) {
			$rid = $attr;
			break;
		}
	}
	if ( empty($rid) ) {
		error( 'ADD_OBJECT_NO_RID' );
	}
    break;
}

if ( $class ) {
	list( $must, $may ) = $ldap->schema_get_object_requirements($class);
}

$output['rid'] = $rid;
$output['parent'] = $parent;
$output['classes'] = $class;
$output['must'] = $must;
$output['may'] = $may;
$output['defaults'] = $defaults;

output( $output, 'admin/add.tmpl' );
