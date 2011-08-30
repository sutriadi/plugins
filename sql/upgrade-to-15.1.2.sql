SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

ALTER TABLE `plugins` ADD `plugin_build` VARCHAR( 50 ) NOT NULL AFTER `plugin_version`;

INSERT INTO `plugins_vars` (`name`, `value`) VALUE ('allowed_ip', '["127.0.0.1","::1","192.168.56.101"]');

CREATE TABLE IF NOT EXISTS `plugins_dtables` (
  `table` varchar(32) NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique key for table.',
  `type` enum('member','biblio') NOT NULL DEFAULT 'member' COMMENT 'Type of table.',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Table title.',
  `desc` text COMMENT 'Table description.',
  `first_col` enum('none','checkbox','radio') NOT NULL,
  `base_cols` blob NOT NULL,
  `end_cols` blob NOT NULL,
  `php_code` tinyint(1) NOT NULL,
  `add_code` blob NOT NULL,
  `windowed` tinyint(1) NOT NULL DEFAULT '1',
  `sort` blob NOT NULL,
  PRIMARY KEY (`table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
