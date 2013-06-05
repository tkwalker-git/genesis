-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 23, 2012 at 11:20 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pangea`
--

-- --------------------------------------------------------

--
-- Table structure for table `promoter_detail`
--

DROP TABLE IF EXISTS `promoter_detail`;
CREATE TABLE IF NOT EXISTS `promoter_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `briefbio` varchar(255) NOT NULL,
  `com_website` varchar(255) NOT NULL,
  `busines_phone` varchar(255) NOT NULL,
  `promoterid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `promoter_detail`
--

