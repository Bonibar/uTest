-- phpMyAdmin SQL Dump
-- version 4.4.13.1
-- http://www.phpmyadmin.net
--
-- Host: zwertvfrpmsql.mysql.db
-- Generation Time: Mar 16, 2016 at 02:15 AM
-- Server version: 5.5.46-0+deb7u1-log
-- PHP Version: 5.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zwertvfrpmsql`
--

-- --------------------------------------------------------

--
-- Table structure for table `utest_user`
--

CREATE TABLE IF NOT EXISTS `utest_user` (
  `id` int(11) NOT NULL,
  `login` varchar(8) NOT NULL,
  `promo` int(11) NOT NULL,
  `city` varchar(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `level` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `utest_utest`
--

CREATE TABLE IF NOT EXISTS `utest_utest` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `command` mediumtext NOT NULL,
  `stdin` mediumtext,
  `stdout` mediumtext,
  `return_value` smallint(6) DEFAULT '0',
  `opt_file` varchar(16) DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `utest_votes`
--

CREATE TABLE IF NOT EXISTS `utest_votes` (
  `id` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `fk_utest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `utest_user`
--
ALTER TABLE `utest_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_2` (`id`);

--
-- Indexes for table `utest_utest`
--
ALTER TABLE `utest_utest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Index 2` (`fk_user`);

--
-- Indexes for table `utest_votes`
--
ALTER TABLE `utest_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_utest_votes_utest_utest` (`fk_utest`),
  ADD KEY `Index 2` (`fk_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utest_user`
--
ALTER TABLE `utest_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `utest_utest`
--
ALTER TABLE `utest_utest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `utest_votes`
--
ALTER TABLE `utest_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `utest_utest`
--
ALTER TABLE `utest_utest`
  ADD CONSTRAINT `utest_ibfk_1` FOREIGN KEY (`fk_user`) REFERENCES `utest_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `utest_votes`
--
ALTER TABLE `utest_votes`
  ADD CONSTRAINT `FK_utest_votes_utest_user` FOREIGN KEY (`fk_user`) REFERENCES `utest_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_utest_votes_utest_utest` FOREIGN KEY (`fk_utest`) REFERENCES `utest_utest` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
