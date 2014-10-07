<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database'];
$table = $db_settings['schema'];

$dbh = db_connect();
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = '' AND COLUMN_NAME = ''";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE ? ADD COLUMN ? INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER ?";
  $dbh->exec( $query );
  return "Example place holder for an update.";
}

?>
