<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core']['write'];
$schema = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME = 'user_default_password' AND COLUMN_NAME = 'salt'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE user_default_password CHANGE COLUMN default_password default_password BLOB null default null, ADD COLUMN salt BLOB NULL after default_password, ADD COLUMN password_mode VARCHAR(32) NOT NULL DEFAULT '' after salt";
  $dbh->exec( $query );

  $query = "ALTER TABLE user_locks CHANGE COLUMN passwd passwd BLOB NULL, ADD COLUMN salt BLOB NULL after passwd, ADD COLUMN password_mode VARCHAR(32) NOT NULL DEFAULT '' after salt";
  $dbh->exec( $query );

  return "Add salt and password mode to user_locks and user_default_password";
}

?>
