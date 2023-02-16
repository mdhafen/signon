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
