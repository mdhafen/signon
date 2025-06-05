-- MySQL dump 10.13  Distrib 5.7.26, for Linux (x86_64)
--
-- Host: 205.126.10.160    Database: Labs_Auth_mac
-- ------------------------------------------------------
-- Server version	5.7.26-0ubuntu0.18.04.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `authorized_macs`
--

DROP TABLE IF EXISTS `authorized_macs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authorized_macs` (
  `macaddress` varchar(18) NOT NULL,
  `submitted_desc` text,
  `device_home` varchar(4) DEFAULT '000',
  `labs_category` enum('','Lan','Labs','Staff','Facilities','AV','Phone','TechOffice','Guest') NOT NULL DEFAULT 'Labs',
  `fields_category` enum('','Facilities') NOT NULL DEFAULT '',
  `iot_category` enum('','Lan','Staff','Facilities','AV','Printer','Student','Phone','Camera','CyberCorp','PLC') NOT NULL DEFAULT '',
  PRIMARY KEY `uk_macaddress` (`macaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

DROP TABLE IF EXISTS `macs_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `macs_log` (
  `macaddress` varchar(18) NOT NULL,
  `submitted_ip` decimal(39,0) DEFAULT NULL,
  `submitted_user` varchar(32) DEFAULT NULL,
  `submitted_date` date DEFAULT NULL,
  INDEX (`macaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `macs_last_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `macs_last_access` (
  `macaddress` varchar(18) NOT NULL,
  `ssid` varchar(18) NOT NULL DEFAULT '',
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`macaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dump completed on 2019-05-06 13:11:34
