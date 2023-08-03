<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM message_templates WHERE code = 'PASSWD_RESET' AND transport = 'EMail' AND body LIKE '% 5 minutes%'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] > 0 ) {
  $query = "UPDATE message_templates SET body = REPLACE(REPLACE(body,'&amp;','&'),' 5 minutes',' 10 minutes') WHERE code = 'PASSWD_RESET' AND transport = 'EMail' AND body LIKE '% 5 minutes%'";
  $dbh->exec( $query );

  return "Change token lifespan in password reset token message.";
}

?>
