SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
INSERT INTO `mst_module` (`module_id`, `module_name`, `module_path`, `module_desc`) VALUES (999, 'plugins', 'plugins', 'Plugins');
INSERT INTO `group_access` (`group_id`, `module_id`, `r`, `w`) VALUES (1, 999, 1, 1);

CREATE TABLE IF NOT EXISTS `plugins` (
  `plugin_id` varchar(100) NOT NULL,
  `plugin_name` varchar(250) NOT NULL,
  `plugin_author` varchar(250) NOT NULL,
  `plugin_version` varchar(50) NOT NULL,
  `plugin_description` text NOT NULL,
  `plugin_type` int(1) NOT NULL,
  `plugin_install` varchar(150) NOT NULL,
  `plugin_remove` text NOT NULL,
  `plugin_deps` text NOT NULL,
  PRIMARY KEY  (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugins_vars` (
  `name` varchar(250) NOT NULL,
  `value` blob NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
