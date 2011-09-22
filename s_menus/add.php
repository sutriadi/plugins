<?php
/*
 *      add.php
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

define('INDEX_AUTH', '1');

if (!defined('SENAYAN_BASE_DIR')) {
    // main system configuration
    require '../../../../sysconfig.inc.php';
    // start the session
    require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}

define('MODPLUGINS_WEB_ROOT_DIR', MODULES_WEB_ROOT_DIR . 'plugins/');

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if ( ! $can_read || ! $can_write)
{
	die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
}

require('../func.php'); // include plugin function
require('./func.php'); // include menu function

checksess();
checkip();
checkref();

list($host, $dir, $file) = scinfo();
$ips = implode(" ", json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true));

if ($can_write)
{
	$item_tab = false;
	if (isset($_GET['type']))
	{
		switch ($_GET['type'])
		{
			case "item":
				list($menu, $title, $desc) = (isset($_GET['menu']) AND ! empty($_GET['menu'])) ? menu_get($_GET['menu']) : array('','','');
				$subtitle = sprintf(' - %s : <em>%s</em>', __('Add Menu Item'), $title);
				$item_tab = true;
				break;
			case "menu":
			default:
				$subtitle = ' - ' . __('Add Menu');
		}
	}
	else if (isset($_GET['item']) AND ! isset($_GET['act']))
	{
		$subtitle = ' - ' . __('Edit Menu Item');
		$item_tab = true;
	}
	else if (isset($_GET['menu']) AND ! isset($_GET['item']) AND ! isset($_GET['act']))
		$subtitle = ' - ' . __('Edit Menu');
	else if (isset($_GET['act']) AND $_GET['act'] == 'del')
	{
		if (isset($_GET['menu']) AND isset($_GET['item']))
		{
			list($menu, $title, $desc) = (isset($_GET['menu']) AND ! empty($_GET['menu'])) ? menu_get($_GET['menu']) : array('','','');
			$subtitle = sprintf(' - %s : <em>%s</em>', __('Delete Menu Item'), $title);
			$item_tab = true;
		}
		else
			$subtitle = ' - ' . __('Delete Menu');
	}
	
	$theme = isset($_GET['theme']) ? $_GET['theme'] : variable_get('opac_theme');
	
	include('./tab.php');
	include('./form.php');
}

exit();
