<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core']['write'];
$schema = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME = 'tokens' AND COLUMN_NAME = 'purpose'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "RENAME TABLE password_reset_tokens TO tokens";
  $dbh->exec( $query );

  $query = "ALTER TABLE tokens ADD COLUMN purpose enum('pass_reset','authen') NOT NULL after ip, ADD UNIQUE KEY (token), ADD UNIQUE KEY (uid,purpose), CHANGE COLUMN timestamp timestamp datetime DEFAULT CURRENT_TIMESTAMP";
  $dbh->exec( $query );

  $query = "CREATE TABLE token_log ( uid VARCHAR(40) NOT NULL, user MEDIUMTEXT, ip VARCHAR(40) NOT NULL DEFAULT '0.0.0.0', purpose ENUM('pass_reset','authen') NOT NULL, timestamp datetime DEFAULT CURRENT_TIMESTAMP, KEY (uid) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  return "Add token purpose and log";
}

?>
