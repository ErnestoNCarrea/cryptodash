-- MySQL dump 10.17  Distrib 10.3.17-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: cryptodash_dev
-- ------------------------------------------------------
-- Server version	10.3.17-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asset`
--

DROP TABLE IF EXISTS `asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset`
--

LOCK TABLES `asset` WRITE;
/*!40000 ALTER TABLE `asset` DISABLE KEYS */;
INSERT INTO `asset` VALUES (32,'ARS','Pesos'),(840,'USD','Dólar'),(1001,'BTC','Bitcoin'),(1002,'ETH','Ethereum'),(1003,'XRP','Ripple'),(1004,'LTC','Litecoin'),(2000,'USDT','Tether'),(2001,'DAI','DAI');
/*!40000 ALTER TABLE `asset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_order`
--

DROP TABLE IF EXISTS `book_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exchange_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pair` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `quantity` double NOT NULL,
  `side` smallint(6) NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FBEB86E168AFD1A0` (`exchange_id`),
  KEY `IDX_FBEB86E1A76ED395` (`user_id`),
  CONSTRAINT `FK_FBEB86E168AFD1A0` FOREIGN KEY (`exchange_id`) REFERENCES `exchange` (`id`),
  CONSTRAINT `FK_FBEB86E1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_order`
--

LOCK TABLES `book_order` WRITE;
/*!40000 ALTER TABLE `book_order` DISABLE KEYS */;
INSERT INTO `book_order` VALUES (226,9000,NULL,'BTC/ARS',551465.54,0,1,'2019-10-18 23:05:24'),(227,9000,NULL,'BTC/ARS',550613.23,0,1,'2019-10-18 23:05:24'),(228,9000,NULL,'BTC/ARS',540000,0,1,'2019-10-18 23:05:24'),(229,9000,NULL,'BTC/ARS',538848.25,0,1,'2019-10-18 23:05:24'),(230,9000,NULL,'BTC/ARS',538333,0,1,'2019-10-18 23:05:24'),(231,9000,NULL,'BTC/ARS',527083.26,0,1,'2019-10-18 23:05:24'),(232,9000,NULL,'BTC/ARS',527000,0,1,'2019-10-18 23:05:24'),(233,9000,NULL,'BTC/ARS',520001,0,1,'2019-10-18 23:05:24'),(234,9000,NULL,'BTC/ARS',520000,0,1,'2019-10-18 23:05:24'),(235,9000,NULL,'BTC/ARS',515318.27,0,1,'2019-10-18 23:05:24'),(236,9000,NULL,'BTC/ARS',512000,0,1,'2019-10-18 23:05:24'),(237,9000,NULL,'BTC/ARS',510000,0,1,'2019-10-18 23:05:24'),(238,9000,NULL,'BTC/ARS',508000,0,1,'2019-10-18 23:05:24'),(239,9000,NULL,'BTC/ARS',490000,0,1,'2019-10-18 23:05:24'),(240,9000,NULL,'BTC/ARS',487000,0,1,'2019-10-18 23:05:24'),(241,9000,NULL,'BTC/ARS',567999.99,0,2,'2019-10-18 23:05:24'),(242,9000,NULL,'BTC/ARS',568000,0,2,'2019-10-18 23:05:24'),(243,9000,NULL,'BTC/ARS',580000,0,2,'2019-10-18 23:05:24'),(244,9000,NULL,'BTC/ARS',585000,0,2,'2019-10-18 23:05:24'),(245,9000,NULL,'BTC/ARS',589000,0,2,'2019-10-18 23:05:24'),(246,9000,NULL,'BTC/ARS',600000,0,2,'2019-10-18 23:05:24'),(247,9000,NULL,'BTC/ARS',610000,0,2,'2019-10-18 23:05:24'),(248,9000,NULL,'BTC/ARS',618000,0,2,'2019-10-18 23:05:24'),(249,9000,NULL,'BTC/ARS',625000,0,2,'2019-10-18 23:05:24'),(250,9000,NULL,'BTC/ARS',625885.42,0,2,'2019-10-18 23:05:24'),(251,9000,NULL,'BTC/ARS',630000,0,2,'2019-10-18 23:05:24'),(252,9000,NULL,'BTC/ARS',637650.4,0,2,'2019-10-18 23:05:24'),(253,9000,NULL,'BTC/ARS',649415.39,0,2,'2019-10-18 23:05:24'),(254,9000,NULL,'BTC/ARS',658000,0,2,'2019-10-18 23:05:24'),(255,9000,NULL,'BTC/ARS',659560.59,0,2,'2019-10-18 23:05:24'),(256,9000,NULL,'ETH/ARS',11570.6,0,1,'2019-10-18 23:05:25'),(257,9000,NULL,'ETH/ARS',11317.31,0,1,'2019-10-18 23:05:25'),(258,9000,NULL,'ETH/ARS',11300,0,1,'2019-10-18 23:05:25'),(259,9000,NULL,'ETH/ARS',11064.01,0,1,'2019-10-18 23:05:25'),(260,9000,NULL,'ETH/ARS',11000,0,1,'2019-10-18 23:05:25'),(261,9000,NULL,'ETH/ARS',10810.72,0,1,'2019-10-18 23:05:25'),(262,9000,NULL,'ETH/ARS',9617.85,0,1,'2019-10-18 23:05:25'),(263,9000,NULL,'ETH/ARS',8909.17,0,1,'2019-10-18 23:05:25'),(264,9000,NULL,'ETH/ARS',12275,0,2,'2019-10-18 23:05:25'),(265,9000,NULL,'ETH/ARS',12300,0,2,'2019-10-18 23:05:25'),(266,9000,NULL,'ETH/ARS',12500,0,2,'2019-10-18 23:05:25'),(267,9000,NULL,'ETH/ARS',13000,0,2,'2019-10-18 23:05:25'),(268,9000,NULL,'ETH/ARS',13758.69,0,2,'2019-10-18 23:05:25'),(269,9000,NULL,'ETH/ARS',14011.98,0,2,'2019-10-18 23:05:25'),(270,9000,NULL,'ETH/ARS',14265.28,0,2,'2019-10-18 23:05:25'),(271,9000,NULL,'ETH/ARS',14518.57,0,2,'2019-10-18 23:05:25'),(272,9000,NULL,'ETH/ARS',15000,0,2,'2019-10-18 23:05:25'),(273,9000,NULL,'ETH/ARS',18000,0,2,'2019-10-18 23:05:25');
/*!40000 ALTER TABLE `book_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exchange`
--

DROP TABLE IF EXISTS `exchange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `infinite_supply` tinyint(1) NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exchange`
--

LOCK TABLES `exchange` WRITE;
/*!40000 ALTER TABLE `exchange` DISABLE KEYS */;
INSERT INTO `exchange` VALUES (1000,'Binance',0,'BinanceExchange'),(1001,'CEX',0,'CexExchange'),(9000,'Ripio',0,'RipioExchange'),(9001,'DolarIol',1,'DolarIolExchange');
/*!40000 ALTER TABLE `exchange` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate`
--

DROP TABLE IF EXISTS `rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exchange_id` int(11) NOT NULL,
  `pair` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buy_price` double NOT NULL,
  `sell_price` double NOT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DFEC3F3968AFD1A0` (`exchange_id`),
  CONSTRAINT `FK_DFEC3F3968AFD1A0` FOREIGN KEY (`exchange_id`) REFERENCES `exchange` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate`
--

LOCK TABLES `rate` WRITE;
/*!40000 ALTER TABLE `rate` DISABLE KEYS */;
INSERT INTO `rate` VALUES (1,9000,'BTC/ARS',589080.189258,565359.8636705,'2019-10-18 23:05:24'),(2,9000,'ETH/ARS',589080.189258,565359.8636705,'2019-10-18 23:05:25'),(3,1000,'BTC/USD',7957.41558859,7957.41558859,'2019-10-18 23:09:42'),(4,1000,'ETH/USD',173.558414,173.558414,'2019-10-18 23:09:43');
/*!40000 ALTER TABLE `rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_request_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'equistango@gmail.com','Ernesto','Carrea','[]','$argon2i$v=19$m=65536,t=4,p=1$Q1JGZ01yamgwZVJnQkpGMw$42p3CgvWV+pSqR0zT+i3iOT5cEWD/8a3/J/yTbB5+zc',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-18 20:09:53
