<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core']['write'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'user_locks' AND COLUMN_NAME = 'uid'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "ALTER TABLE user_locks ADD COLUMN uid varchar(40) NOT NULL AFTER dn";
  $dbh->exec( $query );

  $query = "UPDATE user_locks SET uid = SUBSTRING(dn, 5, LOCATE(',',dn) - 5)";
  $dbh->exec( $query );

  $query = "ALTER TABLE user_locks DROP COLUMN dn, DROP COLUMN userPass, DROP COLUMN NTPass";
  $dbh->exec( $query );

  $query = "DELETE FROM user_locks WHERE uid = :uid LIMIT 1";
  $sth2 = $dbh->prepare($query);

  $query = "SELECT uid FROM user_locks GROUP BY uid HAVING count(*) > 1 LIMIT 1";
  $sth = $dbh->prepare($query);
  $sth->execute();
  $row = $sth->fetch();
  while ( !empty($row['uid']) ) {
    $sth2->bindValue( ':uid', $row['uid'] );
    $sth2->execute();

    $sth->execute();
    $row = $sth->fetch();
  }

  $query = "ALTER TABLE user_locks ADD PRIMARY KEY (uid)";

  return "Change dn to uid in table for user locks";
}

?>
