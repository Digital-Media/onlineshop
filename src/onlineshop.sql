-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 15, 2010 at 09:40 AM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Database: `onlineshop`
--

DROP SCHEMA IF EXISTS `onlineshop` ;
CREATE SCHEMA IF NOT EXISTS `onlineshop` DEFAULT CHARACTER SET utf8;
USE onlineshop;

-- --------------------------------------------------------

-- 
-- Tabellen anlegen
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `session_id` varchar(250) NOT NULL,
  `product_idproduct` bigint NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  PRIMARY KEY (`session_id`, `product_idproduct`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `idorders` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_iduser` bigint unsigned NOT NULL,
  `total_sum` decimal(10,2) NOT NULL,
  `date_ordered` datetime NOT NULL,
  `payment_type` varchar(45) NOT NULL,
  `payment_string1` varchar(50) NOT NULL,
  `payment_string2` varchar(50) NULL,
  `payment_string3` varchar(50) NULL,
  `instructions` varchar(100) NOT NULL,
  `delivery_address_line` varchar(150) NOT NULL,
  `delivery_zipcode` varchar(10) NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_country` varchar(100) NOT NULL,
  `billing_address_line` varchar(150) NULL,
  `billing_zipcode` varchar(10) NULL,
  `billing_city` varchar(100) NULL,
  `billing_country` varchar(100) NULL,
  PRIMARY KEY (`idorders`),
  KEY `user_fk` (`user_iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE IF NOT EXISTS `order_item` (
  `orders_idorders` bigint unsigned NOT NULL,
  `product_idproduct` bigint unsigned NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`orders_idorders`, `product_idproduct`),
  KEY `orders_fk` (`orders_idorders`),
  KEY `product_fk` (`product_idproduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pentest`
--

DROP TABLE IF EXISTS `pentest`;
CREATE TABLE IF NOT EXISTS `pentest` (
  `idpentest` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` char(128) NOT NULL,
  `active` char(128) DEFAULT NULL,
  `role` char(5) NOT NULL DEFAULT 'user',
  `pt_varchar1` varchar(255) NULL,
  `pt_varchar2` varchar(255) NULL,
  `pt_int` int NULL,
  `pt_decimal` decimal(10,2) NULL,
  PRIMARY KEY (`idpentest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `product_category_name` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `short_description` varchar(250) NOT NULL,
  `long_description` text NOT NULL,
  `active` boolean NOT NULL DEFAULT TRUE,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`idproduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

DROP TABLE IF EXISTS `product_category`;
CREATE TABLE IF NOT EXISTS `product_category` (
  `idproduct_category` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_category_name` varchar(45) NOT NULL,
  PRIMARY KEY (`idproduct_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `nick_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` char(128) NOT NULL,
  `active` char(128) DEFAULT NULL,
  `role` char(5) NOT NULL DEFAULT 'user',
  `date_registered` datetime NOT NULL,
  `phone` varchar(45) NULL,
  `mobile` varchar(45) NULL,
  `fax` varchar(45) NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Fremdschlüssel anlegen
--

-- --------------------------------------------------------

--
-- RELATIONS FOR TABLE `orders`:
--   `users_id`
--       `users` -> `users_id`
--

ALTER TABLE `orders`
ADD CONSTRAINT `user_fk` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`);

-- --------------------------------------------------------

--
-- RELATIONS FOR TABLE `order_item`:
--   `idorder`
--       `orders` -> `orders_id`
--   `idproduct`
--       `product` -> `idproduct`
--

ALTER TABLE `order_item`
ADD CONSTRAINT `orders_fk` FOREIGN KEY (`orders_idorders`) REFERENCES `orders` (`idorders`);
ALTER TABLE `order_item`
ADD CONSTRAINT `product_fk` FOREIGN KEY (`product_idproduct`) REFERENCES `product` (`idproduct`);

-- --------------------------------------------------------

--
-- Insert der Daten
-- 

-- --------------------------------------------------------

--
-- Dumping data for table `pentest`
--

-- Die Spalten pentest_varchar1 und pentest_varchart2 dienen als Spalten für email und password, um Angriffe auf Login zu testen
INSERT INTO `pentest` (`idpentest`, `email`, `password`, `active`, `role`) VALUES
(1, 'shopuser1@onlineshop.at', 'geheim', null, 'admin'),
(2, 'shopuser2@onlineshop.at', 'geheim', null, 'user');

-- --------------------------------------------------------

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`idproduct`, `product_name`, `price`, `short_description`, `long_description`, `active`, `date_added`) VALUES
(1, 'Passivhaus', '300000.00', 'Haus mit U-Wert<10kWh/am2', 'Haus mit U-Wert<10kWh/am2\r\n20m2 Solaranlage\r\n40m2 Photovoltaik\r\n7500l Regenwassertank, ideal an kalten Wintertagen', 1, '2009-12-28 15:45:03'),
(2, 'Niedrigenergiehaus', '250000.00', 'Haus mit U-Wert<45kWh/am2', 'Haus mit U-Wert<45kWh/am2\r\n20 m2 Solaranlage, ideal an kalten Wintertagen', 0, '2009-12-28 15:45:44'),
(3, 'Seegrundstück', '200000.00', 'Seegrundstück am Attersee', 'Seegrundstück am Attersee mit Seeblick und Bergblick, ideal für heiße Sommertage', 1,'2009-12-29 16:15:42'),
(4, 'Almgrundstück', '300000.00', 'Almgrundstück an einem Bergsee', 'Almgrundstück an einem Bergsee mit Zufahrtsstraße, geschottert und Winterräumung, ideal für heiße Sommertage', 1,'2009-12-29 16:15:42'),
(5, 'Talgrundstück', '10000.00', 'Grundstück am Talende', 'Talgrundstück am Ende des Steyerlingtales, wenig Sonne, dafür viel kaltes Bachwasser direkt neben dem Grundstück, ideal für heiße Sommertage', 1,'2009-12-29 16:15:42');

--
-- Adding FULLTEXT Index for `product`
--

ALTER TABLE product ADD FULLTEXT product_fulltext (product_name, short_description, long_description);
ALTER TABLE product ADD FULLTEXT product_name_fulltext (product_name);

-- --------------------------------------------------------

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`idproduct_category`, `product_category_name`) VALUES
(1, 'Grundstück'),
(2, 'Haus'),
(3, 'Wohnung');

-- --------------------------------------------------------

--
-- Dumping data for table `user`
--

-- Passwort der User shopuser1 und shopuser2 ist jeweils geheim
INSERT INTO `user` (`iduser`, `first_name`, `last_name`, `nick_name`, `email`, `password`, `active`, `role`, `date_registered`) VALUES
(1, 'shop', 'user1', 'shopuser1', 'shopuser1@onlineshop.at', '$2y$10$z678OArUJa9.mmbbOma2EuFIxpTWF9FFOr4kN8goyrcXImk7GthAe', NULL, 'user', '2009-12-22 16:45:04'),
(2, 'shop', 'user2', 'shopuser2', 'shopuser2@onlineshop.at', '$2y$10$z678OArUJa9.mmbbOma2EuFIxpTWF9FFOr4kN8goyrcXImk7GthAe', 'bdb678676c3f52999829403edc381449', 'user', '2009-12-28 15:52:43');

-- --------------------------------------------------------

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`session_id`, `product_idproduct`, `product_name`, `price`, `quantity`) VALUES
  ('1', 1, 'Passivhaus', 300000.00, 1),
  ('1', 5, 'Talgrundstück', 10000.00, 1);



-- Anlegen des Users "onlineshop" wird bereits im Vagrantfile erledigt
-- Einkommentieren falls USER onlineshop bereits existiert
-- DROP USER IF EXISTS gibt es leider nicht
-- DROP USER 'onlineshop'@'localhost';
-- CREATE USER 'onlineshop'@'localhost' IDENTIFIED BY 'geheim';
-- GRANT USAGE ON onlineshop.* TO 'onlineshop'@'localhost' IDENTIFIED BY 'geheim' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
-- GRANT ALL PRIVILEGES ON `onlineshop`.* TO 'onlineshop'@'localhost';
