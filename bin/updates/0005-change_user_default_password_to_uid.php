<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core']['write'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'user_default_password' AND COLUMN_NAME = 'uid'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE user_default_password ADD COLUMN uid varchar(40) NOT NULL AFTER dn";
  $dbh->exec( $query );

  $query = "UPDATE user_default_password SET uid = SUBSTRING(dn, 5, LOCATE(',',dn) - 5)";
  $dbh->exec( $query );

  $query = "ALTER TABLE user_default_password DROP COLUMN dn";
  $dbh->exec( $query );

  $query = "DELETE FROM user_default_password WHERE uid = :uid LIMIT 1";
  $sth2 = $dbh->prepare($query);

  $query = "SELECT uid AS num FROM user_default_password GROUP BY uid HAVING count(*) > 1 LIMIT 1";
  $sth = $dbh->prepare($query);
  $sth->execute();
  $row = $sth->fetch();
  while ( $row['uid'] ) {
    $sth2->bindValue( ':uid', $row['uid'] );
    $sth2->execute();

    $sth->execute();
    $row = $sth->fetch();
  }

  $query = "ALTER TABLE user_default_password ADD PRIMARY KEY (uid)";

  return "Change dn to uid in table for user default password";
}

?>
