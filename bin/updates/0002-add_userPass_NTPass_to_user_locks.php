<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'user_locks' AND COLUMN_NAME = 'userPass'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE user_locks ADD COLUMN userPass mediumtext, ADD COLUMN NTPass mediumtext";
  $dbh->exec( $query );

  return "Add userPass and NTPass to user_locks";
}

?>
