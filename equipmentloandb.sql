-- MySQL dump 10.13  Distrib 5.5.55, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: equipmentloandb
-- ------------------------------------------------------
-- Server version	5.5.55-0ubuntu0.14.04.1

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
-- Table structure for table `Device`
--

DROP TABLE IF EXISTS `Device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Device` (
  `Type` enum('Laptop','Desktop','Tablet','Other') DEFAULT NULL,
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Model` varchar(30) DEFAULT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `OS` varchar(50) DEFAULT NULL,
  `Available` tinyint(4) DEFAULT NULL,
  `RequestID` mediumint(8) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Device`
--

LOCK TABLES `Device` WRITE;
/*!40000 ALTER TABLE `Device` DISABLE KEYS */;
INSERT INTO `Device` VALUES ('Desktop',1,'T3600','7777-WD-00004','Windows',NULL,22),('Laptop',2,'E7470-45','7777-WL-00045','Windows',NULL,9),('Desktop',3,'7010','7777-WD-00003','Windows',NULL,21),('Desktop',4,'9010','7777-WD-00005','Windows',NULL,NULL),('Desktop',5,'9020','7777-WD-00001','Windows',NULL,11),('Desktop',6,'9020SFF','7777-WD-00002','Windows',NULL,NULL),('Desktop',7,'9030','7777-WD-00006','Windows',NULL,9),('Desktop',8,'E7470-42','7777-WL-00042','Windows',NULL,NULL),('Desktop',10,'E7470-43','7777-WL-00043','Windows',NULL,20),('Desktop',11,'E7470-44','7777-WL-00044','Windows',NULL,NULL),('Desktop',13,'E7470-46','7777-WL-00046','Windows',NULL,NULL),('Desktop',14,'Surface','7777-WT-00002','Windows',NULL,NULL);
/*!40000 ALTER TABLE `Device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DeviceInfoAndRequest`
--

DROP TABLE IF EXISTS `DeviceInfoAndRequest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DeviceInfoAndRequest` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceInfoID` mediumint(8) unsigned DEFAULT NULL,
  `RequestID` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `DeviceInfoID` (`DeviceInfoID`),
  KEY `RequestID` (`RequestID`),
  CONSTRAINT `DeviceInfoAndRequest_ibfk_1` FOREIGN KEY (`DeviceInfoID`) REFERENCES `DeviceRequestInfo` (`id`),
  CONSTRAINT `DeviceInfoAndRequest_ibfk_2` FOREIGN KEY (`RequestID`) REFERENCES `Request` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DeviceInfoAndRequest`
--

LOCK TABLES `DeviceInfoAndRequest` WRITE;
/*!40000 ALTER TABLE `DeviceInfoAndRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `DeviceInfoAndRequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DeviceRequestInfo`
--

DROP TABLE IF EXISTS `DeviceRequestInfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DeviceRequestInfo` (
  `Type` enum('Laptop','Desktop','Tablet','Other') NOT NULL,
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `OS` varchar(30) DEFAULT NULL,
  `Setup` blob,
  `Peripherals` blob,
  `RequestId` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UserId` (`RequestId`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DeviceRequestInfo`
--

LOCK TABLES `DeviceRequestInfo` WRITE;
/*!40000 ALTER TABLE `DeviceRequestInfo` DISABLE KEYS */;
INSERT INTO `DeviceRequestInfo` VALUES ('Laptop',1,'Windows 10','I need Photoshop on it.','Nothing else.',1),('Laptop',2,'Win','None','None',2),('Laptop',3,'Win','none','none',3),('Laptop',4,'Win','none','none',4),('Laptop',5,'Win','none','none',5),('Tablet',6,'ios','','keyboard',1),('Laptop',7,'mac','','',1),('Laptop',8,'win','','',1),('Laptop',9,'Windows ','','',8),('Laptop',10,'Windows ','','',9),('Laptop',11,'Windows','None','None',10),('Laptop',12,'Windows','None','None',11),('Laptop',13,'Windows','None','None',12),('Laptop',14,'Windows','None','None',9),('Laptop',15,'Mac','None','None',10),('Laptop',16,'Mac','','',1),('',17,'','','',15),('Laptop',18,'Windows','None','None',16),('Desktop',19,'Windows','','',1),('Desktop',20,'Mac','','',17),('Desktop',21,'Mac','','',18),('Desktop',22,'Mac','','',18),('Tablet',23,'Mac','','',19),('',24,'Mac','','',20),('Laptop',25,'Mac','','',20),('',26,'Win 7','asdf','asdf',21),('',27,'Win 7','asdf','asdf',22),('',28,'Win 7','asdf','asdf',23),('',29,'Win 7','asdf','asdf',24);
/*!40000 ALTER TABLE `DeviceRequestInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DevicesRequested`
--

DROP TABLE IF EXISTS `DevicesRequested`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DevicesRequested` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `id_device` mediumint(8) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_device` (`id_device`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `DevicesRequested_ibfk_1` FOREIGN KEY (`id_device`) REFERENCES `Device` (`id`),
  CONSTRAINT `DevicesRequested_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DevicesRequested`
--

LOCK TABLES `DevicesRequested` WRITE;
/*!40000 ALTER TABLE `DevicesRequested` DISABLE KEYS */;
/*!40000 ALTER TABLE `DevicesRequested` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Request`
--

DROP TABLE IF EXISTS `Request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Request` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `checkoutDate` date NOT NULL,
  `returnDate` date NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `pickupPerson` varchar(50) DEFAULT NULL,
  `pickupLocation` text,
  `granted` tinyint(1) DEFAULT NULL,
  `Peripherals` text,
  `DateGenerated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Request`
--

LOCK TABLES `Request` WRITE;
/*!40000 ALTER TABLE `Request` DISABLE KEYS */;
INSERT INTO `Request` VALUES (20,'2017-06-07','2017-06-14',1,'Carlos will','N/A',1,NULL,'2017-06-06 09:49:57'),(21,'2017-06-05','2017-06-07',3,'asdf','asdf',1,NULL,'2017-06-06 11:47:29'),(22,'2017-06-05','2017-06-07',3,'asdf','asdf',1,NULL,'2017-06-06 11:55:14'),(23,'2017-06-05','2017-06-07',3,'asdf','asdf',NULL,NULL,'2017-06-06 11:55:45'),(24,'2017-06-05','2017-06-07',3,'asdf','asdf',NULL,NULL,'2017-06-06 11:55:49');
/*!40000 ALTER TABLE `Request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(30) NOT NULL,
  `LastName` varchar(30) NOT NULL,
  `Email` varchar(20) NOT NULL,
  `Phone` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,'Carlos','Gonzalez','testing2@email.edu','33044040505'),(2,'Carlos','Gonzalez','testing@email.edu','33044040505'),(3,'Tester','One','testerone@wustl.edu','314-362-5555'),(4,'Tester','Two','testertwo@wustl.edu','3149355555'),(5,'Tom2','Tom2','t@wustl.edu','1'),(6,'tom2','tom','testing','123'),(7,'tom3','tom','testing','123'),(8,'tom4','tom','testing','123'),(9,'tom5','tom','testing','123'),(10,'Carlos','Gonzalez Mendoza','cgonzalezmendoza@wus','3306899554'),(11,'Jane','Doe','jdoe@wustl.edu','300 546 312'),(12,'Jane','Doe2','jdoe@wustl.edu','300 546 312'),(13,'Jane','Doe4','jdoe4@wustl.edu','300 546 312'),(14,'Jane','Doe 4','jd2@wustl.edu','330 899 456'),(15,'l','','',''),(16,'Samuel','Teeter','steeter@wustl.edi','780-801-123'),(17,'Carlos','Enrique','testing','1203'),(18,'Carlos2','Enrique','testing','1203'),(19,'David','Testing','david@hotmail.com','123 123 123'),(20,'David2','Testing2','david@hotmail.com','123 123 123');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-07  1:37:56
