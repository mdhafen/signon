--
-- Table structure for table `message_templates`
--

DROP TABLE IF EXISTS `message_templates`;
CREATE TABLE `message_templates` (
  `template_id` INT NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `transport` ENUM('EMail','SMS','Print') NOT NULL DEFAULT 'EMail',
  `lang` varchar(5) NOT NULL,
  `subject` varchar(200),
  `body` mediumtext,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY (`code`,`transport`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `message_templates` (`code`,`transport`,`lang`,`subject`,`body`) VALUES
  ('PASSWD_RESET','EMail','en-us','WCSD SignOn / Google password reset','Click this link to reset your WCSD SignOn / Google password : [[_BASE_URL]]/change_password.php?op=Reset&amp;uid=[[UID]]&amp;token=[[TOKEN]].  This link will expire after 5 minutes.');

--
-- Table structure for table `message_queue`
--

DROP TABLE IF EXISTS `message_queue`;
CREATE TABLE `message_queue` (
  `queue_id` INT NOT NULL AUTO_INCREMENT,
  `to_uid` varchar(40) NOT NULL,
  `from_uid` varchar(40) NOT NULL,
  `template_id` INT,
  `status` ENUM('pending','failed','sent','cancelled') NOT NULL DEFAULT 'pending',
  `status_metadata` mediumtext,
  `subject` varchar(200),
  `body` mediumtext,
  PRIMARY KEY (`queue_id`),
  CONSTRAINT `template_id_fk` FOREIGN KEY (`template_id`) REFERENCES `message_templates` (`template_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user_message_settings`
--

DROP TABLE IF EXISTS `user_message_settings`;
CREATE TABLE `user_message_settings` (
  `userid` varchar(40),
  `template_id` INT NOT NULL,
  UNIQUE KEY (`userid`,`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_message_settings` (`userid`,`template_id`)
  ( SELECT NULL,`template_id` FROM `message_templates` WHERE `code` = 'PASSWD_RESET' AND `transport` = 'EMail' AND `lang` = 'en-us' );

--
-- Table structure for table `attribute_changes`
--

DROP TABLE IF EXISTS `attribute_changes`;
CREATE TABLE `attribute_changes` (
  `dn` varchar(256) NOT NULL,
  `attr` varchar(28) NOT NULL,
  `previous_value` mediumtext,
  `user` mediumtext,
  `ip` varchar(40) NOT NULL DEFAULT '0.0.0.0',
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`attr`,`dn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user_locks`
--

DROP TABLE IF EXISTS `user_locks`;
CREATE TABLE `user_locks` (
  `dn` varchar(256) NOT NULL,
  `passwd` mediumtext NULL,
  `user` mediumtext,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `guest_signatures`
--

DROP TABLE IF EXISTS `guest_signatures`;
CREATE TABLE `guest_signatures` (
  `guest_uid` varchar(256) NOT NULL,
  `aup_sent` datetime DEFAULT NULL,
  `aup_signed` datetime DEFAULT NULL,
  `aup_expire` datetime DEFAULT NULL,
  `guest_token` varchar(96) DEFAULT NULL,
  PRIMARY KEY (`guest_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user_default_password`
--

DROP TABLE IF EXISTS `user_default_password`;
CREATE TABLE `user_default_password` (
  uid varchar(40) NOT NULL,
  default_password varchar(39) DEFAULT NULL,
  timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `pwned_passwords`
--

DROP TABLE IF EXISTS `pwned_passwords`;
CREATE TABLE `pwned_passwords` (
  `hash` varchar(40) NOT NULL,
  `times_seen` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
