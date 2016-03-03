-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 02, 2015 at 01:53 PM
-- Server version: 5.6.25
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bakarmedia`
--
CREATE DATABASE IF NOT EXISTS `bakarmedia` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bakarmedia`;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `code` varchar(32) NOT NULL DEFAULT 'notyet' COMMENT 'php generated',
  `user` varchar(256) NOT NULL,
  `path` varchar(256) NOT NULL,
  `filename` varchar(256) NOT NULL,
  `filesize` int(11) NOT NULL,
  `uploaddate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `folder`
--

CREATE TABLE `folder` (
  `user` varchar(256) NOT NULL,
  `path` varchar(256) NOT NULL,
  `key` varchar(16) DEFAULT NULL COMMENT 'CONCAT(''m_'', LEFT(MD5(CONCAT(user, path, now())), 14))',
  `visibility` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `sender` varchar(12) NOT NULL,
  `file` varchar(32) NOT NULL,
  `type` varchar(5) NOT NULL,
  `desc` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `slot`
--

CREATE TABLE `slot` (
  `id` varchar(32) DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `code` varchar(32) NOT NULL,
  `status` int(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(256) NOT NULL,
  `password` varchar(64) NOT NULL,
  `fname` varchar(32) DEFAULT NULL,
  `lname` varchar(32) DEFAULT NULL,
  `level` enum('a','u','p') NOT NULL DEFAULT 'u',
  `limit` bigint(20) UNSIGNED NOT NULL DEFAULT '524288000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`email`, `password`, `fname`, `lname`, `level`, `limit`) VALUES
('admin', '63e69cfc25674c8a9dcd817ee81db01d9f732c098114fa7fac59c26134aebb30', 'Admin', ' ', 'a', 1099511627776);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `folder`
--
ALTER TABLE `folder`
  ADD PRIMARY KEY (`user`,`path`);

--
-- Indexes for table `slot`
--
ALTER TABLE `slot`
  ADD PRIMARY KEY (`ip`,`code`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
