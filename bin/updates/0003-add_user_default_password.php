<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'user_default_password'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "CREATE TABLE user_default_password ( dn varchar(256) NOT NULL, default_password varchar(39) DEFAULT NULL, timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX (dn) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  $query = "INSERT INTO default_password (dn,default_password,timestamp) VALUES (:dn,:passwd)";
  $sth = $dbh->prepare($query);
  $ldap = new LDAP_Wrapper();
  $users = array();
  $users = $ldap->quick_search( '(employeeType=Student)' , array() );
  while ( !empty($users) ) {
    $thisUser = array_shift($users);
	$passwd = strtolower( substr($thisUser['givenName'][0],0,1) . substr($thisUser['sn'][0],0,1) . $thisUser['employeeNumber'][0] );
	$sth->bindValue( ':dn', $thisUser['dn'] );
	$sth->bindValue( ':passwd', $passwd );
	$sth->execute();
}

  return "Add table for user default password and initialize (for students)";
}

?>
