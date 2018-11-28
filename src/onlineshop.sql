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

--
-- Datenbank: `onlineshop`
--
DROP SCHEMA  IF EXISTS onlineshop;
CREATE SCHEMA IF NOT EXISTS `onlineshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `onlineshop`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `idaddress` bigint unsigned NOT NULL AUTO_INCREMENT,
  `address_line` varchar(100) NOT NULL,
  `user_iduser` bigint unsigned NOT NULL,
  `city_idcity` bigint unsigned NOT NULL,
  PRIMARY KEY (`idaddress`),
  KEY `userfk` (`user_iduser`),
  KEY `cityfk` (`city_idcity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `address`:
--   `city_idcity`
--       `city` -> `idcity`
--   `user_iduser`
--       `user` -> `iduser`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `session_id` varchar(250) NOT NULL,
  `product_idproduct` bigint NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  PRIMARY KEY (`session_id`,`product_idproduct`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

INSERT INTO `cart` (`session_id`, `product_idproduct`, `product_name`, `price`, `quantity`) VALUES
('1', 1, 'Passivhaus', 300000.00, 1),
('1', 5, 'Talgrundstück', 10000.00, 1),
('2', 5, 'Talgrundstück', 10000.00, 2);


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `idcity` bigint unsigned NOT NULL AUTO_INCREMENT,
  `PLZ` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `country_idcountry` bigint unsigned NOT NULL,
  PRIMARY KEY (`idcity`),
  KEY `countryfk` (`country_idcountry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `idcountry` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `ISOcode` char(2) NOT NULL,
  PRIMARY KEY (`idcountry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Daten der Tabelle `country`
--

insert into country set name = 'Austria', ISOcode = 'AT';
insert into country set name = 'Deutschland', ISOcode = 'DE';
insert into country set name = 'Schweiz', ISOcode = 'CH';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `idorders` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_iduser` bigint unsigned NOT NULL,
  `total_sum` decimal(10,2) NOT NULL,
  `date_ordered` datetime NOT NULL,
  `payment_type` varchar(45) NOT NULL,
  `payment_string1` varchar(50) NOT NULL,
  `payment_string2` varchar(50) DEFAULT NULL,
  `payment_string3` varchar(50) DEFAULT NULL,
  `instructions` varchar(100) NOT NULL,
  `delivery_address_line` varchar(150) NOT NULL,
  `delivery_zipcode` varchar(10) NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_country` varchar(100) NOT NULL,
  `billing_address_line` varchar(150) DEFAULT NULL,
  `billing_zipcode` varchar(10) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_country` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idorders`),
  KEY `user_fk` (`user_iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `order_item`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE IF NOT EXISTS `order_item` (
  `orders_idorders` bigint unsigned NOT NULL,
  `product_idproduct` bigint unsigned NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`orders_idorders`,`product_idproduct`),
  KEY `orders_fk` (`orders_idorders`),
  KEY `product_fk` (`product_idproduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `idpayment` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_iduser` bigint unsigned NOT NULL,
  `payment_type` varchar(45) NOT NULL,
  `payment_string1` varchar(50) NOT NULL,
  `payment_string2` varchar(50) DEFAULT NULL,
  `payment_string3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idpayment`),
  KEY `user_iduser` (`user_iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
-- Tabellenstruktur für Tabelle `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `product_category_name` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `short_description` varchar(250) NOT NULL,
  `long_description` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`idproduct`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `product`
--

INSERT INTO `product` (`idproduct`, `product_name`, `product_category_name`, `price`, `short_description`, `long_description`, `active`, `date_added`) VALUES
(1, 'Passivhaus', '', 300000.00, 'Haus mit U-Wert<10kWh/am2', 'Haus mit U-Wert<10kWh/am2\r\n20m2 Solaranlage\r\n40m2 Photovoltaik\r\n7500l Regenwassertank, ideal an kalten Wintertagen', 1, '2009-12-28 15:45:03'),
(2, 'Niedrigenergiehaus', '', 250000.00, 'Haus mit U-Wert<45kWh/am2', 'Haus mit U-Wert<45kWh/am2\r\n20 m2 Solaranlage, ideal an kalten Wintertagen', 0, '2009-12-28 15:45:44'),
(3, 'Seegrundstück', '', 200000.00, 'Seegrundstück am Attersee', 'Seegrundstueck am Attersee mit Seeblick und Bergblick, ideal fuer heisse Sommertage', 1, '2009-12-29 16:15:42'),
(4, 'Almgrundstück', '', 300000.00, 'Almgrundstück an einem Bergsee', 'Almgrundstück an einem Bergsee mit Zufahrtsstrasse, geschottert und Winterräumung, ideal fuer heiße Sommertage', 1, '2009-12-29 16:15:42'),
(5, 'Talgrundstück', '', 10000.00, 'Grundstück am Talende', 'Talgrundstück am Ende des Steyerlingtales, wenig Sonne, dafuer viel kaltes Bachwasser direkt neben dem Grundstück, ideal für heiße Sommertage', 1, '2009-12-29 16:15:42');

ALTER TABLE product ADD FULLTEXT INDEX product_full_idx1 (product_name);
ALTER TABLE product ADD FULLTEXT INDEX product_full_idx2 (product_name, short_description, long_description);


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `product_category`
--

DROP TABLE IF EXISTS `product_category`;
CREATE TABLE IF NOT EXISTS `product_category` (
  `idproduct_category` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_category_name` varchar(45) NOT NULL,
  PRIMARY KEY (`idproduct_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `product_category`
--

INSERT INTO `product_category` (`idproduct_category`, `product_category_name`) VALUES
(1, 'Grundstück'),
(2, 'Haus'),
(3, 'Wohnung');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
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
  `phone` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `user`
-- Passwords have been generated with password_hash() of PHP. Both passwords are "geheim"
--

INSERT INTO `user` (`iduser`, `first_name`, `last_name`, `nick_name`, `email`, `password`, `active`, `role`, `date_registered`, `phone`, `mobile`, `fax`) VALUES
(1, 'shop', 'user1', 'shopuser1', 'shopuser1@onlineshop.at', '$2y$10$IO4DIPMzORQgxQz0cYi/1.RtEWeG5AYK90PsrAq1xKVINXDsyoivG', NULL, 'user', '2009-12-22 16:45:04', NULL, NULL, NULL),
(2, 'shop', 'user2', 'shopuser2', 'shopuser2@onlineshop.at', '$2y$10$IO4DIPMzORQgxQz0cYi/1.RtEWeG5AYK90PsrAq1xKVINXDsyoivG', 'bdb678676c3f52999829403edc381449', 'user', '2009-12-28 15:52:43', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visit`
--

DROP TABLE IF EXISTS `visit`;
CREATE TABLE IF NOT EXISTS `visit` (
  `idvisit` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_iduser` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`idvisit`),
  KEY `fk_visits_users1` (`user_iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `visit`
--

insert into visit ( `ip_address`, `timestamp`, `user_iduser`) values
('10.28.14.123', NOW(), 1),
('10.28.14.124', NOW(), 2),
('10.28.14.123', NOW(), 1),
('10.28.14.123', NOW(), 2),
('10.28.14.125', NOW(), null);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_2` FOREIGN KEY (`city_idcity`) REFERENCES `city` (`idcity`),
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`);

--
-- Constraints der Tabelle `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `city_ibfk_1` FOREIGN KEY (`country_idcountry`) REFERENCES `country` (`idcountry`);

--
-- Constraints der Tabelle `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `user_fk` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`);

--
-- Constraints der Tabelle `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `product_fk` FOREIGN KEY (`product_idproduct`) REFERENCES `product` (`idproduct`),
  ADD CONSTRAINT `orders_fk` FOREIGN KEY (`orders_idorders`) REFERENCES `orders` (`idorders`);

--
-- Constraints der Tabelle `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `visits`
--
ALTER TABLE `visit`
  ADD CONSTRAINT `fk_visits_users1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
