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

INSERT INTO `plugins_vars` (`name`, `value`) VALUE ('allowed_ip', '["127.0.0.1","::1","192.168.56.101"]');

CREATE TABLE IF NOT EXISTS `plugins_menus` (
  `menu` varchar(32) NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique key for menu. This is used as a block delta so length is 32.',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu title; displayed at top of block.',
  `description` text COMMENT 'Menu description.',
  PRIMARY KEY (`menu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugins_menus_items` (
  `menu` varchar(32) NOT NULL DEFAULT '' COMMENT 'The menu name. All links with the same menu name (such as navigation) are part of the same menu.',
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The menu link item ID is the integer primary key.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The parent link item ID is the mlid of the link above in the hierarchy, or zero if the link is at the top level in its menu.',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT 'The Drupal path or external path this link points to.',
  `label` varchar(255) NOT NULL DEFAULT '' COMMENT 'The text displayed for the link, which may be modified by a title callback stored in menu_router.',
  `hidden` smallint(6) NOT NULL DEFAULT '0' COMMENT 'A flag for whether the link should be rendered in menus. (1 = a disabled menu item that may be shown on admin screens, -1 = a menu callback, 0 = a normal, visible link)',
  `external` smallint(6) NOT NULL DEFAULT '0' COMMENT 'A flag to indicate if the link points to a full URL starting with a protocol, like http:// (1 = external, 0 = internal).',
  `weight` int(11) NOT NULL DEFAULT '0' COMMENT 'Link weight among links in the same menu at the same depth.',
  `customized` smallint(6) NOT NULL DEFAULT '0' COMMENT 'A flag to indicate that the user has manually created or edited the link (1 = customized, 0 = not customized).',
  PRIMARY KEY (`item_id`),
  KEY `path_menu` (`path`(128),`menu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
