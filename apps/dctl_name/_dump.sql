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
-- Database: `dctl_name`
--

-- --------------------------------------------------------

--
-- Table structure for table `tNAME`
--

DROP TABLE IF existS `tNAME`;
CREATE TABLE IF NOT existS `tNAME` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `collector` int(10) unsigned default NULL,
  `name` tinytext character set utf8 collate utf8_bin NOT NULL,
  `lang` varchar(2) character set utf8 collate utf8_bin NOT NULL default '',
  `nameSoundex` varchar(4) character set utf8 collate utf8_bin NOT NULL default '',
  `type` varchar(20) character set utf8 collate utf8_bin default '',
  `nameNormalized` varchar(40) character set utf8 collate utf8_bin NOT NULL default '',
  `subtype` varchar(20) character set utf8 collate utf8_bin default '',
  PRIMARY KEY  (`id`),
  KEY `collector` (`collector`),
  KEY `nameSoundex` (`nameSoundex`),
  KEY `type` (`type`),
  KEY `nameNormalized` (`nameNormalized`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
