<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['Labs']['write'];
$table = $db_settings['schema'];

$dbh = db_connect('Labs');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'authorized_macs' AND COLUMN_NAME = 'labs_category' AND COLUMN_TYPE LIKE 'enum%Staff%'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE authorized_macs CHANGE COLUMN labs_category labs_category enum('','Lan','Labs','Staff','Facilities','AV','Phone','TechOffice','Guest') not null default 'Labs'";
  $dbh->exec( $query );

  return "Add Staff to labs categories in mac address registration database";
}

?>
