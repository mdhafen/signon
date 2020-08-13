<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'attribute_changes' AND COLUMN_NAME = 'user_ip'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE attribute_changes ADD COLUMN user_ip varchar(40) NOT NULL DEFAULT '0.0.0.0'";
  $dbh->exec( $query );

  return "Add user ip address to attribute changes table";
}

?>
