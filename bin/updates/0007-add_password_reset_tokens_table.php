<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core']['write'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'password_reset_tokens'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "CREATE TABLE password_reset_tokens ( `uid` varchar(40) NOT NULL, `token` varchar(40) NOT NULL, `user` mediumtext, `ip` varchar(40) NOT NULL DEFAULT '0.0.0.0', timestamp datetime DEFAULT NULL, PRIMARY KEY (uid) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  return "Add table for user default password and initialize (for students)";
}

?>
