-- MariaDB dump 10.19  Distrib 10.11.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: 639
-- ------------------------------------------------------
-- Server version	10.11.6-MariaDB-0+deb12u1

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
-- Table structure for table `demande`
--

DROP TABLE IF EXISTS `demande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demande` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateurs` int(11) NOT NULL,
  `demande` text NOT NULL,
  `status` enum('en attente','accepter','rejeter') DEFAULT 'en attente',
  PRIMARY KEY (`id`),
  KEY `id_utilisateurs` (`id_utilisateurs`),
  CONSTRAINT `demande_ibfk_1` FOREIGN KEY (`id_utilisateurs`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demande`
--

LOCK TABLES `demande` WRITE;
/*!40000 ALTER TABLE `demande` DISABLE KEYS */;
INSERT INTO `demande` VALUES
(1,1,'test','accepter'),
(2,1,'double','accepter');
/*!40000 ALTER TABLE `demande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demande_grade`
--

DROP TABLE IF EXISTS `demande_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demande_grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `nouveau_grade` enum('Civil','Garde','Garde-Vétéran','Caporal','Sergent','Lieutenant','Capitaine','Commandant','Colonel','Général','Major') NOT NULL,
  `statut` enum('en attente','approuvé','rejeté') DEFAULT 'en attente',
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `demande_grade_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demande_grade`
--

LOCK TABLES `demande_grade` WRITE;
/*!40000 ALTER TABLE `demande_grade` DISABLE KEYS */;
INSERT INTO `demande_grade` VALUES
(1,1,'Caporal','approuvé','2024-09-03 19:22:59'),
(2,1,'Caporal','rejeté','2024-09-03 19:23:14'),
(3,1,'Caporal','rejeté','2024-09-03 19:26:41'),
(4,1,'Caporal','rejeté','2024-09-03 19:26:42'),
(5,1,'Caporal','rejeté','2024-09-03 19:26:42'),
(6,1,'Caporal','rejeté','2024-09-03 19:27:17'),
(7,1,'Garde-Vétéran','approuvé','2024-09-03 19:27:38'),
(8,1,'Garde-Vétéran','rejeté','2024-09-03 19:27:45'),
(9,3,'Major','en attente','2024-09-05 21:59:21');
/*!40000 ALTER TABLE `demande_grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formation`
--

DROP TABLE IF EXISTS `formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `formation` enum('FB','FS','Aucune') DEFAULT 'Aucune',
  `formation_hierarchique` enum('FH1','FH2','FH3','FH4','FH5','FH6','FH1T','FH2T','FH3T','FH4T','FH5T','FH6T','Aucune') DEFAULT 'Aucune',
  PRIMARY KEY (`id`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `formation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formation`
--

LOCK TABLES `formation` WRITE;
/*!40000 ALTER TABLE `formation` DISABLE KEYS */;
INSERT INTO `formation` VALUES
(1,1,'FS','Aucune'),
(2,3,'FB','FH6'),
(3,23,'Aucune','Aucune');
/*!40000 ALTER TABLE `formation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informations_medicales`
--

DROP TABLE IF EXISTS `informations_medicales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informations_medicales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taille` int(11) NOT NULL,
  `poids` int(11) NOT NULL,
  `problemes_medicaux` text NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informations_medicales`
--

LOCK TABLES `informations_medicales` WRITE;
/*!40000 ALTER TABLE `informations_medicales` DISABLE KEYS */;
INSERT INTO `informations_medicales` VALUES
(13,180,80,'Rien',1);
/*!40000 ALTER TABLE `informations_medicales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personnages`
--

DROP TABLE IF EXISTS `personnages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personnages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `gerance` int(11) NOT NULL,
  `faction` enum('Officio Prefectus','Adeptus Mechanicus','Ecclésiarchie','Inquisition','Psyker','Abhumains') NOT NULL,
  `histoire` text NOT NULL,
  `validation` enum('Attente','Accepter','Rejeter') DEFAULT 'Attente',
  `raison` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `personnages_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personnages`
--

LOCK TABLES `personnages` WRITE;
/*!40000 ALTER TABLE `personnages` DISABLE KEYS */;
INSERT INTO `personnages` VALUES
(1,'Le grand patron',1,1,'Officio Prefectus','Il est puissant','Accepter',NULL);
/*!40000 ALTER TABLE `personnages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spe`
--

DROP TABLE IF EXISTS `spe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `ab` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spe`
--

LOCK TABLES `spe` WRITE;
/*!40000 ALTER TABLE `spe` DISABLE KEYS */;
INSERT INTO `spe` VALUES
(1,'Machine Gunner','MG'),
(2,'Anti-Tank','AT'),
(3,'Medicae','MDC'),
(4,'Vox Operator','VOX'),
(5,'Marksman','MKM'),
(6,'Plasma','PLM'),
(7,'Breacher','BRC'),
(8,'ETL','ETL'),
(9,'Fusilier','FSL'),
(10,'Commandement','CMDM');
/*!40000 ALTER TABLE `spe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `confirmation` tinyint(1) DEFAULT 0,
  `grade` varchar(255) DEFAULT 'Civil',
  `banni` tinyint(1) DEFAULT 0,
  `role` enum('utilisateur','admin') DEFAULT 'utilisateur',
  `spe_id` int(11) DEFAULT 0,
  `gerance` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_spe` (`spe_id`),
  CONSTRAINT `fk_spe` FOREIGN KEY (`spe_id`) REFERENCES `spe` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES
(1,'GIV-Baldwin','loicraoult31@gmail.com','$2y$10$RAtmmhi4bPW9nhHmqZ7VC.tLYiiN1rYe1JfkF7iedNx4A2dfz8DMa',1,'Capitaine',0,'admin',3,2),
(2,'CPI-66639 Sieger','pierreloic.gouttebel@gmail.com','$2y$10$qlt0yR2/p/YjLtDJcsfS9Oh8ckvi.Ebtm7QIEaWwV2Cx43MXekoDK',1,'GÃ©nÃ©ral',0,'admin',7,2),
(3,'MJI-33669 JÃ¤gerMeister','frayhan3@hotmail.fr','$2y$10$vxceCCLYmoEnYIqHcWOCe.nURs/5A7KLdl0WYiDTcO0gCnPtiNwN6',1,'Major',0,'admin',10,0),
(7,'GIV-14963 Riossak','rjudigael@gmail.com','$2y$10$qp0BWNVtK8o3ARPOUBuvIebWp0bx7fAsrYxuxtR6WpFitO4Ppb18K',1,'Garde-VÃ©tÃ©ran',0,'utilisateur',4,0),
(22,'test','fafa@gmail.com','$2y$10$VcwzdPteoRNsfsP1ALd3EO/yt8tcdw89Nhth412MBdyfhzfn2DSgK',1,'Civil',0,'utilisateur',9,NULL),
(23,'CPI-02695 Jojin','jojinfame@gmail.com','$2y$10$VsyTMTRBaajfP9iPtdtkGO5x7hg.JjsGVzBqW4iCmzKXkhj5BAYOO',1,'Capitaine',0,'utilisateur',3,0);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-11  2:32:44
