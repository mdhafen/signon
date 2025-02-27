<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['Labs']['write'];
$table = $db_settings['schema'];

$dbh = db_connect('Labs');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'authorized_macs' AND COLUMN_NAME = 'iot_category' AND COLUMN_TYPE LIKE 'enum%Printer%'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE authorized_macs CHANGE COLUMN iot_category iot_category enum('','Lan','Staff','Facilities','Printer','Student','Phone','Camera','CyberCorp') not null default ''";
  $dbh->exec( $query );

  return "Add Printer to iot categories in mac address registration database";
}

?>
