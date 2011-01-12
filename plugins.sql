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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
