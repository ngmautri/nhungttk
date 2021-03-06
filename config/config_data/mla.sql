-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Jan 2016 um 03:58
-- Server Version: 5.6.16
-- PHP-Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `mla`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_asset`
--

CREATE TABLE IF NOT EXISTS `mla_asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text,
  `category_id` int(11) NOT NULL,
  `group_id` int(11) unsigned zerofill DEFAULT NULL,
  `tag` varchar(45) DEFAULT NULL,
  `brand` varchar(45) DEFAULT NULL,
  `model` varchar(45) DEFAULT NULL,
  `serial` varchar(45) DEFAULT NULL,
  `origin` varchar(45) DEFAULT NULL,
  `received_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `location` varchar(45) NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `comment` text,
  `created_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `_idx` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=812 ;

--
-- Daten für Tabelle `mla_asset`
--

INSERT INTO `mla_asset` (`id`, `name`, `description`, `category_id`, `group_id`, `tag`, `brand`, `model`, `serial`, `origin`, `received_on`, `location`, `status`, `comment`, `created_on`) VALUES
(1, 'Rotory Cutter', 'Rotory Cutter', 26, 00000000000, '22-4-00174', 'DAYANG', 'RSD-100', '??', '0', '2013-12-05 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(2, 'Rotory Cutter', 'Rotory Cutter', 26, 00000000000, '22-4-00175', 'DAYANG', 'RSD-100', '??', '0', '2013-12-05 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(3, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00176', 'JUKI', 'MO-6916R', '8M0GD31025', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(4, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00177', 'JUKI', 'MO-6916R', '8M0GK31049', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(5, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00178', 'JUKI', 'MO-6916R', '8M0GJ31005', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(6, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00179', 'JUKI', 'MO-6916R', '8M0FH31112', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(7, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00180', 'JUKI', 'MO-6916R', '8M0GK31052', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(8, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00181', 'JUKI', 'MO-6916R', '8M0GK31051', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(9, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00182', 'JUKI', 'MO-6916R', '8M0GK31045', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(10, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00183', 'JUKI', 'MO-6916R', '8M0GD31024', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(11, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00184', 'JUKI', 'MO-6916R', '8M0GK31047', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(12, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00185', 'JUKI', 'MO-6916R', '8M0GK31050', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(13, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00186', 'JUKI', 'MO-6916R', '8M0GL31031', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(14, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00187', 'JUKI', 'MO-6916R', '8M0GJ31006', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(15, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00188', 'JUKI', 'MO-6916R', '8M0FH31114', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(16, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00189', 'JUKI', 'MO-6916R', '8M0GK31053', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(17, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00190', 'JUKI', 'MO-6916R', '8M0GK31048', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(18, 'Auto Label', 'Auto Label', 26, 00000000000, '22-4-00191', 'JUKI', 'AMS-210EN1510-HL', '2A3FD00008', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(19, 'Auto Label', 'Auto Label', 26, 00000000000, '22-4-00192', 'JUKI', 'AMS-210EN1510-HL', '2A3FD00009', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(20, 'Auto Label', 'Auto Label', 26, 00000000000, '22-4-00193', 'JUKI', 'AMS-210EN1510-HL', '2A3FD00007', '0', '2013-12-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(21, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00001', 'Komatsu', 'KHP-25', '1356546', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(22, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00002', 'Komatsu', 'KHP-25', '1408196', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(23, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00003', 'Komatsu', 'KHP-25', '-', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(24, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00004', 'Komatsu', 'KHP-25', '1356554', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(25, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00005', 'Komatsu', 'KHP-25', '-', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(26, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00006', 'Komatsu', 'KHP-25', '-', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(27, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00007', 'Komatsu', 'KHP-25', '-', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(28, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00008', 'Komatsu', 'KHP-25', '1356519', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(29, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00009', 'Komatsu', 'KHP-25', '1408236', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(30, 'Hand Lift', 'Hand Lift', 26, 00000000000, '11-1-00010', 'Komatsu', 'KHP-25', '1356521', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(31, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00001', 'SUNSTAR', 'KM-350B-7S', '71013552', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(32, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00002', 'SUNSTAR', 'KM-350B-7S', '10D06185', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(33, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00003', 'SUNSTAR', 'KM-350B-7S', '41102758', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(34, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00004', 'SUNSTAR', 'KM-350B-7S', '10D06468', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(35, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00005', 'SUNSTAR', 'KM-350B-7S', '80207614', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(36, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00006', 'SUNSTAR', 'KM-350B-7S', '71014053', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(37, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00007', 'SUNSTAR', 'KM-350B-7S', '71014010', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(38, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00008', 'SUNSTAR', 'KM-350B-7S', '80207592', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(39, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00009', 'SUNSTAR', 'KM-350B-7S', '80207589', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(40, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00010', 'SUNSTAR', 'KM-350B-7S', '80207653', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(41, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00011', 'SUNSTAR', 'KM-350B-7S', '80207564', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(42, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00012', 'SUNSTAR', 'KM-350B-7S', '71014046', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(43, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00013', 'SUNSTAR', 'KM-350B-7S', '71013550', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(44, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00014', 'SUNSTAR', 'KM-350B-7S', '71016702', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(45, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00015', 'SUNSTAR', 'KM-350B-7S', '80207585', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(46, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00016', 'SUNSTAR', 'KM-350B-7S', '80207632', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(47, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00017', 'SUNSTAR', 'KM-350B-7S', '71013545', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(48, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00018', 'SUNSTAR', 'KM-350B-7S', '71014055', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(49, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00019', 'SUNSTAR', 'KM-350B-7S', '80207636', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(50, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00020', 'SUNSTAR', 'KM-350B-7S', '41101685', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(51, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00021', 'SUNSTAR', 'KM-350B-7S', '71014050', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(52, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00022', 'SUNSTAR', 'KM-350B-7S', '80207630', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(53, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00023', 'SUNSTAR', 'KM-350B-7S', '80908420', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(54, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00024', 'SUNSTAR', 'KM-350B-7S', '10F10775', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(55, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00025', 'SUNSTAR', 'KM-350B-7S', '80207606', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(56, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00026', 'SUNSTAR', 'KM-350B-7S', '91105960', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(57, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00027', 'SUNSTAR', 'KM-350B-7S', '71016692', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(58, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00028', 'SUNSTAR', 'KM-350B-7S', '41102757', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(59, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00029', 'SUNSTAR', 'KM-350B-7S', '10D06181', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(60, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00030', 'SUNSTAR', 'KM-350B-7S', '71014021', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(61, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00031', 'SUNSTAR', 'KM-350B-7S', '71014035', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(62, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00032', 'SUNSTAR', 'KM-350B-7S', '71014008', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(63, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00033', 'SUNSTAR', 'KM-350B-7S', '80207652', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(64, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00034', 'SUNSTAR', 'KM-350B-7S', '10F10776', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(65, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00035', 'SUNSTAR', 'KM-350B-7S', '10DO6187', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(66, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00036', 'SUNSTAR', 'KM-350B-7S', '80207571', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(67, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00037', 'SUNSTAR', 'KM-350B-7S', '80207638', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(68, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00038', 'SUNSTAR', 'KM-350B-7S', '71014031', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(69, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00039', 'SUNSTAR', 'KM-350B-7S', '71014051', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(70, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00040', 'SUNSTAR', 'KM-350B-7S', '80207611', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(71, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00041', 'SUNSTAR', 'KM-350B-7S', '71016695', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(72, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00042', 'SUNSTAR', 'KM-350B-7S', '81103574', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(73, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00043', 'SUNSTAR', 'KM-350B-7S', '80207625', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(74, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00044', 'SUNSTAR', 'KM-350B-7S', '80207570', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(75, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00045', 'SUNSTAR', 'KM-350B-7S', '71014017', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(76, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00046', 'SUNSTAR', 'KM-350B-7S', '80207583', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(77, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00047', 'SUNSTAR', 'KM-350B-7S', '71014023', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(78, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00048', 'SUNSTAR', 'KM-350B-7S', '80207644', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(79, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00049', 'SUNSTAR', 'KM-350B-7S', '80109069', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(80, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00050', 'SUNSTAR', 'KM-350B-7S', '71014045', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(81, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00051', 'SUNSTAR', 'KM-350B-7S', '80207595', 'NA', '2015-12-31 16:47:17', 'PRO', NULL, NULL, '2015-12-31 10:47:17'),
(82, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00052', 'SUNSTAR', 'KM-350B-7S', '71014022', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(83, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00053', 'SUNSTAR', 'KM-350B-7S', '71014013', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(84, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00054', 'SUNSTAR', 'KM-350B-7S', '80207622', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(85, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00055', 'SUNSTAR', 'KM-350B-7S', '91105959', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(86, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00056', 'SUNSTAR', 'KM-350B-7S', '80109073', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(87, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00057', 'SUNSTAR', 'KM-350B-7S', '71013072', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(88, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00058', 'SUNSTAR', 'KM-350B-7S', '10F10779', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(89, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00059', 'SUNSTAR', 'KM-350B-7S', '81103579', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(90, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00060', 'SUNSTAR', 'KM-350B-7S', '80207598', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(91, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00061', 'SUNSTAR', 'KM-350B-7S', '10D06184', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(92, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00062', 'SUNSTAR', 'KM-350B-7S', '71014014', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(93, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00063', 'SUNSTAR', 'KM-350B-7S', '71013543', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(94, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00064', 'SUNSTAR', 'KM-350B-7S', '71013546', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(95, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00065', 'SUNSTAR', 'KM-350B-7S', '71016696', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(96, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00066', 'SUNSTAR', 'KM-350B-7S', '71014039', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(97, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00067', 'SUNSTAR', 'KM-350B-7S', '71014043', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(98, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00068', 'SUNSTAR', 'KM-350B-7S', '80207627', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(99, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00069', 'SUNSTAR', 'KM-350B-7S', '71014025', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(100, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00070', 'SUNSTAR', 'KM-350B-7S', '80207640', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(101, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00071', 'SUNSTAR', 'KM-350B-7S', '10D06177', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(102, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00072', 'SUNSTAR', 'KM-350B-7S', '71013553', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(103, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00073', 'SUNSTAR', 'KM-350B-7S', '80207567', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(104, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00074', 'JUKI', 'DLN-9010A-SH', '2D3DE00126', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(105, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00075', 'JUKI', 'DLN-9010A-SH', '2D3EF00190', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(106, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00076', 'JUKI', 'DLN-9010A-SH', '2D3ED00022', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(107, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00077', 'JUKI', 'DLN-9010A-SH', '2D3EB00172', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(108, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00078', 'JUKI', 'DLN-9010A-SH', '2D3EE00264', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(109, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00079', 'JUKI', 'DLN-9010A-SH', '2D3EF00200', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(110, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00080', 'JUKI', 'DLN-9010A-SH', '2D3EF00091', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(111, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00081', 'JUKI', 'DLN-9010A-SH', '2D3ED00095', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(112, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00082', 'JUKI', 'DLN-9010A-SH', '2D3EE00156', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(113, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00083', 'JUKI', 'DLN-9010A-SH', '2D3ED00101', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(114, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00084', 'JUKI', 'DLN-9010A-SH', '2D3EF00115', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(115, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00085', 'JUKI', 'DLN-9010A-SH', '2D3EB00296', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(116, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00086', 'JUKI', 'DLN-9010A-SH', '2D3ED00102', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(117, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00087', 'JUKI', 'DLN-9010A-SH', '2D3EC00058', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(118, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00088', 'JUKI', 'DLN-9010A-SH', '2D3EB00165', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(119, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00089', 'JUKI', 'DLN-9010A-SH', '2D3EF00033', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(120, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00090', 'JUKI', 'DLN-9010A-SH', '2D3EF00043', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(121, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00091', 'JUKI', 'DLN-9010A-SH', '2D3EE00040', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(122, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00092', 'JUKI', 'DLN-9010A-SH', '2D3EE00147', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(123, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00093', 'JUKI', 'DLN-9010A-SH', '2D3ED00021', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(124, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00094', 'JUKI', 'DLN-9010A-SH', '2D3EF00218', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(125, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00095', 'SUNSTAR', 'KM -250B', '80906446', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(126, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00096', 'SUNSTAR', 'KM -250B', '80906459', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(127, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00097', 'SUNSTAR', 'KM -250B', '80906486', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(128, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00098', 'SUNSTAR', 'KM -250B', '80906440', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(129, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00099', 'SUNSTAR', 'KM -250B', '71208949', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(130, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00100', 'SUNSTAR', 'KM -250B', '71208950', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(131, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00101', 'SUNSTAR', 'KM -250B', '80906492', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(132, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00102', 'SUNSTAR', 'KM -250B', '71208975', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(133, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00103', 'SUNSTAR', 'KM -250B', '80906481', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(134, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00104', 'SUNSTAR', 'KM -250B', '80906487', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(135, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00105', 'JUKI', 'DLU-5490N-7', 'DLUYE18421', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(136, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00106', 'JUKI', 'DLU-5490N-7', 'DLUBH24876', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(137, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00107', 'SUNSTAR', 'KM-797BL-7S', '80600385', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(138, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00108', 'SUNSTAR', 'KM-797BL-7S', '80209746', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(139, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00109', 'SUNSTAR', 'KM-797BL-7S', '80209144', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(140, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00110', 'SUNSTAR', 'KM-797BL-7S', '80207776', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(141, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00111', 'SUNSTAR', 'KM-797BL-7S', '80209747', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(142, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00112', 'SUNSTAR', 'KM-797BL-7S', '71212124', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(143, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00113', 'SUNSTAR', 'KM-797BL-7S', '10F00720', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(144, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00114', 'SUNSTAR', 'KM-797BL-7S', '91000015', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(145, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00115', 'SUNSTAR', 'KM-797BL-7S', '71213038', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(146, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00116', 'SUNSTAR', 'KM-797BL-7S', '10F00702', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(147, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00117', 'SUNSTAR', 'KM-797BL-7S', '71100184', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(148, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00118', 'SUNSTAR', 'KM-757BL-7S', '80209151', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(149, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00119', 'SUNSTAR', 'KM-757BL-7S', '71013569', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(150, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00120', 'SUNSTAR', 'KM-757BL-7S', '71000088', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(151, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00121', 'SUNSTAR', 'KM-757BL-7S', '71000089', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(152, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00122', 'SUNSTAR', 'KM-757BL-7S', '71000091', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(153, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00123', 'SUNSTAR', 'KM-757BL-7S', '80207226', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(154, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00124', 'SUNSTAR', 'KM-757BL-7S', '80114286', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(155, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00125', 'SUNSTAR', 'KM-757BL-7S', '71014075', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(156, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00126', 'SUNSTAR', 'KM-757BL-7S', '71015646', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(157, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00127', 'SUNSTAR', 'KM-757BL-7S', '71100088', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(158, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00128', 'SUNSTAR', 'KM-757BL-7S', '71000093', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(159, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00129', 'SUNSTAR', 'KM-757BL-7S', '80207232', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(160, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00130', 'SUNSTAR', 'KM-757BL-7S', '70400214', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(161, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00131', 'SUNSTAR', 'KM-757BL-7S', '71014073', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(162, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00132', 'SUNSTAR', 'KM-757BL-7S', '80206126', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(163, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00133', 'SUNSTAR', 'KM-757BL-7S', '80207233', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(164, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00134', 'SUNSTAR', 'KM-757BL-7S', '71100086', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(165, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00135', 'SUNSTAR', 'KM-757BL-7S', '71001400', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(166, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00136', 'SUNSTAR', 'KM-757BL-7S', '71001405', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(167, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00137', 'SUNSTAR', 'KM-757BL-7S', '71100082', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(168, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00138', 'SUNSTAR', 'KM-757BL-7S', '80207229', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(169, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00139', 'SUNSTAR', 'KM-757BL-7S', '71015240', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(170, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00140', 'SUNSTAR', 'KM-757BL-7S', '80209150', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(171, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00141', 'SUNSTAR', 'KM-757BL-7S', '71001409', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(172, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00142', 'SUNSTAR', 'KM-757BL-7S', '71100089', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(173, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00143', 'SUNSTAR', 'KM-757BL-7S', '70400806', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(174, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00144', 'SUNSTAR', 'KM-757BL-7S', '70400212', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(175, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00145', 'SUNSTAR', 'KM-757BL-7S', '70400280', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(176, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00146', 'SUNSTAR', 'KM-757BL-7S', '70400280', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(177, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00147', 'JUKI', 'LK-1900A-HS', '2L1EH02004', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(178, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00148', 'JUKI', 'LK-1900A-HS', '2L1EH01996', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(179, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00149', 'JUKI', 'LK-1900A-HS', '2L1EH02053', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(180, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00150', 'JUKI', 'LK-1900A-HS', '2L1ED00315', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(181, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00151', 'JUKI', 'LK-1900A-HS', '2L1ED00323', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(182, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00152', 'JUKI', 'LK-1900A-HS', '2L1EC01887', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(183, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00153', 'JUKI', 'LK-1900A-HS', '2L1EC02523', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(184, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00154', 'JUKI', 'LK-1900A-HS', '2L1EH02031', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(185, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00155', 'JUKI', 'LK-1900A-HS', '2L1EH01985', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(186, '1-Needle sewing machine', '1-Needle sewing machine', 26, 00000000000, '22-4-00156', 'JUKI', 'LK-1900A-HS', '2L1EH02049', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(187, '3-Need, feed off arm machine', '3-Need, feed off arm machine', 26, 00000000000, '22-4-00157', 'JUKI', '35800DZ36', 'KK1895356', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(188, '3-Need, feed off arm machine', '3-Need, feed off arm machine', 26, 00000000000, '22-4-00158', 'JUKI', '35800DZ36', 'KF1894210', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(189, '3-Need, feed off arm machine', '3-Need, feed off arm machine', 26, 00000000000, '22-4-00159', 'JUKI', '35800DZ36', 'KK1895333', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(190, '3-Need, feed off arm machine', '3-Need, feed off arm machine', 26, 00000000000, '22-4-00160', 'JUKI', 'MS3580', '2M8DA00010', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(191, '3-Need, feed off arm machine', '3-Need, feed off arm machine', 26, 00000000000, '22-4-00161', 'JUKI', 'MS3580', '2M8CF00037', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(192, '3-Need, feed off arm machine', '3-Need, feed off arm machine', 26, 00000000000, '22-4-00162', 'JUKI', 'MS3580', '2M8CF00042', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(193, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00163', 'KANSAI', 'DLR-1508PR', '1108671', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(194, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00164', 'KANSAI', 'DLR-1508PR', '1110715', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(195, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00165', 'KANSAI', 'DLR-1508PR', '1110717', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(196, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00166', 'KANSAI', 'DLR-1508PR', '1108635', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(197, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00167', 'KANSAI', 'DLR-1508PR', '727400', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(198, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00168', 'KANSAI', 'DLR-1508PR', '727337', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(199, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00169', 'KANSAI', 'DLR-1508PR', '727378', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(200, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00170', 'KANSAI', 'DLR-1508PR', '1108646', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(201, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00171', 'KANSAI', 'DFB-1412P', '718448', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(202, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00172', 'KANSAI', 'DFB-1412P', '718436', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(203, 'Kansai machine', 'Kansai machine', 26, 00000000000, '22-4-00173', 'KANSAI', 'DFB-1412P', '718437', '0', '2013-12-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(204, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00194', 'JUKI', 'DMN-5420N-7-WB', '2D5GJ00042', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(205, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00195', 'JUKI', 'DMN-5420N-7-WB', '2D5GJ00048', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(206, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00196', 'JUKI', 'DMN-5420N-7-WB', '2D5GJ00041', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(207, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00197', 'JUKI', 'DMN-5420N-7-WB', '2D5GJ00037', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(208, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00198', 'JUKI', 'DMN-5420N-7-WB', '2D5GJ00051', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(209, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00199', 'JUKI', 'DMN-5420N-7-WB', '2D5GJ00046', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(210, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00200', 'JUKI', 'MO-6916R', '8M0GK31046', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(211, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00201', 'JUKI', 'MO-6916R', '8M0FH31117', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(212, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00202', 'JUKI', 'MO-6916R', '8M0GL31034', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(213, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00203', 'JUKI', 'MO-6916R', '8M0FH31111', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(214, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00204', 'JUKI', 'MO-6916R', '8M0GL31030', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(215, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00205', 'JUKI', 'MO-6916R', '8M0Gk31044', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(216, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00206', 'JUKI', 'MO-6916R', '8M0GK31055', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(217, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00207', 'JUKI', 'MO-6916R', '8M0GK31054', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(218, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00208', 'JUKI', 'MO-6916R', '8M0GL31035', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(219, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00209', 'JUKI', 'MO-6916R', '8M0GK31041', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(220, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00210', 'JUKI', 'MO-6916R', '8M0GK31043', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(221, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00211', 'JUKI', 'MO-6916R', '8M0GL31033', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(222, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00212', 'JUKI', 'MO-6916R', '8M0GL31032', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(223, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00213', 'JUKI', 'MO-6916R', '8M0FD31169', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(224, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00214', 'JUKI', 'MO-6916R', '8M0GL31069', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(225, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00215', 'JUKI', 'MO-6916R', '8M0GK31042', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(226, 'Auto Velcro', 'Auto Velcro', 26, 00000000000, '22-4-00216', 'JUKI', 'AMS-210EN1306HS', '2A3GJ00054', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(227, 'Auto Velcro', 'Auto Velcro', 26, 00000000000, '22-4-00217', 'JUKI', 'AMS-210EN1306HS', '2A3GG00180', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(228, 'Auto Velcro', 'Auto Velcro', 26, 00000000000, '22-4-00218', 'JUKI', 'AMS-210EN1306HS', '2A3GG00181', '0', '2013-12-25 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(229, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00219', 'JUKI', 'MO-6916R', '8M0GL31062', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(230, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00220', 'JUKI', 'MO-6916R', '8M0GL31159', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(231, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00221', 'JUKI', 'MO-6916R', '8M0GL31066', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(232, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00222', 'JUKI', 'MO-6916R', '8M0GL31156', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(233, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00223', 'JUKI', 'MO-6916R', '8M0GL31063', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(234, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00224', 'JUKI', 'MO-6916R', '8M0GL31064', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(235, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00225', 'JUKI', 'MO-6916R', '8M0GL31160', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(236, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00226', 'JUKI', 'MO-6916R', '8M0FH31110', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(237, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00227', 'JUKI', 'MO-6916R', '8M0GL31158', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(238, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00228', 'JUKI', 'MO-6916R', '8M0GL31068', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(239, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00229', 'JUKI', 'MO-6916R', '8M0GL31157', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(240, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00230', 'JUKI', 'MO-6916R', '8M0GL31151', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(241, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00231', 'JUKI', 'MO-6916R', '8M0GL31070', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(242, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00232', 'JUKI', 'MO-6916R', '8M0GL31153', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(243, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00233', 'JUKI', 'MO-6916R', '8M0GL31067', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(244, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00234', 'JUKI', 'MO-6916R', '8M0GL31152', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(245, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00235', 'JUKI', 'MO-6916R', '8M0GL31061', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(246, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00236', 'JUKI', 'MO-6916R', '8M0GL31065', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(247, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00237', 'JUKI', 'MO-6916R', '8M0GL31155', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(248, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00238', 'JUKI', 'MO-6916R', '8M0GL31150', '0', '2014-01-27 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(249, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00239', 'Grand', 'T22C', '22110300145', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(250, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00240', 'Grand', 'T22C', '2211030043', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(251, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00241', 'Grand', 'T22C', '22804057', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(252, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00242', 'Grand', 'T22C', '2211030080', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(253, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00243', 'Grand', 'T22C', '22110300141', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(254, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00244', 'Grand', 'T22C', '22804036', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(255, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00245', 'Grand', 'T22C', '22804102', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(256, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00246', 'Grand', 'T22C', '22110300147', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(257, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00247', 'Grand', 'T22C', '221311168', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(258, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00248', 'Grand', 'T22C', '221311188', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(259, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00249', 'Grand', 'T22C', '221311052', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(260, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00250', 'Grand', 'T22C', '221311023', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(261, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00251', 'Grand', 'T22C', '221311179', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(262, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00252', 'Grand', 'T22C', '221311181', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(263, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00253', 'Grand', 'T22C', '221311190', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(264, 'Trmmimg MLC', 'Trmmimg MLC', 26, 00000000000, '22-4-00254', 'Grand', 'T22C', '221311189', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(265, ' Cleaning machine', ' Cleaning machine', 26, 00000000000, '22-4-00255', 'Gold Eagle', 'DL-1380A', '0', '0', '2014-03-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(266, 'Quick up 12', 'Quick up 12', 26, 00000000000, '11-1-00014', 'Haulotte', 'QU12AC', '131453', '0', '2014-03-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(267, 'Stacker', 'Stacker', 26, 00000000000, '11-1-00011', 'JUNGHEINRICH', 'ERC-214', '90437190', '0', '2014-03-20 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(268, 'Stacker', 'Stacker', 26, 00000000000, '11-1-00012', 'JUNGHEINRICH', 'ERC-214', '90437191', '0', '2014-03-20 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(269, 'Fork Lift', 'Fork Lift', 26, 00000000000, '11-1-00013', 'JUNGHEINRICH', 'EFG-320', 'FN463131', '0', '2014-03-20 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(270, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00256', 'SUNSTAR', 'KM-757BL-7S', '80209153', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00');
INSERT INTO `mla_asset` (`id`, `name`, `description`, `category_id`, `group_id`, `tag`, `brand`, `model`, `serial`, `origin`, `received_on`, `location`, `status`, `comment`, `created_on`) VALUES
(271, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00257', 'SUNSTAR', 'KM-757BL-7S', '70400210', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(272, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00258', 'SUNSTAR', 'KM-757BL-7S', '71015643', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(273, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00259', 'SUNSTAR', 'KM-757BL-7S', '71000083', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(274, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00260', 'SUNSTAR', 'KM-757BL-7S', '71014077', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(275, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00261', 'SUNSTAR', 'KM-757BL-7S', '71000086', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(276, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00262', 'SUNSTAR', 'KM-757BL-7S', '70400817', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(277, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00263', 'SUNSTAR', 'KM-757BL-7S', '80206128', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(278, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00264', 'SUNSTAR', 'KM-757BL-7S', '71100084', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(279, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00265', 'SUNSTAR', 'KM-757BL-7S', '70400816', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(280, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00266', 'SUNSTAR', 'KM-757BL-7S', '71000080', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(281, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00267', 'SUNSTAR', 'KM-757BL-7S', '70400219', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(282, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00268', 'SUNSTAR', 'KM-757BL-7S', '71000087', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(283, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00269', 'SUNSTAR', 'KM-757BL-7S', '71000094', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(284, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00270', 'SUNSTAR', 'KM-757BL-7S', '71014546', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(285, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00271', 'SUNSTAR', 'KM-757BL-7S', '80209156', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(286, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00272', 'SUNSTAR', 'KM-757BL-7S', '80206125', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(287, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00273', 'SUNSTAR', 'KM-757BL-7S', '71100083', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(288, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00274', 'SUNSTAR', 'KM-757BL-7S', '80209155', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(289, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00275', 'SUNSTAR', 'KM-757BL-7S', '71015235', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(290, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00276', 'SUNSTAR', 'KM-757BL-7S', '71000092', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(291, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00277', 'SUNSTAR', 'KM-757BL-7S', '80114284', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(292, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00278', 'SUNSTAR', 'KM-757BL-7S', '71001407', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(293, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00279', 'SUNSTAR', 'KM-757BL-7S', '71100118', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(294, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00280', 'SUNSTAR', 'KM-797BL-7S', '71110831', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(295, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00281', 'SUNSTAR', 'KM-797BL-7S', '71000197', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(296, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00282', 'SUNSTAR', 'KM-797BL-7S', '71000182', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(297, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00283', 'SUNSTAR', 'KM-757BL-7S', '80209152', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(298, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00284', 'SUNSTAR', 'KM-757BL-7S', '71100085', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(299, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00285', 'SUNSTAR', 'KM-757BL-7S', '70400211', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(300, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00286', 'SUNSTAR', 'KM-757BL-7S', '80207227', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(301, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00287', 'SUNSTAR', 'KM-757BL-7S', '71015647', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(302, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00288', 'SUNSTAR', 'KM-757BL-7S', '80206127', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(303, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00289', 'SUNSTAR', 'KM-757BL-7S', '71000085', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(304, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00290', 'SUNSTAR', 'KM-797BL-7S', '71000193', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(305, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00291', 'SUNSTAR', 'KM-797BL-7S', '71000190', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(306, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00292', 'SUNSTAR', 'KM-797BL-7S', '80300175', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(307, '2-Needle sewing machine', '2-Needle sewing machine', 26, 00000000000, '22-4-00293', 'SUNSTAR', 'KM-797BL-7S', '10F01211', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(308, '1-Needle, post-bed, unison-feed, lockstitch', '1-Needle, post-bed, unison-feed, lockstitch', 26, 00000000000, '22-4-00294', 'JUKI', 'DSC-246-7', '3D9EF01030', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(309, '1-Needle, post-bed, unison-feed, lockstitch', '1-Needle, post-bed, unison-feed, lockstitch', 26, 00000000000, '22-4-00295', 'JUKI', 'DSC-246-7', '3D9EF01028', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(310, '1-Needle, post-bed, unison-feed, lockstitch', '1-Needle, post-bed, unison-feed, lockstitch', 26, 00000000000, '22-4-00296', 'JUKI', 'DSC-246-7', '3D9EF01031', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(311, '1-Needle, post-bed, unison-feed, lockstitch', '1-Needle, post-bed, unison-feed, lockstitch', 26, 00000000000, '22-4-00297', 'JUKI', 'DSC-246-7', '3D9EF01032', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(312, 'Multi Needle machine', 'Multi Needle machine', 26, 00000000000, '22-4-00298', 'KANSAI', 'DLR-1508PR', '727405', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(313, 'Multi Needle machine', 'Multi Needle machine', 26, 00000000000, '22-4-00299', 'KANSAI', 'DLR-1508PR', '727405', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(314, 'Multi Needle machine', 'Multi Needle machine', 26, 00000000000, '22-4-00300', 'KANSAI', 'DLR-1508PR', '727409', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(315, 'Multi Needle machine', 'Multi Needle machine', 26, 00000000000, '22-4-00301', 'KANSAI', 'DLR-1508PR', '727394', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(316, 'Multi Needle machine', 'Multi Needle machine', 26, 00000000000, '22-4-00302', 'KANSAI', 'DLR-1508PR', '727352', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(317, 'Button  presser machine', 'Button  presser machine', 26, 00000000000, '22-4-00303', 'YKK', 'N6NCE', '6336', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(318, 'Button sewing machine', 'Button sewing machine', 26, 00000000000, '22-4-00304', 'SUNSTAR', 'SPS/D-B1202-02', '10F00850', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(319, ' Feed off arm machine', ' Feed off arm machine', 26, 00000000000, '22-4-00305', 'JUKI', '35800DZ36', 'KF1894198', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(320, ' Feed off arm machine', ' Feed off arm machine', 26, 00000000000, '22-4-00306', 'JUKI', '35800DZ36', 'KK1895345', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(321, ' Feed off arm machine', ' Feed off arm machine', 26, 00000000000, '22-4-00307', 'JUKI', '35800DZ36', 'KK1895349', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(322, ' Feed off arm machine', ' Feed off arm machine', 26, 00000000000, '22-4-00308', 'JUKI', 'US-35800DZ36', 'KF1894212', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(323, ' Feed off arm machine', ' Feed off arm machine', 26, 00000000000, '22-4-00309', 'JUKI', '35800DZ36', 'KK1895334', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(324, 'Pattern M/C', 'Pattern M/C', 26, 00000000000, '22-4-00310', 'SUNSTAR', 'SPS/B-B1254HA-22', '35100309', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(325, ' cover stitch machine ', ' cover stitch machine ', 26, 00000000000, '22-4-00311', 'RIMOLDI', '264-42-4LM', '821121', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(326, ' cover stitch machine ', ' cover stitch machine ', 26, 00000000000, '22-4-00312', 'RIMOLDI', '264-42-4LM', '0867517', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(327, 'Button hole machine', 'Button hole machine', 26, 00000000000, '22-4-00313', 'JUKI', 'MEB-3200J', '2M5EL00031', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(328, 'Button hole machine', 'Button hole machine', 26, 00000000000, '22-4-00314', 'JUKI', 'MEB-3200J', '2M5EB00067', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(329, '2-needle bactack machine', '2-needle bactack machine', 26, 00000000000, '22-4-00315', 'JUKI', 'LK1960', '2L1EG00790', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(330, 'Veit iron Table', 'Veit iron Table', 26, 00000000000, '22-4-00316', 'VEIT', 'SC030066', '050062011C', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(331, 'Veit iron steam genarator', 'Veit iron steam genarator', 26, 00000000000, '22-4-00317', 'VEIT', 'SC010009', '040007811C', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(332, '1-needle bactack machine', '1-needle bactack machine', 26, 00000000000, '22-4-00318', 'JUKI', 'AMS-206A', 'S01805', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(333, '1-needle bactack machine', '1-needle bactack machine', 26, 00000000000, '22-4-00319', 'JUKI', 'AMS-206A', 'S01559', '0', '2014-05-16 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(334, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00320', 'JUKI', 'DMN-5420N-7-WB', '2D5HA 00018', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(335, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00321', 'JUKI', 'DMN-5420N-7-WB', '2D5HA 00021', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(336, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00322', 'JUKI', 'DMN-5420N-7-WB', '2D5HA 00020', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(337, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00323', 'JUKI', 'DMN-5420N-7-WB', '2D5HA 00019', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(338, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00324', 'JUKI', 'DMN-5420N-7-WB', '2D5HA 00022', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(339, '1-Needle lockstich, vertical edge trmmer', '1-Needle lockstich, vertical edge trmmer', 26, 00000000000, '22-4-00325', 'JUKI', 'DMN-5420N-7-WB', '2D5HA 00023', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(340, '5TH Overlock', '5TH Overlock', 26, 00000000000, '22-4-00326', 'JUKI', 'MO- 6716 DA', '8MOHA 12148', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(341, '5TH Overlock', '5TH Overlock', 26, 00000000000, '22-4-00327', 'JUKI', 'MO- 6716 DA', '8MOHA 12137', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(342, '5TH Overlock', '5TH Overlock', 26, 00000000000, '22-4-00328', 'JUKI', 'MO- 6716 DA', '8MOHA 12143', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(343, '5TH Overlock', '5TH Overlock', 26, 00000000000, '22-4-00329', 'JUKI', 'MO- 6716 DA', '8MOHA 12146', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(344, '5TH Overlock', '5TH Overlock', 26, 00000000000, '22-4-00330', 'JUKI', 'MO- 6716 DA', '8MOHA 12147', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(345, '5TH Overlock', '5TH Overlock', 26, 00000000000, '22-4-00331', 'JUKI', 'MO- 6716 DA', '8MOHA 12673', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(346, 'Bartack M/C', 'Bartack M/C', 26, 00000000000, '22-4-00332', 'JUKI', 'LK- 1900A-HS', '2L1HD 00481', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(347, 'Bartack M/C', 'Bartack M/C', 26, 00000000000, '22-4-00333', 'JUKI', 'LK- 1900A-HS', '2L1HD 00485', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(348, 'Bartack M/C', 'Bartack M/C', 26, 00000000000, '22-4-00334', 'JUKI', 'LK- 1900A-HS', '2L1HD 00434', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(349, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00335', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13755', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(350, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00336', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13737', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(351, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00337', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13733', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(352, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00338', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13738', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(353, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00339', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13753', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(354, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00340', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13724', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(355, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00341', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13704', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(356, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00342', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13741', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(357, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00343', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13754', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(358, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00344', 'JUKI', 'DDL- 9000BSH-WB', '8DOHD 13736', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(359, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00345', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00390', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(360, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00346', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00367', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(361, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00347', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00388', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(362, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00348', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00371', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(363, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00349', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00298', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(364, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00350', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00289', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(365, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00351', 'JUKI', 'DLN- 9010A SH', '2D3GJ 00378', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(366, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00352', 'JUKI', 'DLN- 9010A SH', '2D3GK 00273', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(367, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00353', 'JUKI', 'DLN- 9010A SH', '2D3GK 00382', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(368, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00354', 'JUKI', 'DLN- 9010A SH', '2D3GK 00270 ', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(369, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00355', 'JUKI', 'DLN- 9010A SH', '2D3GK 00366', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(370, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00356', 'JUKI', 'DLN- 9010A SH', '2D3GK 00296', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(371, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00357', 'JUKI', 'DLN- 9010A SH', '2D3GK 00386', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(372, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00358', 'JUKI', 'DLN- 9010A SH', '2D3GK 00290', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(373, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00359', 'JUKI', 'DLN- 9010A SH', '2D3GK 00276', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(374, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00360', 'JUKI', 'DLN- 9010A SH', '2D3GK 00284', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(375, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00361', 'JUKI', 'DLN- 9010A SH', '2D3GK 00286', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(376, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00362', 'JUKI', 'DLN- 9010A SH', '2D3GK 00274', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(377, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00363', 'JUKI', 'DLN- 9010A SH', '2D3GK 00267', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(378, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00364', 'JUKI', 'DLN- 9010A SH', '2D3GK 00266', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(379, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00365', 'JUKI', 'DLN- 9010A SH', '2D3GK 00287', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(380, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00366', 'JUKI', 'DLN- 9010A SH', '2D3GK 00285', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(381, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00367', 'JUKI', 'DLN- 9010A SH', '2D3GK 00291', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(382, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00368', 'JUKI', 'DLN- 9010A SH', '2D3GK 00297', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(383, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00369', 'JUKI', 'DLN- 9010A SH', '2D3GK 00301', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(384, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00370', 'JUKI', 'DLN- 9010A SH', '2D3GK 00288', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(385, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00371', 'JUKI', 'DLN- 9010A SH', '2D3GK 00391', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(386, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00372', 'JUKI', 'DLN- 9010A SH', '2D3GK 00300', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(387, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00373', 'JUKI', 'DLN- 9010A SH', '2D3GK 00271', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(388, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00374', 'JUKI', 'DLN- 9010A SH', '2D3GK 00275', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(389, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00375', 'JUKI', 'DLN- 9010A SH', '2D3GK 00272', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(390, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00376', 'JUKI', 'DLN- 9010A SH', '2D3GK 00392', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(391, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00377', 'JUKI', 'DLN- 9010A SH', '2D3GK 00383', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(392, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00378', 'JUKI', 'DLN- 9010A SH', '2D3GK 00364', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(393, '1-Needle lockstitch sweing M/C', '1-Needle lockstitch sweing M/C', 26, 00000000000, '22-4-00379', 'JUKI', 'DLN- 9010A SH', '2D3GK 00302', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(394, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00380', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12216', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(395, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00381', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12203', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(396, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00382', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12233', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(397, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00383', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12197', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(398, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00384', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12209', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(399, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00385', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12279', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(400, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00386', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE12200', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(401, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00387', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 11142', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(402, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00388', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12194', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(403, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00389', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12225', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(404, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00390', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12230', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(405, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00391', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12231', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(406, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00392', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12210', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(407, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00393', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12196', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(408, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00394', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12198', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(409, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00395', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12207', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(410, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00396', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12206', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(411, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00397', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12227', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(412, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00398', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12237', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(413, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00399', 'JUKI', 'LK- 3588AGF-7-WB', '8L3HE 12222', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(414, 'Flat-Bed, top & Bottom Coverstitch M/C', 'Flat-Bed, top & Bottom Coverstitch M/C', 26, 00000000000, '22-4-00400', 'JUKI', 'MF- 7523DC11-B64', '8M4HA 41320', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(415, 'Flat-Bed, top & Bottom Coverstitch M/C', 'Flat-Bed, top & Bottom Coverstitch M/C', 26, 00000000000, '22-4-00401', 'JUKI', 'MF- 7523DC11-B64', '8M4HA 41323', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(416, 'Flat-Bed, top & Bottom Coverstitch M/C', 'Flat-Bed, top & Bottom Coverstitch M/C', 26, 00000000000, '22-4-00402', 'JUKI', 'MF- 7523DC11-B64', '8M4HA 41372', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(417, 'Flat-Bed, top & Bottom Coverstitch M/C', 'Flat-Bed, top & Bottom Coverstitch M/C', 26, 00000000000, '22-4-00403', 'JUKI', 'MF- 7523DC11-B65', '8M4HA 41376', '0', '2014-07-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(418, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00404', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00491', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(419, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00405', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00093', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(420, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00406', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00161', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(421, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00407', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00187', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(422, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00408', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00423', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(423, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00409', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00207', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(424, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00410', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00167', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(425, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00411', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00096', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(426, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00412', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00180', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(427, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00413', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00156', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(428, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00414', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00095', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(429, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00415', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00420', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(430, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00416', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00413', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(431, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00417', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00209', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(432, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00418', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00108', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(433, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00419', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00414', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(434, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00420', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00406', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(435, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00421', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00402', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(436, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00422', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00181', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(437, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00423', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00177', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(438, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00424', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00094', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(439, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00425', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00417', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(440, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00426', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00159', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(441, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00427', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00304', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(442, '1-Needle sewing M/C', '1-Needle sewing M/C', 26, 00000000000, '22-4-00428', 'JUKI', 'DLN- 9010A-SH', '2D3GJ00088', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(443, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00429', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13679', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(444, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00430', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13713 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(445, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00431', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13671 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(446, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00432', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13721 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(447, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00433', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13680 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(448, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00434', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13677 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(449, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00435', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13673 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(450, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00436', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13744 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(451, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00437', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13674 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(452, '1-Needle lockstitch sewing M/C', '1-Needle lockstitch sewing M/C', 26, 00000000000, '22-4-00438', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHD13670 ', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(453, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00439', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11302', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(454, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00440', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11849', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(455, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00441', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11850', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(456, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00442', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11278', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(457, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00443', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11851', '', '2015-12-31 16:40:15', 'PRO', NULL, NULL, '2015-12-31 10:40:15'),
(458, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00444', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11310', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(459, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00445', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11290', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(460, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00446', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11847', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(461, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00447', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11304', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(462, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00448', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11854', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(463, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00449', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE12567', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(464, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00450', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE12555', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(465, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00451', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11852', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(466, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00452', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11848', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(467, '2-Needle lockstitch M/C', '2-Needle lockstitch M/C', 26, 00000000000, '22-4-00453', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE11280', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(468, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00454', 'JUKI', 'MO- 6716DA', '8MOHA12674', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(469, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00455', 'JUKI', 'MO- 6716DA', '8MOHA16129', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(470, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00456', 'JUKI', 'MO- 6716DA', '8MOHA16128', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(471, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00457', 'JUKI', 'MO- 6716DA', '8MOHA16127', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(472, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00458', 'JUKI', 'MO- 6716DA', '8MOHA16126', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(473, '5T/H Overlock', '5T/H Overlock', 26, 00000000000, '22-4-00459', 'JUKI', 'MO- 6716DA', '8MOHA16130', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(474, 'Flat bed & Bottom coverstitch M/C', 'Flat bed & Bottom coverstitch M/C', 26, 00000000000, '22-4-00460', 'JUKI', 'MF- 7523DC11-B64', '8M4HD41336', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(475, 'Flat bed & Bottom coverstitch M/C', 'Flat bed & Bottom coverstitch M/C', 26, 00000000000, '22-4-00461', 'JUKI', 'MF- 7523DC11-B65', '8M4HD41368', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(476, 'Auto Label', 'Auto Label', 26, 00000000000, '22-4-00462', 'JUKI', 'AMS- 210EN-HL', '2A3FF 00181', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(477, 'Auto VelCro', 'Auto VelCro', 26, 00000000000, '22-4-00463', 'JUKI', 'AMS- 210EN-HS', '2A3HF 00363', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(478, 'Bartack M/C', 'Bartack M/C', 26, 00000000000, '22-4-00464', 'JUKI', 'LK- 1900A-HS', '2L1HD 00991', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(479, 'Bartack M/C', 'Bartack M/C', 26, 00000000000, '22-4-00465', 'JUKI', 'LK- 1900A-HS', '2L1HD 01168', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(480, 'Bartack M/C', 'Bartack M/C', 26, 00000000000, '22-4-00466', 'JUKI', 'LK- 1900A-HS', '2L1HD01173', '0', '2014-08-18 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(481, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00467', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00377', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(482, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00468', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00393', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(483, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00469', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00102', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(484, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00470', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00097', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(485, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00471', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00380', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(486, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00472', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00372', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(487, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00473', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00381', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(488, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00474', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00384', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(489, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00475', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00373', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(490, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00476', 'JUKI', 'DLN-  9010A-SH', '2D3GJ 00376', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(491, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00477', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHF 11535', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(492, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00478', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHF 11538', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(493, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00479', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHF 11518', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(494, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00480', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHF 11534', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(495, '1-Needle Sewing M/C', '1-Needle Sewing M/C', 26, 00000000000, '22-4-00481', 'JUKI', 'DDL- 9000B-SH-WB', '8DOHF 11512', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(496, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00482', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12562', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(497, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00483', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12567', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(498, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00484', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12560', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(499, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00485', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12553', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(500, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00486', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12570', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(501, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00487', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12554', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(502, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00488', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12552', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(503, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00489', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12564', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(504, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00490', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12556', '', '2015-12-31 16:38:32', 'PRO', NULL, NULL, '2015-12-31 10:38:32'),
(505, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00491', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12559', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(506, '2-Needle overlock 5T/H', '2-Needle overlock 5T/H', 26, 00000000000, '22-4-00492', 'JUKI', 'MO- 6716 DA', '8MOHE 15437', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(507, '2-Needle overlock 5T/H', '2-Needle overlock 5T/H', 26, 00000000000, '22-4-00493', 'JUKI', 'MO- 6716 DA', '8MOHE 15436', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(508, '2-Needle overlock 5T/H', '2-Needle overlock 5T/H', 26, 00000000000, '22-4-00494', 'JUKI', 'MO- 6716 DA', '8MOHE 15434', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(509, '2-Needle overlock 5T/H', '2-Needle overlock 5T/H', 26, 00000000000, '22-4-00495', 'JUKI', 'MO- 6716 DA', '8MOHE 15433', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(510, 'Flat-Bed, top & Bottom coverstitch M/C', 'Flat-Bed, top & Bottom coverstitch M/C', 26, 00000000000, '22-4-00496', 'JUKI', 'MF- 7523 DC11-B64', '8M4HE 41364', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(511, 'Flat-Bed, top & Bottom coverstitch M/C', 'Flat-Bed, top & Bottom coverstitch M/C', 26, 00000000000, '22-4-00497', 'JUKI', 'MF- 7523 DC11-B64', '8M4HE 41366', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(512, 'Bartacking M/C', 'Bartacking M/C', 26, 00000000000, '22-4-00498', 'JUKI', 'LK- 1900A-HS', '2L1HDO 1660', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(513, 'Bartacking M/C', 'Bartacking M/C', 26, 00000000000, '22-4-00499', 'JUKI', 'LK- 1900A-HS', '2L1HDO 1723', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(514, 'Bartacking M/C', 'Bartacking M/C', 26, 00000000000, '22-4-00500', 'JUKI', 'LK- 1900A-HS', '2L1HDO 1681', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(515, 'Bartacking M/C', 'Bartacking M/C', 26, 00000000000, '22-4-00501', 'JUKI', 'LK- 1900A-HS', '2L1HDO 1718', '0', '2014-09-14 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(516, 'Snap button', 'Snap button', 26, 00000000000, '22-4-00502', 'UZU', 'UZ-7EM', '0106538', '0', '2014-11-03 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(517, 'Snap button', 'Snap button', 26, 00000000000, '22-4-00503', 'UZU', 'UZ-7EM', '0106536', '0', '2014-11-03 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(518, 'Snap button', 'Snap button', 26, 00000000000, '22-4-00504', 'UZU', 'UZ-7EM', '0106539', '0', '2014-11-03 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(519, 'Snap button', 'Snap button', 26, 00000000000, '22-4-00505', 'UZU', 'UZ-7EM', '0106537', '0', '2014-11-03 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(520, '1- Needle Hemming M/C', '1- Needle Hemming M/C', 26, 00000000000, '22-4-00506', 'JUKI', 'DLN- 6390-7', '2D3HJ 00363', '0', '2014-11-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(521, '3/TH Overlock M/C', '3/TH Overlock M/C', 26, 00000000000, '22-4-00507', 'Pegasus', 'EXT 2242-52P1', '0151358', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(522, '3/TH Overlock M/C', '3/TH Overlock M/C', 26, 00000000000, '22-4-00508', 'Pegasus', 'EXT 2242-52P1', '0166478', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(523, '3/TH Overlock M/C', '3/TH Overlock M/C', 26, 00000000000, '22-4-00509', 'Pegasus', 'EXT 2242-52P1', '9192780', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00');
INSERT INTO `mla_asset` (`id`, `name`, `description`, `category_id`, `group_id`, `tag`, `brand`, `model`, `serial`, `origin`, `received_on`, `location`, `status`, `comment`, `created_on`) VALUES
(524, '3/TH Overlock M/C', '3/TH Overlock M/C', 26, 00000000000, '22-4-00510', 'Pegasus', 'EXT 2242-52P1', '0151398', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(525, 'Seam sealing M/C', 'Seam sealing M/C', 26, 00000000000, '22-4-00511', 'Pfaff', '901-8303-040/0013', '02720117', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(526, 'Seam sealing M/C', 'Seam sealing M/C', 26, 00000000000, '22-4-00512', 'Pfaff', '901-8303-040/0013', '02720121', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(527, 'Seam sealing M/C', 'Seam sealing M/C', 26, 00000000000, '22-4-00513', 'Pfaff', '901-8303-040/0013', '02735666', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(528, 'Seam sealing M/C', 'Seam sealing M/C', 26, 00000000000, '22-4-00514', 'Pfaff', '901-8303-040/0013', '02722854', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(529, 'Seam sealing M/C', 'Seam sealing M/C', 26, 00000000000, '22-4-00515', 'Pfaff', '901-8303-040/0013', '02720122', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(530, 'Automatic Press button M/C', 'Automatic Press button M/C', 26, 00000000000, '22-4-00516', 'KANE-M', 'MRT- 888', '6S-AXB', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(531, 'Automatic Press button M/C', 'Automatic Press button M/C', 26, 00000000000, '22-4-00517', 'KANE-M', 'MRT-888-020', 'MRT-588-020', '0', '2014-11-26 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(532, 'Foor clean MC', 'Foor clean MC', 26, 00000000000, '11-1-00015', 'HAKOMATIC', 'B115 R WZB7', '709021402914', '0', '2014-12-02 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(533, 'Heat transfer pressing machine', 'Heat transfer pressing machine', 26, 00000000000, '22-4-00518', 'VIM', 'V-348', '219', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(534, 'Heat transfer pressing machine', 'Heat transfer pressing machine', 26, 00000000000, '22-4-00519', 'VIM', 'V-348', '234', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(535, 'Heat transfer pressing machine', 'Heat transfer pressing machine', 26, 00000000000, '22-4-00520', 'VIM', 'V-348', '233', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(536, 'Heat transfer pressing machine', 'Heat transfer pressing machine', 26, 00000000000, '22-4-00521', 'VIM', 'V-348', '235', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(537, 'Seam sealing machine', 'Seam sealing machine', 26, 00000000000, '22-4-00522', 'PFAFF 901 ', 'PFAFF 9303-002', '02722855', 'EU(Germany)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(538, '2-needle lock stitch machine', '2-needle lock stitch machine', 26, 00000000000, '22-4-00523', 'SUNSTAR', 'KM-757BL-7S', '80206131', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(539, '2-needle sewing machine', '2-needle sewing machine', 26, 00000000000, '22-4-00524', 'SUNSTAR', 'KM-757-7S', '35011187', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(540, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00525', 'SUNSTAR', 'KM-350B-7S', '71013069', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(541, '2-needle lock stitch machine', '2-needle lock stitch machine', 26, 00000000000, '22-4-00526', 'SUNSTAR', 'KM-757BL-7S', '71000090', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(542, '2-needle lock stitch machine', '2-needle lock stitch machine', 26, 00000000000, '22-4-00527', 'SUNSTAR', 'KM-757BL-7S', '71013568', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(543, '2-needle sewing machine', '2-needle sewing machine', 26, 00000000000, '22-4-00528', 'SUNSTAR', 'KM-757-7S', '35011178', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(544, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00529', 'SUNSTAR', 'KM-350B-7S', '80207604', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(545, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00530', 'SUNSTAR', 'KM-350B-7S', '71016698', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(546, '2-needle sewing machine', '2-needle sewing machine', 26, 00000000000, '22-4-00531', 'SUNSTAR', 'KM-757-7S', '35011185', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(547, 'Cool set suction table 130*80 cm with air-ven', 'Cool set suction table 130*80 cm with air-vent ', 26, 00000000000, '22-4-00532', '0', '0', 'SC030066', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(548, 'Pegasus Overlock machine', 'Pegasus Overlock machine', 26, 00000000000, '22-4-00533', 'PEGASUS', 'WXT 2242-52P1', '0151408', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(549, 'Cool set suction table 130*80 cm with air-ven', 'Cool set suction table 130*80 cm with air-vent chimney, swivel arm with 01 egg-shape buck', 26, 00000000000, '22-4-00534', '0', '0', 'SC030066', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(550, 'Veit Iron HP 2003 230V/1250W/50-60 Hz ( consi', 'Veit Iron HP 2003 230V/1250W/50-60 Hz ( consiting of: teflon frame sole Veit HP 2003 Blue, hose and cable support)', 26, 00000000000, '22-4-00535', ' ', '0', '2365112', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(551, 'Steam Generator Veit 2365 6.6 kW/400V/50-60 H', 'Steam Generator Veit 2365 6.6 kW/400V/50-60 Hz. Steam capacity approx. 8.5 kg/h', 26, 00000000000, '22-4-00536', '0', '0', 'SC90009', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(552, 'roning tables consisting of: Veit iron HP 200', 'roning tables consisting of: Veit iron HP 2003, teflon sole and hose and cable support, cool set suction, steam generator Veit', 26, 00000000000, '22-4-00537', '0', '0', 'SC030066', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(553, 'Veit Iron HP 2003 230V/1250W/50-60 Hz ( consi', 'Veit Iron HP 2003 230V/1250W/50-60 Hz ( consiting of: teflon frame sole Veit HP 2003 Blue, hose and cable support)', 26, 00000000000, '22-4-00538', '0', '0', '2365112', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(554, 'JUKI automatic mongramming seewer No.AMSUE 03', 'JUKI automatic mongramming seewer No.AMSUE 03703', 26, 00000000000, '22-4-00539', 'JUKI', 'AMS-220B', 'AMSUE3703', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(555, '12 needle, double chain chain stitch Machine ', '12 needle, double chain chain stitch Machine with Motor 3 phase, 380V,400W and accessories(Origin: Motor:Japan, Motor: China)', 26, 00000000000, '22-4-00540', 'KANSAI', 'DFB-1412P', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(556, 'Pegasus Overlock machine', 'Pegasus Overlock machine', 26, 00000000000, '22-4-00541', 'PEGASUS', 'WXT 2242-52P1', '0169907', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(557, 'roning tables consisting of: Veit iron HP 200', 'roning tables consisting of: Veit iron HP 2003, teflon sole and hose and cable support, cool set suction, steam generator Veit', 26, 00000000000, '22-4-00542', '0', '1', '142282008', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(558, 'Sunshine Industrial Sewing Machine-3412 PRO, ', 'Sunshine Industrial Sewing Machine-3412 PRO, Serial No: 98081132', 26, 00000000000, '22-4-00543', 'SUNSHINE', 'SUNSHINE-3412', '98081132', 'Taiwan', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(559, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00544', 'SUNSTAR', 'KM-350B-7S', '71013544', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(560, 'Pegasus Overlock machine', 'Pegasus Overlock machine', 26, 00000000000, '22-4-00545', 'PEGASUS', 'WXT 2242-52P1', '0151376', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(561, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00546', 'SUNSTAR', 'KM-350B-7S', '41102755', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(562, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00547', 'SUNSTAR', 'KM-350B-7S', '80207637', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(563, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00548', 'SUNSTAR', 'KM-350B-7S', '80903401', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(564, '2-needle lock stitch machine, split needle ba', '2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00549', 'SUNSTAR', 'KM-797BL-7S', '80207238', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(565, '2-needle lock stitch machine, split needle ba', '2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00550', 'SUNSTAR', 'KM-797BL-7S', '71000200', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(566, '2-needle lock stitch machine', '2-needle lock stitch machine', 26, 00000000000, '22-4-00551', 'SUNSTAR', 'KM-757BL-7S', '71015644', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(567, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00552', 'SUNSTAR', 'KM-350B-7S', '80207656', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(568, '1 needle lock stitch machine ( head, table, s', '1 needle lock stitch machine ( head, table, stand, motor)', 26, 00000000000, '22-4-00553', 'SUNSTAR', 'KM-350B-7S', '71016706', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(569, '1-needle lock stitch machine', '1-needle lock stitch machine', 26, 00000000000, '22-4-00554', 'SUNSTAR', 'KM-350B-7S', '71013558', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(570, '2-needle lock stitch machine', '2-needle lock stitch machine', 26, 00000000000, '22-4-00555', 'SUNSTAR', 'KM-757BL-7S', '70400216', 'KOREA', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(571, 'Fabric roll stacker, 100 kg lift', 'Fabric roll stacker, 100 kg lift', 26, 00000000000, '22-4-00556', '0', '44000', '551299', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(572, 'Niebuhr 1800 mm semi automatic spreader inclu', 'Niebuhr 1800 mm semi automatic spreader including Assembling parts', 26, 00000000000, '22-4-00560', '0', '1800MM-103S', '-', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(573, 'Niebuhr 1800 mm semi automatic spreader inclu', 'Niebuhr 1800 mm semi automatic spreader including Assembling parts', 26, 00000000000, '22-4-00561', '0', '1800MM-138S,1800MM-149S', '-', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(574, 'Niebuhr 1800 mm semi automatic spreader inclu', 'Niebuhr 1800 mm semi automatic spreader including Assembling parts', 26, 00000000000, '22-4-00562', '0', '1800MM-138S,1800MM-149S', '-', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(575, 'Niebuhr 2000 mm semi automatic spreader inclu', 'Niebuhr 2000 mm semi automatic spreader including ', 26, 00000000000, '22-4-00563', '0', '2000MM-129S', '-', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(576, 'Rothnborg Band Saw', 'Rothnborg Band Saw', 26, 00000000000, '22-4-00564', 'ROTHENBORG', 'ROTHENBORG', '738-4650', 'Denmark', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(577, 'Rothnborg Band Saw', 'Rothnborg Band Saw', 26, 00000000000, '22-4-00565', 'ROTHENBORG', 'ROTHENBORG', '661-4650', 'Denmark', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(578, 'Rothnborg Band Saw, type 4650 with air system', 'Rothnborg Band Saw, type 4650 with air system', 26, 00000000000, '22-4-00566', 'ROTHENBORG', 'ROTHENBORG', '-', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(579, 'Pegasus Overlock machine', 'Pegasus Overlock machine', 26, 00000000000, '22-4-00567', 'PEGASUS', 'WXT 2242-52P1', '0166489', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(580, 'Pegasus Overlock machine', 'Pegasus Overlock machine', 26, 00000000000, '22-4-00568', 'PEGASUS', 'WXT 2242-52P1', '0151364', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(581, 'Rothnborg Band Saw, type 4650 with air system', 'Rothnborg Band Saw, type 4650 with air system', 26, 00000000000, '22-4-00571', 'ROTHENBORG', 'ROTHENBORG', '66-4650', 'Denmark', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(582, 'Generator Veit 2365 6.6 kW/400V/50-60 Hz. Ste', 'Generator Veit 2365 6.6 kW/400V/50-60 Hz. Steam capacity approx. 8.5 kg/h', 26, 00000000000, '22-4-00572', '0', '0', 'SC01009', 'China', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(583, 'Electric Heating Cloth Drill Machine', 'Electric Heating Cloth Drill Machine', 26, 00000000000, '22-4-00578', '0', 'PMM-SM-201L', '0', 'Taiwan', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(584, 'Electric Heating Cloth Drill Machine', 'Electric Heating Cloth Drill Machine', 26, 00000000000, '22-4-00579', '0', 'PMM-SM-201L', '0', 'Taiwan', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(585, 'Electric Heating Cloth Drill Machine', 'Electric Heating Cloth Drill Machine', 26, 00000000000, '22-4-00580', '0', 'PMM-SM-201L', '0', 'Taiwan', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(586, 'Electric Heating Cloth Drill Machine', 'Electric Heating Cloth Drill Machine', 26, 00000000000, '22-4-00581', '0', 'PMM-SM-201L', '0', 'Taiwan', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(587, 'Electric Heating Cloth Drill Machine', 'Electric Heating Cloth Drill Machine', 26, 00000000000, '22-4-00582', '0', 'PMM-SM-201L', '0', 'Taiwan', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(588, 'Straight Knife Cutter 8', 'Straight Knife Cutter 8', 26, 00000000000, '22-4-00583', '0', 'KM KS AUV 8', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(589, 'Straight Knife Cutter 8', 'Straight Knife Cutter 8', 26, 00000000000, '22-4-00584', '0', 'KM KS AUV 8', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(590, 'Straight Knife Cutter 8', 'Straight Knife Cutter 8', 26, 00000000000, '22-4-00585', '0', 'KM KS AUV 8', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(591, 'Straight Knife Cutter 8', 'Straight Knife Cutter 8', 26, 00000000000, '22-4-00586', ' ', 'KM KS AUV 8', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(592, 'Straight Knife Cutter 8', 'Straight Knife Cutter 8', 26, 00000000000, '22-4-00587', '0', 'KM KS AUV 8', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(593, 'Straight Knife Cutter 8', 'Straight Knife Cutter 8', 26, 00000000000, '22-4-00588', '0', 'KM KS AUV 8', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(594, 'Straight Knife Cutter 10', 'Straight Knife Cutter 10', 26, 00000000000, '22-4-00589', '0', 'KM KS AUV 10', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(595, 'Straight Knife Cutter 10', 'Straight Knife Cutter 10', 26, 00000000000, '22-4-00590', '0', 'KM KS AUV 10', '0', 'JAPAN', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(596, 'Cutting tables 2x24 metre prepared for 1800 m', 'Cutting tables 2x24 metre prepared for 1800 mm spreader, with air suction system. 1x6 metre manuel', 26, 00000000000, '22-4-00591', '0', '0', '0', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(597, 'Cutting tables 1x24 metre prepared for 2000 m', 'Cutting tables 1x24 metre prepared for 2000 mm spreader, with air suction system. ', 26, 00000000000, '22-4-00592', '0', '0', '0', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(598, 'Cutting tables 18 metre prepared for 1800 mm ', 'Cutting tables 18 metre prepared for 1800 mm spreader, with air system', 26, 00000000000, '22-4-00593', '0', '0', '0', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(599, 'Cutting tables', 'Cutting tables', 26, 00000000000, '22-4-00594', '0', '0', '0', 'EU(Denmark)', '2015-01-31 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(600, 'Botton Pressing Machine', 'Botton Pressing Machine', 26, 00000000000, '22-4-00573', '0', 'DX-2808', '-', 'JAPAN', '2015-03-15 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(601, 'Belt Loop Cutter', 'Belt Loop Cutter', 26, 00000000000, '22-4-00574', 'Cutex Brand ', 'TBC-50', 'T64122901', 'JAPAN', '2015-03-15 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(602, 'Botton Pressing Machine', 'Botton Pressing Machine', 26, 00000000000, '22-4-00575', '0', 'DX-2808', '1411065', 'JAPAN', '2015-03-15 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(603, 'Botton Pressing Machine', 'Botton Pressing Machine', 26, 00000000000, '22-4-00576', '0', 'DX-2808', '1411066', 'JAPAN', '2015-03-15 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(604, 'Botton Pressing Machine', 'Botton Pressing Machine', 26, 00000000000, '22-4-00577', '0', 'DX-2808', '1412084', 'JAPAN', '2015-03-15 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(605, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00595', 'Sunstar', 'KM-350B-7S', '80207634', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(606, 'Used Complete set of 1 needle lock stitch mac', 'Used Complete set of 1 needle lock stitch machine (complete set: head, table, stand, motor)', 26, 00000000000, '22-4-00596', 'Sunstar', 'KM-350B-7S', '80207568', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(607, 'Used Complete set of 1 needle lock stitch mac', 'Used Complete set of 1 needle lock stitch machine (complete set: head, table, stand, motor)', 26, 00000000000, '22-4-00597', 'Sunstar', 'KM-350B-7S', '71014012', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(608, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00598', '0', 'KM-350B-7S', '71013559', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(609, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00599', '0', 'KM-350B-7S', '80507765', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(610, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar. ', 26, 00000000000, '22-4-00600', '0', 'KM-797BL-7S', '80207243', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(611, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar. ', 26, 00000000000, '22-4-00601', '0', 'KM-797BL-7S', '71000184', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(612, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00602', '0', 'KM-350-7S', '80207661', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(613, 'Used Sewing machine SUNSTAR ', 'Used Sewing machine SUNSTAR ', 26, 00000000000, '22-4-00603', 'Sunstar', 'KM-350-7S', '60703534', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(614, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine( with pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00604', 'Sunstar', 'KM-350B-7S', '71014052', 'Korea, China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(615, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine( with pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00605', 'Sunstar', 'KM-350B-7S', '80207561', 'Korea, China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(616, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine( with pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00606', 'Sunstar', ' KM-350B-7S', '81103051', 'Korea, China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(617, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine( Origin: Head: China, Motor, table&stand: Korea)', 26, 00000000000, '22-4-00607', '0', ' KM-350B-7S', '80207623', 'Korea, China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(618, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high speed, bartacking machine for heavy-weight material( Origin: head and motor: Japan, control box: China,  table top and stand: Malaysia)', 26, 00000000000, '22-4-00608', '0', 'LK-1900AHS/MC-596KSS ', '2L1EC02612', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(619, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high speed, bartacking machine for heavy-weight material( Origin: head and motor: Japan, control box: China,  table top and stand: Malaysia)', 26, 00000000000, '22-4-00609', '0', 'LK-1900AHS/MC-596KSS ', '2L1ED00285', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(620, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00610', '0', 'KM-350B-7S', '80207584', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(621, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00611', '0', 'KM-350B-7S', '71016703', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(622, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar. ', 26, 00000000000, '22-4-00612', '0', 'KM-797BL-7S', '71204084', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(623, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00613', '0', 'KM-350B-7S', '80207586', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(624, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00614', '0', 'KM-350B-7S', '80207602', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(625, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00615', '0', 'KM-350B-7S', '41101687', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(626, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine.', 26, 00000000000, '22-4-00616', '0', 'KM-797BL-7S', '80207770', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(627, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine.', 26, 00000000000, '22-4-00617', '0', 'KM-350B-7S', '80207603', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(628, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00618', '0', 'KM-350B-7S', '91105957', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(629, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00619', '0', 'KM-350B-7S', '80207558', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(630, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00620', '0', 'KM-350B-7S', '71013556', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(631, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00621', '0', 'KM-350-7S', '80207662', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(632, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00622', '0', 'KM-350B-7S', '91110443', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(633, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00623', '0', 'KM-350B-7S', '80207566', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(634, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00624', '0', 'KM-530-7S', '80207665', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(635, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00625', '0', 'KM-530-7S', '80207669', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(636, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00626', '0', 'KM-797BL-7S', '80207767', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(637, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00627', '0', 'KM-350B-7S', '80207633', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(638, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00628', '0', 'KM-350B-7S', '71013070', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(639, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00629', '0', 'KM-350B-7S', '10F10774', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(640, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00630', '0', 'KM-350B-7S', '10DO6455', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(641, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00631', '0', 'KM-350B-7S', '74014020', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(642, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00632', '0', 'KM-757BL-7S', '80207224', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(643, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00633', '0', 'KM-797BL-7S', '71207514', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(644, 'Used Complete set of 1 needle lock stitch ', 'Used Complete set of 1 needle lock stitch ', 26, 00000000000, '22-4-00634', '0', 'KM-350B-7S', '71014019', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(645, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00635', '0', 'KM-797BL-7S', '80207774', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(646, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00636', '0', 'KM-797BL-7S', '80207235', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(647, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00637', '0', 'KM-797BL-7S', '71110828', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(648, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads JUKI ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00638', '0', 'MO6916R-FF650H/T041/JVF-390', '2MOAL00092', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(649, 'Used Overlock machine JUKI MOR 2500 Model 251', 'Used Overlock machine JUKI MOR 2500 Model 2516 FF6-500', 26, 00000000000, '22-4-00639', '0', 'MOR 2516', 'FF6-500', 'Japan', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(650, 'Used Overlock machine JUKI MOR 2500 Model 251', 'Used Overlock machine JUKI MOR 2500 Model 2516 FF6-500', 26, 00000000000, '22-4-00640', '0', 'MOR 2516', 'FF6-500', 'Japan', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(651, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads JUKI ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00641', '0', 'MO6916R-FF650H/T041/JVF-390', '2MOAL00101', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(652, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads JUKI ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00642', '0', 'MO6916R-FF650H/T041/JVF-390 ', '2MOAL00140', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(653, 'Used Complete set of 1 needle, 3 thread overl', 'Used Complete set of 1 needle, 3 thread overlock machine (Origin: Head, motor: China, table top and stand: Malaysia)', 26, 00000000000, '22-4-00643', '0', 'JUKI MO-6704S-0F6-50H/T041/GA112-1-P-D-F', '8MOEF11010', 'China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(654, 'Used Complete set of 1 needle, 3 thread overl', 'Used Complete set of 1 needle, 3 thread overlock machine (Origin: Head, motor: China, table top and stand: Malaysia)', 26, 00000000000, '22-4-00644', '0', 'JUKI MO-6704S-0F6-50H/T041/GA112-1-P-D-F', '8MFF11008', 'China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(655, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00645', '0', 'KM-530B-7S', '71014036', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(656, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00646', '0', 'KM-530B-7S', '91110493', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(657, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00647', '0', 'KM-530B-7S', '8020667', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(658, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00648', '0', 'KM-530-7S', '8020659', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(659, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.  ( Origin: Head&motor: Japan, Control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00649', '0', 'LK-1900AHS/MC-596KSS', '2L1ED00316', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(660, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00650', '0', 'KM-797BL-7S', '71205783', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(661, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00651', '0', 'KM-797BL-7S', '80209140', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(662, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00652', '0', 'KM-530B-7S', '71016701', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(663, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar.', 26, 00000000000, '22-4-00653', '0', 'KM-797BL-7S', '80209743', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(664, 'Used Complete set of 1 needle lock stitch wit', 'Used Complete set of 1 needle lock stitch with edge trimer', 26, 00000000000, '22-4-00654', '0', 'KM-530B-7S', '71013549', 'Korea', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(665, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high speed, bartacking machine for heavy-weight material( Origin: head and motor: Japan, control box: China,  table top and stand: Malaysia)', 26, 00000000000, '22-4-00655', '0', 'LK-1900AHS/MC-596KSS ', '2L1EA01266', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(666, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high speed, bartacking machine for heavy-weight material( Origin: head and motor: Japan, control box: China,  table top and stand: Malaysia)', 26, 00000000000, '22-4-00656', '0', 'LK-1900AHS/MC-596KSS ', '2L1EC01886', 'Japan, China, Malaysia', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(667, 'Used Hot Air Seam Sealing Machine V-8 with ac', 'Used Hot Air Seam Sealing Machine V-8 with accessories', 26, 00000000000, '22-4-00657', '0', 'V 8', '0', 'China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(668, 'Used Hot Air Seam Sealing Machine V-8 with ac', 'Used Hot Air Seam Sealing Machine V-8 with accessories', 26, 00000000000, '22-4-00658', '0', 'V 8', '0', 'China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(669, 'Used Hot Air Seam Sealing Machine V-8 with ac', 'Used Hot Air Seam Sealing Machine V-8 with accessories', 26, 00000000000, '22-4-00659', '0', 'V 8', '0', 'China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(670, 'Used Hot Air Seam Sealing Machine V-8 with ac', 'Used Hot Air Seam Sealing Machine V-8 with accessories', 26, 00000000000, '22-4-00660', '0', 'V 8', '0', 'China', '2015-03-17 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(671, 'BUTTON PRESSING MACHINE ', 'BUTTON PRESSING MACHINE ', 26, 00000000000, '22-4-00661', '0', 'XD-2808', '0', '0', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(672, 'BUTTON PRESSING MACHINE ', 'BUTTON PRESSING MACHINE ', 26, 00000000000, '22-4-00662', '0', 'XD-2808', '0', '0', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(673, 'BUTTON PRESSING MACHINE ', 'BUTTON PRESSING MACHINE ', 26, 00000000000, '22-4-00663', '0', 'XD-2808', '0', '0', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(674, 'BUTTON PRESSING MACHINE ', 'BUTTON PRESSING MACHINE ', 26, 00000000000, '22-4-00664', '0', 'XD-2808', '0', '0', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(675, 'Used Complete set of 1-needle, needle feed, c', 'Used Complete set of 1-needle, needle feed, cylinder-bed, lockstitch machine ( Origin: Head-Japan, Motor & control box-China, Table top& Stand - Malaysia)', 26, 00000000000, '22-4-00665', '0', 'JUKI DLN-6390S', '0', 'Japan, China, Malaysia', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(676, 'Used Complete set of 12 needle, double chain ', 'Used Complete set of 12 needle, double chain stitch machine with motor 3 phase, 380V, 400W and accessories( Origin: Head-Japan, Motor-China)', 26, 00000000000, '22-4-00666', '0', 'DFB-1412P', '0', 'Japan', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(677, 'Used Complete set of 12 needle, double chain ', 'Used Complete set of 12 needle, double chain stitch machine with motor 3 phase, 380V, 400W and accessories( Origin: Head-Japan, Motor-China)', 26, 00000000000, '22-4-00667', '0', 'DFB-1412P', '0', 'Japan', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(678, 'Used Straight Knife Cutter 8', 'Used Straight Knife Cutter 8', 26, 00000000000, '22-4-00668', '0', 'KM KS AUV 8''''', '0', 'Japan', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(679, 'Used Straight Knife Cutter 8', 'Used Straight Knife Cutter 8', 26, 00000000000, '22-4-00670', '0', 'KM KS AUV 8''''', '0', 'Japan', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(680, 'Used Straight Knife Cutter 8', 'Used Straight Knife Cutter 8', 26, 00000000000, '22-4-00671', '0', 'KM KS AUV 8''''', '0', 'Japan', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(681, 'Used Straight Knife Cutter 8', 'Used Straight Knife Cutter 8', 26, 00000000000, '22-4-00672', '0', 'KM KS AUV 8''''', '0', 'Japan', '2015-04-08 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(682, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high-speed bartacking machine for heavy-weight material (Origin: Head: Japan, control box: China, Table top& stand: Malaysia) ', 26, 00000000000, '22-4-00609', '0', 'LK-1900AHS/MC-596KSS', '2LZ1EC02672', 'Japan, China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(683, 'Machine for velcrostrips', 'Machine for velcrostrips', 26, 00000000000, '22-4-00669', '0', 'Mxs12-30', '-', 'JP', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(684, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia, wide pedal 300x400 mm)', 26, 00000000000, '22-4-00673', '0', 'JUKI MO-6916R-FF650H/T041/JVF-390', '2M0BK00228', 'Japan, China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(685, ' Used Complete set of 2-needle, needle feed, ', ' Used Complete set of 2-needle, needle feed, split needle bar lock stitch machine', 26, 00000000000, '22-4-00674', '0', 'KM-797BL-7S', '10F01476', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(686, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar', 26, 00000000000, '22-4-00675', '0', 'KM-797BL-7S', '71212119', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(687, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia, wide pedal 300x400 mm)', 26, 00000000000, '22-4-00676', '0', 'JUKI MO-6916R-FF650H/T041/JVF-390', '2M0BK00229', 'Japan, China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(688, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00677', '0', 'KM-797BL-7S', '80800612', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(689, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia, wide pedal 300x400 mm)', 26, 00000000000, '22-4-00678', '0', 'JUKI MO-6916R-FF650H/T041/JVF-390', '2M0BL00035', 'Japan, China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(690, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine with edge trimer( wide pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00680', '0', 'KM-530-7S', '91112763', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(691, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine with edge trimer( wide pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00681', '0', 'KM-530-7S', '70103064', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(692, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00682', '0', 'KM-350B-7S', '71014047', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(693, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00683', '0', 'KM-350B-7S', '71016699', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(694, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00684', '0', 'KM-350B-7S', '80207613', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(695, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00685', '0', 'KM-350B-7S', '71016694', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(696, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00686', '0', 'KM-350B-7S', '80207591', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(697, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00687', '0', 'KM-350B-7S', '80207642', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(698, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00688', '0', 'KM-350B-7S', '71013066', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(699, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00689', '0', 'KM-350B-7S', '80207599', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(700, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00690', '0', 'KM-350B-7S', '80207643', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(701, 'Used Complete set of 1- needle, 3-thread over', 'Used Complete set of 1- needle, 3-thread overlock machine (Origin: Head, motor, control box & Panel: China, table top and stand: Malaysia)', 26, 00000000000, '22-4-00691', '0', 'JUKI-MO-6704S-0F6-50H/SC921/M51N/CP18B/YHH-SY', 'M80GM1113', 'China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(702, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00692', '0', 'KM-350B-7S', '80207596', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(703, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine with edge trimer( wide pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00693', '0', 'KM-530-7S', '80207664', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(704, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00694', '0', 'KM-350B-7S', '71013557', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(705, 'Used Complete set of automatic velcro/label a', 'Used Complete set of automatic velcro/label attachment machine', 26, 00000000000, '22-4-00695', '0', 'SPS/C-1306HS-23', '71101025', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(706, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00696', '0', 'KM-350B-7S', '80207563', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(707, 'Used Complete set of 1- needle, 3-thread over', 'Used Complete set of 1- needle, 3-thread overlock machine (Origin: Head, motor: China, table top and stand: Malaysia)', 26, 00000000000, '22-4-00697', '0', 'JUKI MO-6704S-0F6-50H', 'M80EC13901', 'China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(708, 'Used Hot Air Seam Sealing Machine V-8 with ac', 'Used Hot Air Seam Sealing Machine V-8 with accessories', 26, 00000000000, '22-4-00698', '0', 'V-8', '-', 'China', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(709, 'Used Sunstar Bartack machine', 'Used Sunstar Bartack machine', 26, 00000000000, '22-4-00699', '0', 'SPS/B-B1254', '34050088', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(710, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine with edge trimer( wide pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00700', '0', 'KM-530-7S', '91112762', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(711, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar (Needle gauge: 6.4 mm, wide pedal 300x400 mm) ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00701', '0', 'KM-797BL-7S', '71110199', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(712, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00702', '0', 'KM-797BL-7S', '71110207', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(713, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '22-4-00703', '0', 'KM-350B-7S', '80207594', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(714, 'Used Hot Air Seam Sealing Machine V-8 with ac', 'Used Hot Air Seam Sealing Machine V-8 with accessories', 26, 00000000000, '22-4-00704', '0', 'V-8', '-', 'China', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(715, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00705', '0', 'JUKI MO-6916R-FF650H/T041/GA112-1-P-F', '2M0BK00218', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(716, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia, wide pedal 300x400 mm)', 26, 00000000000, '22-4-00707', '0', 'JUKI MO-6916R-FF650H/T041/JVF-390', '2M0BK00220', 'Japan, China, Malaysia', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(717, ' Used Complete set of automatic velcro/label ', ' Used Complete set of automatic velcro/label attachment machine', 26, 00000000000, '22-4-00708', '0', 'SPS/C-1306HS-23', '71101023', 'Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(718, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00710', '0', '0', '0', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(719, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00711', '0', '0', '0', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(720, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00712', '0', '0', '0', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(721, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00713', '0', '0', '0', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(722, 'Sewing Machine', 'Sewing Machine', 26, 00000000000, '22-4-00714', 'Kansai', 'DI-B-1412P', '0815818', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(723, 'Snap button', 'Snap button', 26, 00000000000, '22-4-00715', '0', '0', '0', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(724, 'Snap button', 'Snap button', 26, 00000000000, '22-4-00716', '0', '0', '0', '0', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(725, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '0', '0', 'KM-350B-7S', '0', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00');
INSERT INTO `mla_asset` (`id`, `name`, `description`, `category_id`, `group_id`, `tag`, `brand`, `model`, `serial`, `origin`, `received_on`, `location`, `status`, `comment`, `created_on`) VALUES
(726, ' Used Complete set of 1-needle lock stitch ma', ' Used Complete set of 1-needle lock stitch machine ( Origin: head & motor: China, table&stand: Korea)', 26, 00000000000, '0', '0', 'KM-350B-7S', '0', 'China, Korea', '2015-06-01 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(727, 'Used Sewing machine Sunstar KM-797-7S', 'Used Sewing machine Sunstar KM-797-7S', 26, 00000000000, '22-4-00717', 'Sunstar', 'KM-797BL-7S', '91000024', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(728, 'Snap button machine', 'Snap button machine', 26, 00000000000, '22-4-00718', '0', 'TIEMA', 'TM-808', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(729, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00719', '0', 'Grand', '0', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(730, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00720', '0', 'Grand', '0', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(731, 'Snap button machine', 'Snap button machine', 26, 00000000000, '22-4-00721', '0', 'TIEMA', 'TM-808', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(732, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS ( Origin: Head & motor: Japan, control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00722', '0', 'LK-1900A-HS', '2L 1AM01539', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(733, 'Thread Cutting Machine', 'Thread Cutting Machine', 26, 00000000000, '22-4-00723', '0', 'Grand', '0', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(734, 'Snap button machine', 'Snap button machine', 26, 00000000000, '22-4-00724', '0', 'TIEMA', 'TM-808', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(735, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS ( Origin: Head & motor: Japan, control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00725', '0', 'LK-1900A-HS', '2L 1BB00719', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(736, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar. Model: KM-797BL-7S', 26, 00000000000, '22-4-00726', '0', 'KM-797BL-7S', '80207241', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(737, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00728', 'Sunstar', 'KM-350B-7S', '90508884', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(738, 'Used Complete set of automatic velcro/label a', 'Used Complete set of automatic velcro/label attachment machine', 26, 00000000000, '22-4-00729', '0', 'SPS/C-11306HS-23', '71101011', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(739, 'Used complete set of 1-needle, needle feed cy', 'Used complete set of 1-needle, needle feed cylinder bed, lockstitch machine. Type: JUKI DLN-6390S-7-W0A/SC510/M51. Origin: Head: USA, Motor&control box: China, table top&stand: Malaysia', 26, 00000000000, '22-4-00729', '0', '0', 'DLN-6090-7', 'USA\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(740, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads.Type: JUKI MO6916R-FF650H/T041/JVF-390 ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00730', 'Juki', 'MO-6916R', '2MOAL00088', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(741, 'Used Complete set of 1-needle, needle feed cy', 'Used Complete set of 1-needle, needle feed cylinder bed, lockstitch machine. Type: JUKI DLN-6390S-7 W01A', 26, 00000000000, '22-4-00731', 'Juki', 'DLN-6390-7', '2D3BK00200', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(742, 'Used Complete set of 1-needle overlock for ed', 'Used Complete set of 1-needle overlock for edges machine. Type: JUKI MO6704S0F650H/T041/JVF-390. Complete set with fully sunken table and stand with wide pedal 300x400 an clutch motor. Origin: Head&motor: China, table top and stand: Malaysia', 26, 00000000000, '22-4-00732', 'Sunstar', 'MO-6704S', '8MOBCO22355', 'China\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(743, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00733', 'Sunstar', 'KM-350B-7S', '80207639', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(744, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00734', 'Sunstar', 'KM-350B-7S', '71016700', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(745, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS ( Origin: Head & motor: Japan, control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00735', '0', 'LK-1900A-HS', '2L 1BB00616', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(746, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00736', 'Sunstar', 'KM-350B-7S', '80207641', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(747, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar. Model: KM-797BL-7S', 26, 00000000000, '22-4-00737', 'Sunstar', 'KM-797BL-7S', '80700533', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(748, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00738', 'Sunstar', 'KM-350B-7S', '80207575', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(749, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00739', 'Sunstar', 'KM-350B-7S', '80207581', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(750, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00740', 'Sunstar', 'KM-350B-7S', '80207646', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(751, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00741', 'Sunstar', 'KM-350B-7S', '41102754', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(752, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00742', 'Sunstar', 'KM-350B-7S', '80207616', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(753, 'Used Complete set of 1-needle, lock stitch ma', 'Used Complete set of 1-needle, lock stitch machine. Origin: Head: China, Stand and table: Korea, motor: Korea', 26, 00000000000, '22-4-00743', 'Sunstar', 'KM-350B-7S', '10C12748', 'China\nKorea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(754, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00744', 'Sunstar', 'KM-350B-7S', '71016705', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(755, 'Used Complete set of 1-needle lock stitch mac', 'Used Complete set of 1-needle lock stitch machine', 26, 00000000000, '22-4-00745', 'Sunstar', 'KM-350B-7S', '90508879', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(756, 'Used Hot Air Seam sealing machine', 'Used Hot Air Seam sealing machine', 26, 00000000000, '22-4-00746', 'Vim', 'V-8', '0189', 'China', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(757, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar. Model: KM-797BL-7S', 26, 00000000000, '22-4-00747', 'Sunstar', 'KM-797BL-7S', '80207234', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(758, 'Used Complete set of 2-needle belt loop attac', 'Used Complete set of 2-needle belt loop attaching machine. Type: JUKI MOL254DABCDE', 26, 00000000000, '22-4-00748', 'Juki', 'MOL-254', '2L 1D002176', 'Japan', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(759, 'Used Complete set of 1-needle, lock stitch ma', 'Used Complete set of 1-needle, lock stitch machine. Origin: Head: China, Stand and table: Korea, motor: Korea', 26, 00000000000, '22-4-00749', 'Sunstar', 'KM-350B-7S', '71010432', 'China\nKorea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(760, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.  Type: JUKI LK-1900AHS/MC-596KSS. ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00750', 'Juki', 'LK-1900A-HS', '2L 1BB00722', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(761, 'Used Complete set of 1-needle, lock stitch ma', 'Used Complete set of 1-needle, lock stitch machine (Wide pedal 300x400 mm; Origin: Head & Motor: China, Table top & stand: Korea )', 26, 00000000000, '22-4-00751', 'Sunstar', 'KM-350B-7S', '71016710', 'China\nKorea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(762, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads.Type: JUKI MO6916RFF650H/T041/JVF-390 ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00752', 'Juki', 'MO-6916R', '2M0BB00109', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(763, 'Used Complete set of 1-needle, 3 threads over', 'Used Complete set of 1-needle, 3 threads overlock machine.Type: Juki MO-6704S-0F6-50H/T041/GA112-1-P-D-F  (Origin: Head & Motor: China, Table top & stand: Malaysia)', 26, 00000000000, '22-4-00753', 'Juki', 'MO-6704S', '8MOBK21935', 'China\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(764, 'Used Complete set of 2-needle, lock stitch ma', 'Used Complete set of 2-needle, lock stitch machine, split needle bar (Wide pedal 300x400 mm; Origin: Head & Motor: China, Table top & stand: Korea )', 26, 00000000000, '22-4-00754', 'Sunstar', 'KM-797BL-7S', '10FO1101', 'China\nKorea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(765, 'Used Hot Air Seam sealing machine', 'Used Hot Air Seam sealing machine', 26, 00000000000, '22-4-00755', 'Vim', 'V-8', '0135', 'China', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(766, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.  Type: JUKI LK-1900AHS/MC-596KSS. ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00756', 'Juki', 'LK-1900A-HS', '2L IAM01639', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(767, 'Used Complete set of 2-needle, needle feed, s', 'Used Complete set of 2-needle, needle feed, split needle bar lock stitch machine', 26, 00000000000, '22-4-00757', 'Sunstar', 'KM-797BL-7S', '71212121', 'Korea\nChina', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(768, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads, wide pedal 300x400.Type: JUKI MO6916R-FF650H/T041/JVF-390 ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00758', 'Juki', 'MO-6916R', '8MOEH30156', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(769, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads, wide pedal 300x400.Type: JUKI MO6916R-FF650H/T041/JVF-390 ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00759', 'Juki', 'MO-6916R', '2MOBK00227', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(770, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads.Type: JUKI MO6916R-FF650H/T041/JVF-390 ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00760', 'Juki', 'MO-6916R', '2MOBB00072', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(771, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads.Type: JUKI MO6916R-FF650H/T041/JVF-390 ( Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00761', 'Juki', 'MO-6916R', '8M0EC31123', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(772, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, hight-speed, bartacking machine for heavy-weight material. Wide pedal: 300x400 mm. Type: JUKI LK-1900AHS/MC-596KSS. (Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00762', 'Juki', 'LK-1900A-HS', '2L DD00269', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(773, 'Used Complete set of 2-needle overlock, 5 thr', 'Used Complete set of 2-needle overlock, 5 threads.Type: JUKI MO-6916R-FF650H/T041/GA112-1-P-F (Origin: Head: Japan, Motor: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00763', 'Juki', 'MO-6916R', '8MOEC31119', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(774, 'Used Complete set of 2-needle, 5 threads over', 'Used Complete set of 2-needle, 5 threads overlock machine. Type: JUKI MO-6916R-FF650H/T041/GA112-1-P-D-F (Origin: Head & Motor: China, Table top & stand: Malaysia)', 26, 00000000000, '22-4-00764', 'Juki', 'MO-6916R', '2MOAL00141', 'China\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(775, 'Used Complete set of 2-needle, 5 threads over', 'Used Complete set of 2-needle, 5 threads overlock machine  (Origin: Head & Motor: China, Table top & stand: Malaysia)', 26, 00000000000, '22-4-00765', 'Juki', 'MO-6916R', '8MOEC31127', 'China\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(776, 'Used Complete set of 2-needle, 5 threads over', 'Used Complete set of 2-needle, 5 threads overlock machine  (Origin: Head & Motor: China, Table top & stand: Malaysia)', 26, 00000000000, '22-4-00766', 'Juki', 'MO-6916R', '2MBK00219', 'China\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(777, 'Used Complete set of 2-needle, 5 threads over', 'Used Complete set of 2-needle, 5 threads overlock machine  (Origin: Head & Motor: China, Table top & stand: Malaysia)', 26, 00000000000, '22-4-00767', 'Juki', 'MO-6916R', '2MOBJ00465', 'China\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(778, 'Used Water Pressure tester', 'Used Water Pressure tester', 26, 00000000000, '22-4-00768', '0', '0', '-', 'Korea', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(779, 'Snap button machine', 'Snap button machine', 26, 00000000000, '22-4-00769', '0', 'TIEMA', 'TM-808', '0', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(780, 'Used Sewing machine Adker K269990004-73 50393', 'Used Sewing machine Adker K269990004-73 503936', 26, 00000000000, '22-4-00770', 'Ader', 'Adker K269990004-73 503936', '503936', 'Germany', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(781, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS ( Origin: Head & motor: Japan, control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00771', '0', 'LK-1900A-HS', '2L 1DD00110', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(782, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS ( Origin: Head & motor: Japan, control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00772', '0', 'LK-1900A-HS', '2L 1EA01263', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(783, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS ( Origin: Head & motor: Japan, control box: China, Table top& stand: Malaysia)', 26, 00000000000, '22-4-00773', '0', 'LK-1900A-HS', '2L 1AK02579', 'Japan\nChina\nMalaysia', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(784, 'Used Machine for velcrostrips', 'Used Machine for velcrostrips', 26, 00000000000, '22-4-00774', '0', '0', '0', 'Denmark', '2015-09-09 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(785, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high-speed, bartacking machine for heavy-weight material.\nType: JUKI LK-1900ANHS/MC-598KSS \nOrigin:Head&motor:Japan, Control box:China, Table top&stand:Malaysia', 26, 00000000000, '22-4-00578', 'JUKI', 'LK-1900AN-HS', '2L1HD00488', 'JAPAN\nCHINA\nMALAYSIA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(786, 'Used Complete set of bartacking machine for h', 'Used Complete set of bartacking machine for heavy-weight material.Type: JUKI LK-1900AHS/MC-596KSS\nOrigin:Head&motor:Japan, Control box:China, Table top&stand:Malaysia', 26, 00000000000, '22-4-00579', 'JUKI', 'LK-1900A-HS', '2L1ED00317', 'JAPAN\nCHINA\nMALAYSIA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(787, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar', 26, 00000000000, '22-4-00580', 'Sunstar', 'KM-797BL-7S', '71110832', 'KOREA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(788, 'Used Complete set of computer-controlled, hig', 'Used Complete set of computer-controlled, high-speed, bartacking machine for heavy-weight material.\nOrigin:Head&motor:Japan, Control box:China, Table top&stand:Malaysia', 26, 00000000000, '22-4-00581', 'JUKI', 'LK-1900A-HS', '2L1AK02563', 'JAPAN\nCHINA\nMALAYSIA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(789, 'Used Complete set of Button hole machine JUKI', 'Used Complete set of Button hole machine JUKI LBH 790', 26, 00000000000, '22-4-00775', 'JUKI', 'LBH 790', '791-R01948', 'JAPAN', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(790, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar', 26, 00000000000, '22-4-00776', 'Sunstar', 'KM-797BL-7S', '71000192', 'KOREA\nCHINA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(791, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar', 26, 00000000000, '22-4-00777', 'Sunstar', 'KM-797BL-7S', '71100182', 'KOREA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(792, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar', 26, 00000000000, '22-4-00778', 'Sunstar', 'KM-797BL-7S', '80207769', 'KOREA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(793, 'Used Complete set of 2-needle lock stitch mac', 'Used Complete set of 2-needle lock stitch machine, split needle bar', 26, 00000000000, '22-4-00779', 'Sunstar', 'KM-797BL-7S', '71100167', 'KOREA\nCHINA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(794, 'Used Complete set of 2-needle belt loop attac', 'Used Complete set of 2-needle belt loop attaching machine. Type: JUKI MOL254DABCDE', 26, 00000000000, '22-4-00780', 'JUKI', 'MOL-254', '071016', 'JAPAN', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(795, 'Used Complete set of Eyelet button hole machi', 'Used Complete set of Eyelet button hole machine. Type: JUKI MEB 3200JSKA', 26, 00000000000, '22-4-00781', 'JUKI', 'MEB-3200J', '2M5BD00011', 'JAPAN', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(796, 'Used Complete set of Eyelet button hole machi', 'Used Complete set of Eyelet button hole machine. Type: JUKI MEB 3200JSKA', 26, 00000000000, '22-4-00782', 'JUKI', 'MEB-3200J', '2M5BD00013', 'JAPAN', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(797, 'Used Hot Air Seam sealing machine', 'Used Hot Air Seam sealing machine', 26, 00000000000, '22-4-00783', 'VIM', 'V-8', '0191', 'CHINA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(798, 'Used Complete set of 3-needle feed-off-the-ar', 'Used Complete set of 3-needle feed-off-the-arm double chainstitch machine for lapping seam', 26, 00000000000, '22-4-00784', 'JUKI', 'UNION SPECIAL 35800DZ36', 'KK1895348', 'USA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(799, 'Used Complete set of Automatic belt loop atta', 'Used Complete set of Automatic belt loop attaching machine', 26, 00000000000, '22-4-00785', 'JUKI', 'MOL-254', 'AMBDA01012', 'JAPAN', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(800, 'Used Complete set inclusive motor and frame. ', 'Used Complete set inclusive motor and frame. JUKI-UNION SPECIAL DZ36', 26, 00000000000, '22-4-00786', 'JUKI', 'UNION SPECIAL 35800DZ36', 'KF1894200', 'JAPAN', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(801, 'Used Complete set of 3-needle feed-off-the-ar', 'Used Complete set of 3-needle feed-off-the-arm double chainstitch machine for lapping seam', 26, 00000000000, '22-4-00787', 'JUKI', 'UNION SPECIAL 35800DZ36', 'KM1896103', 'USA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(802, 'Used Complete set of 3-needle feed-off-the-ar', 'Used Complete set of 3-needle feed-off-the-arm double chainstitch machine for lapping seam', 26, 00000000000, '22-4-00788', 'JUKI', 'UNION SPECIAL 35800DZ36', 'LB1896816', 'USA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(803, 'Used Complete set of 3-needle feed-off-the-ar', 'Used Complete set of 3-needle feed-off-the-arm double chainstitch machine for lapping seam', 26, 00000000000, '22-4-00789', 'JUKI', 'UNION SPECIAL 35800DZ36', 'KF1894206', 'USA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(804, 'Used Complete set of 3-needle feed-off-the-ar', 'Used Complete set of 3-needle feed-off-the-arm double chainstitch machine for lapping seam', 26, 00000000000, '22-4-00790', 'JUKI', 'UNION SPECIAL 35800DZ36', 'KF1894191', 'USA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(805, 'Used Complete set of 3-needle feed-off-the-ar', 'Used Complete set of 3-needle feed-off-the-arm double chainstitch machine for lapping seam', 26, 00000000000, '22-4-00791', 'JUKI', 'UNION SPECIAL 35800DZ36', 'LA1896345', 'USA', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(806, 'No name', 'No name', 26, 00000000000, '22-4-00792', 'Grand', 'Grand', '0', '0', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(807, 'No name', 'No name', 26, 00000000000, '22-4-00793', 'Grand', 'Grand', '0', '0', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(808, 'No name', 'No name', 26, 00000000000, '22-4-00794', 'Weijie', 'WJ-808', 'LINDER10-02', '0', '2015-11-04 17:00:00', 'PRO', '', '', '2015-12-30 17:00:00'),
(809, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00490', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12556', '', '2015-12-31 16:15:37', 'PRO', NULL, NULL, '2015-12-31 10:15:37'),
(810, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00490', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12556', '', '2015-12-31 16:16:23', 'PRO', NULL, NULL, '2015-12-31 10:16:23'),
(811, '2-Needle sewing M/C', '2-Needle sewing M/C', 26, 00000000000, '22-4-00490', 'JUKI', 'LH- 3588AGF-7-WB', '8L3HE 12556', '', '2015-12-31 16:19:53', 'PRO', NULL, NULL, '2015-12-31 10:19:53');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_asset_categories`
--

CREATE TABLE IF NOT EXISTS `mla_asset_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Daten für Tabelle `mla_asset_categories`
--

INSERT INTO `mla_asset_categories` (`id`, `category`, `description`, `created_on`) VALUES
(4, 'Buiding	', '    ADM and Workshop Building    dsfdsfdsfdsf', '2015-12-30'),
(24, 'IT equipments', 'IT equipments   ', '2015-12-29'),
(25, 'Office Funiture', 'Office Funiture    ', '2015-12-29'),
(26, 'Machinery', 'Machinery Management', '2015-12-29'),
(27, 'Other small aquisition', 'Other small aquisition', '2015-12-29'),
(28, 'Cars /Vehicles', 'Cars and Semi-Vehicles. etc.', '2015-12-30');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_asset_group`
--

CREATE TABLE IF NOT EXISTS `mla_asset_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_type_id_idx` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `mla_asset_group`
--

INSERT INTO `mla_asset_group` (`id`, `category_id`, `name`, `description`, `created_on`) VALUES
(1, 4, '2 Needle Machine details', '2 Needle Machine details', '2015-12-29 03:13:34'),
(2, 4, 'Press button machine', 'Press button machine', '2015-12-29 03:14:22');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_asset_location`
--

CREATE TABLE IF NOT EXISTS `mla_asset_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id_idx` (`department_id`),
  KEY `user_id_idx` (`user_id`),
  KEY `asset_id_idx` (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_asset_pics`
--

CREATE TABLE IF NOT EXISTS `mla_asset_pics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `filetype` varchar(45) NOT NULL,
  `visibility` tinyint(1) DEFAULT NULL,
  `comments` longtext,
  `uploaded_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id_idx` (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `mla_asset_pics`
--

INSERT INTO `mla_asset_pics` (`id`, `asset_id`, `url`, `size`, `filetype`, `visibility`, `comments`, `uploaded_on`) VALUES
(1, 810, '\\pictures/490.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:16:23'),
(2, 811, 'C:\\Users\\nmt\\workspace\\nhungttk\\/module/Inventory/data\\assets\\asset_811\\pictures/490.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:19:53'),
(3, NULL, 'C:\\Users\\nmt\\workspace\\nhungttk\\/module/Inventory/data\\assets\\asset_504\\pictures/490.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:35:53'),
(4, NULL, 'C:\\Users\\nmt\\workspace\\nhungttk\\/module/Inventory/data\\assets\\asset_504\\pictures/490.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:37:23'),
(5, 504, 'C:\\Users\\nmt\\workspace\\nhungttk\\/module/Inventory/data\\assets\\asset_504\\pictures/490.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:38:32'),
(6, 457, 'C:\\Users\\nmt\\workspace\\nhungttk\\/module/Inventory/data\\assets\\asset_457\\pictures/443.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:40:15'),
(7, 81, 'C:\\Users\\nmt\\workspace\\nhungttk\\/module/Inventory/data\\assets\\asset_81\\pictures/51.png', NULL, 'image/png', NULL, NULL, '2015-12-31 10:47:17');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_asset_repair_requests`
--

CREATE TABLE IF NOT EXISTS `mla_asset_repair_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_to_name` varchar(45) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id_idx` (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_departments`
--

CREATE TABLE IF NOT EXISTS `mla_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_department_members`
--

CREATE TABLE IF NOT EXISTS `mla_department_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `department_id_idx` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_spareparts`
--

CREATE TABLE IF NOT EXISTS `mla_spareparts` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  `created_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_sparepart_pics`
--

CREATE TABLE IF NOT EXISTS `mla_sparepart_pics` (
  `id` int(11) NOT NULL,
  `sparepart_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `visibility` tinyint(1) DEFAULT NULL,
  `comments` longtext,
  `uploaded_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sparepart_id_idx` (`sparepart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_users`
--

CREATE TABLE IF NOT EXISTS `mla_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `firstname` varchar(64) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `lastname` varchar(64) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `salt` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `role` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `registration_key` varchar(32) CHARACTER SET latin1 NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastvisit_date` timestamp NULL DEFAULT NULL,
  `block` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `CT_users_1` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mla_user_photos`
--

CREATE TABLE IF NOT EXISTS `mla_user_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `size` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT 'small',
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  `comment` text CHARACTER SET latin1,
  PRIMARY KEY (`id`),
  KEY `mla_user_photos_FK1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=1 ;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `mla_asset`
--
ALTER TABLE `mla_asset`
  ADD CONSTRAINT `mla_asset_FK1` FOREIGN KEY (`category_id`) REFERENCES `mla_asset_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_asset_group`
--
ALTER TABLE `mla_asset_group`
  ADD CONSTRAINT `mla_asset_group_FK1` FOREIGN KEY (`category_id`) REFERENCES `mla_asset_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_asset_location`
--
ALTER TABLE `mla_asset_location`
  ADD CONSTRAINT `mla_asset_location_FK1` FOREIGN KEY (`department_id`) REFERENCES `mla_departments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `mla_asset_location_FK2` FOREIGN KEY (`user_id`) REFERENCES `mla_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `mla_asset_location_FK3` FOREIGN KEY (`asset_id`) REFERENCES `mla_asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_asset_pics`
--
ALTER TABLE `mla_asset_pics`
  ADD CONSTRAINT `mla_asset_pics_FK1` FOREIGN KEY (`asset_id`) REFERENCES `mla_asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_asset_repair_requests`
--
ALTER TABLE `mla_asset_repair_requests`
  ADD CONSTRAINT `mla_asset_repair_requests_FK1` FOREIGN KEY (`asset_id`) REFERENCES `mla_asset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_department_members`
--
ALTER TABLE `mla_department_members`
  ADD CONSTRAINT `mla_department_members_FK1` FOREIGN KEY (`department_id`) REFERENCES `mla_departments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `mla_department_members_FK2` FOREIGN KEY (`user_id`) REFERENCES `mla_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_sparepart_pics`
--
ALTER TABLE `mla_sparepart_pics`
  ADD CONSTRAINT `mla_sparepart_pics_FK1` FOREIGN KEY (`sparepart_id`) REFERENCES `mla_spareparts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `mla_user_photos`
--
ALTER TABLE `mla_user_photos`
  ADD CONSTRAINT `mla_user_photos_FK1` FOREIGN KEY (`user_id`) REFERENCES `mla_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
