SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `plugins_blocks` (
  `idblock` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(100) NOT NULL,
  `delta` varchar(32) NOT NULL,
  `theme` varchar(64) NOT NULL,
  `region` varchar(64) NOT NULL,
  `weight` tinyint(4) NOT NULL,
  `title` varchar(64) NOT NULL,
  `classes` text NOT NULL,
  PRIMARY KEY (`idblock`),
  KEY `list` (`idblock`,`plugin`,`delta`,`theme`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugins_blocks_custom` (
  `block` varchar(32) NOT NULL,
  `desc` varchar(128) NOT NULL,
  `title` varchar(64) NOT NULL,
  `code` blob NOT NULL,
  `filter` enum('text','simple','full','php') NOT NULL,
  PRIMARY KEY (`block`),
  KEY `desc` (`desc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
