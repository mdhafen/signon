<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['Labs'];
$table = $db_settings['schema'];

$dbh = db_connect('Labs');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'authorized_macs' AND COLUMN_NAME = 'fields_category'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE authorized_macs CHANGE COLUMN device_category labs_category enum('','Lan','Labs','Facilities','AV','Phone','TechOffice','Guest') not null default 'Labs'";
  $dbh->exec( $query );

  $query = "ALTER TABLE authorized_macs ADD COLUMN fields_category enum('','Facilities') not null default ''";
  $dbh->exec( $query );

  $query = "ALTER TABLE authorized_macs ADD COLUMN iot_category enum('','Lan','Labs','Facilities','AV','TechOffice') not null default ''";
  $dbh->exec( $query );

  $query = "CREATE TABLE macs_log ( macaddress varchar(18) NOT NULL, submitted_ip decimal(39,0) DEFAULT NULL, submitted_user varchar(32) DEFAULT NULL, submitted_date date DEFAULT NULL, INDEX (macaddress) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  $query = "INSERT INTO macs_log ( macaddress, submitted_ip, submitted_user, submitted_date ) ( SELECT macaddress, submitted_ip, submitted_user, submitted_date from authorized_macs )";
  $dbh->exec( $query );

  $query = "ALTER TABLE authorized_macs DROP COLUMN submitted_user, DROP COLUMN submitted_ip, DROP COLUMN submitted_date";
  $dbh->exec( $query );

  return "Add macs log table and Add fields and iot device categories to mac address registration database";
}

?>
