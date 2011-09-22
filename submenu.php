<?php
/*
 *      submenu.php
 *      
 *      Copyright 2011 Indra Sutriadi Pipii <indra@sutriadi.web.id>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

/* Plugins module submenu items */

$menu[] = array('Header', __('Plugins'));
$menu[] = array(__('Plugins'), MODULES_WEB_ROOT_DIR.'plugins/index.php', __('View Available Plugins'));
$menu[] = array(__('Configure'), MODULES_WEB_ROOT_DIR.'plugins/s_conf/', __('Configure Plugins'));
$menu[] = array(__('Menu'), MODULES_WEB_ROOT_DIR.'plugins/s_menus/', __('Configure Menus'));
$menu[] = array(__('Theme'), MODULES_WEB_ROOT_DIR.'plugins/s_themes/', __('Configure Themes'));
$menu[] = array(__('Block'), MODULES_WEB_ROOT_DIR.'plugins/s_blocks/', __('Configure Blocks'));
$menu[] = array(__('DataTables'), MODULES_WEB_ROOT_DIR.'plugins/s_datatables/', __('Configure DataTables'));

if (isset($_SESSION['plugins_enabled']) AND count($_SESSION['plugins_enabled']) > 0)
{
	$enplugins = $_SESSION['plugins_enabled'];
	if (count($enplugins) > 0)
		$menu[] = array('Header', __('Sibling Plugins'));
	foreach ($enplugins as $key)
	{
		if ($key['plugin_type'] == 1 AND array_key_exists($key['plugin_id'], $_SESSION['plugins_available']))
			$menu[] = array($key['plugin_name'], MODULES_WEB_ROOT_DIR . "plugins/include/{$key['plugin_id']}", $key['plugin_name']);
	}
	unset($enplugins);
}
else
{
	$sql = 'SELECT * FROM plugins';
	$query = $dbs->query($sql);
	$enplugins = array();
	if ($query->num_rows > 0)
	{
		$menu[] = array('Header', __('Sibling Plugins'));
		$arrays = array();
		while($array = $query->fetch_assoc())
			$arrays[$array['plugin_id']] = $array;
		$enplugins = array_merge($enplugins, $arrays);
	}
	foreach ($enplugins as $key)
	{
		if ($key['plugin_type'] == 1 AND array_key_exists($key['plugin_id'], $enplugins))
			$menu[] = array($key['plugin_name'], MODULES_WEB_ROOT_DIR . "plugins/include/{$key['plugin_id']}", $key['plugin_name']);
	}
}
