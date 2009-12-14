-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 03, 2008 at 11:20 AM
-- Server version: 5.0.41
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dctl_iconclass`
--

-- --------------------------------------------------------

--
-- Table structure for table `tNAME`
--

DROP TABLE IF existS `tNAME`;
CREATE TABLE IF NOT existS `tNAME` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` tinytext character set utf8 collate utf8_bin NOT NULL default '',
  `iconclass` tinytext character set utf8 collate utf8_bin NOT NULL default '',
  `nameSoundex` varchar(4) character set utf8 collate utf8_bin NOT NULL default '',
  `nameNormalized` varchar(40) character set utf8 collate utf8_bin NOT NULL default '',
  `note` tinytext character set utf8 collate utf8_bin default '',
  PRIMARY KEY  (`id`),
  KEY `iconclass` (`iconclass`),
  KEY `nameSoundex` (`nameSoundex`),
  KEY `nameNormalized` (`nameNormalized`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
