SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

ALTER TABLE `plugins` ADD COLUMN `plugin_block` TEXT NOT NULL AFTER `plugin_type`;
ALTER TABLE `plugins` ADD COLUMN `plugin_menu` TEXT NOT NULL AFTER `plugin_type`;
ALTER TABLE `plugins` ADD COLUMN `plugin_page` TEXT NOT NULL AFTER `plugin_type`;

INSERT INTO `plugins_vars` (`name`, `value`) VALUES ('allowed_tags', '<a> <em> <strong> <cite> <code> <ul> <ol> <li> <dl> <dt> <dd>');

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
('core', 'search', 'base', 'Search'),
('core', 'advanced_search', 'base', 'Advanced Search'),
('core', 'language', 'base', 'Language'),
('core', 'license', 'base', 'License'),
('core', 'welcome', 'base', 'Welcome'),
('core', 'award', 'base', 'Award');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8
