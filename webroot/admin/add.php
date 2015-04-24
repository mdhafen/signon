<?php
include_once( '../../lib/input.phpm' );
include_once( '../../lib/security.phpm' );
include_once( '../../lib/data.phpm' );
include_once( '../../lib/output.phpm' );
include_once( '../../inc/person.phpm' );
include_once( '../../inc/schema.phpm' );

global $config, $ldap;

do_ldap_connect();
authorize( 'manage_objects' );

$parent = input( 'parent', INPUT_HTML_NONE );
$class = input( 'class', INPUT_HTML_NONE );

$output = array();
$rid = '';
$must = array();
$may = array();

switch ( $class ) {
  case 'security': $class = array('person');
    $rid = 'cn';
    break;
  case 'user': $class = array('inetOrgPerson','sambaSamAccount');
    $rid = 'uid';
    break;
  case 'group': $class = array('groupOfNames');
    $rid = 'cn';
    break;
  case 'folder': $class = array('organizationalUnit');
    $rid = 'ou';
    break;
}

if ( $class ) {
   list( $must, $may ) = schema_get_object_requirements($class);
}

$output['rid'] = $rid;
$output['parent'] = $parent;
$output['classes'] = $class;
$output['must'] = $must;
$output['may'] = $may;

output( $output, 'admin/add.tmpl' );
