<?php

include_once( '../../lib/config.phpm' );
include_once( '../../lib/data.phpm' );

$db_settings = $config['database']['core']['write'];
$table = $db_settings['schema'];

$dbh = db_connect('core');
$query = "SELECT COUNT(*) AS count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$table' AND TABLE_NAME = 'message_templates'";
$sth = $dbh->query( $query );
$row = $sth->fetch();
if ( $row['count'] == 0 ) {
  $query = "CREATE TABLE `message_templates` (
  `template_id` INT NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `transport` ENUM('EMail','SMS','Print') NOT NULL DEFAULT 'EMail',
  `lang` varchar(5) NOT NULL,
  `subject` varchar(200),
  `body` mediumtext,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY (`code`,`transport`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  $query = "INSERT INTO `message_templates` (`code`,`transport`,`lang`,`subject`,`body`) VALUES
  ('PASSWD_RESET','EMail','en-us','WCSD SignOn / Google password reset','Click this link to reset your WCSD SignOn / Google password : [[_BASE_URL]]change_password.php?op=Reset&amp;uid=[[UID]]&amp;token=[[TOKEN]].  This link will expire after 5 minutes.')";
  $dbh->exec( $query );

  $query = "CREATE TABLE `message_queue` (
  `queue_id` INT NOT NULL AUTO_INCREMENT,
  `to_uid` varchar(40) NOT NULL,
  `from_uid` varchar(40) NULL DEFAULT NULL,
  `template_id` INT,
  `status` ENUM('pending','failed','sent','cancelled') NOT NULL DEFAULT 'pending',
  `status_metadata` mediumtext,
  `subject` varchar(200),
  `body` mediumtext,
  PRIMARY KEY (`queue_id`),
  CONSTRAINT `template_id_fk` FOREIGN KEY (`template_id`) REFERENCES `message_templates` (`template_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  $query = "CREATE TABLE `user_message_settings` (
  `userid` varchar(40) NULL DEFAULT NULL,
  `template_id` INT NOT NULL,
  UNIQUE KEY (`userid`,`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $dbh->exec( $query );

  $query = "INSERT INTO `user_message_settings` (`userid`,`template_id`)
  ( SELECT NULL,`template_id` FROM `message_templates` WHERE `code` = 'PASSWD_RESET' AND `transport` = 'EMail' AND `lang` = 'en-us' )";
  $dbh->exec( $query );

  return "Add tables for messaging (templates, user settings, queue)";
}

?>
