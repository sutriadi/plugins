DROP TABLE IF EXISTS `plugins`;
DROP TABLE IF EXISTS `plugins_dtables`;
DELETE FROM `mst_module` WHERE `module_id` = 999;
DELETE FROM `group_access` WHERE `module_id` = 999;
DELETE FROM `plugins_vars` WHERE `name` = 'allowed_ip';
DELETE FROM `plugins_vars` WHERE `name` = 'allowed_tags';
DELETE FROM `plugins_menus` WHERE `menu` = 'primary-links' OR `menu` = 'secondary-links';
