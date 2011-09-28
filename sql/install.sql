SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
INSERT INTO `mst_module` (`module_id`, `module_name`, `module_path`, `module_desc`) VALUES (999, 'plugins', 'plugins', 'Plugins');
INSERT INTO `group_access` (`group_id`, `module_id`, `r`, `w`) VALUES (1, 999, 1, 1);

CREATE TABLE IF NOT EXISTS `plugins` (
  `plugin_id` varchar(100) NOT NULL,
  `plugin_name` varchar(250) NOT NULL,
  `plugin_author` varchar(250) NOT NULL,
  `plugin_version` varchar(50) NOT NULL,
  `plugin_build` VARCHAR( 50 ) DEFAULT 0,
  `plugin_description` text NOT NULL,
  `plugin_type` int(1) NOT NULL,
  `plugin_page` text NOT NULL,
  `plugin_menu` text NOT NULL,
  `plugin_block` text NOT NULL,
  `plugin_install` text NOT NULL,
  `plugin_remove` text NOT NULL,
  `plugin_deps` text NOT NULL,
  PRIMARY KEY  (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugins_vars` (
  `name` varchar(250) NOT NULL,
  `value` blob NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `plugins_vars` (`name`, `value`) VALUES
('allowed_ip', '["127.0.0.1","::1","192.168.56.101"]'),
('allowed_tags', '<a> <em> <strong> <cite> <code> <ul> <ol> <li> <dl> <dt> <dd>');

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

CREATE TABLE IF NOT EXISTS `plugins_menus` (
  `menu` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `desc` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`menu`),
  KEY `menu` (`menu`,`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `plugins_menus` (`menu`, `title`, `desc`) VALUES
('primary-links', 'Primary Links', 'Primary links'),
('secondary-links', 'Secondary Links', 'Secondary links');

INSERT INTO `plugins_blocks` (`plugin`, `delta`, `theme`, `title`) VALUES
('menu', 'primary-links', 'base', 'Primary Links'),
('menu', 'secondary-links', 'base', 'Secondary Links');

INSERT INTO `plugins_blocks` (`plugin`, `delta`, `theme`, `title`) VALUES
('core', 'advanced_search', 'base', 'Advanced Search'),
('core', 'award', 'base', 'Award'),
('core', 'language', 'base', 'Language'),
('core', 'librarian_login', 'base', 'Librarian Login'),
('core', 'license', 'base', 'License'),
('core', 'member_login', 'base', 'Member Login'),
('core', 'search', 'base', 'Search'),
('core', 'welcome', 'base', 'Welcome');

CREATE TABLE `plugins_menus_items` (
  `menu` varchar(32) NOT NULL DEFAULT '',
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL,
  `disabled` tinyint(1) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `external` tinyint(1) NOT NULL DEFAULT '0',
  `weight` int(11) NOT NULL DEFAULT '0',
  `customized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `path_menu` (`path`(128),`menu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
