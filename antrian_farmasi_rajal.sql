-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2023 at 10:40 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sik`
--

-- --------------------------------------------------------

--
-- Table structure for table `antrian_farmasi_rajal`
--

CREATE TABLE `antrian_farmasi_rajal` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_rawat` varchar(17) CHARACTER SET latin1 NOT NULL,
  `no_resep` varchar(14) CHARACTER SET latin1 NOT NULL,
  `no_antrian` varchar(5) CHARACTER SET latin1 NOT NULL,
  `status` enum('0','1') CHARACTER SET latin1 NOT NULL,
  `tgl_antri` date DEFAULT NULL,
  `resep` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tgl_no_resep` (`tgl_antri`,`no_resep`),
  UNIQUE KEY `uq_tgl_resep_no_antrian` (`tgl_antri`,`resep`,`no_antrian`),
  KEY `idx_status_resep_tgl_no` (`status`,`resep`,`tgl_antri`,`no_antrian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
