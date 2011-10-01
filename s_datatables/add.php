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
    require '../../../../sysconfig.inc.php';
    require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}

if ( ! defined('MODPLUGINS_WEB_ROOT_DIR'))
	define('MODPLUGINS_WEB_ROOT_DIR', MODULES_WEB_ROOT_DIR . 'plugins/');
if ( ! defined('MODPLUGINS_BASE_DIR'))
	define('MODPLUGINS_BASE_DIR', MODULES_BASE_DIR . 'plugins/');

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if ( ! $can_read || ! $can_write)
{
	die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
}

require('../func.php'); // include plugin function
require('./func.php'); // include dataTables function

checksess();
checkip();
checkref();

list($host, $dir, $file) = scinfo();
$ips = implode(" ", variable_get('allowed_ip', '["127.0.0.1", "::1"]', 'json'));

if ($can_write)
{
	if ($_GET AND isset($_GET['cols']))
	{
		switch ($_GET['cols'])
		{
			case "sort":
				$subtitle = ' - ' . __('Sort Columns');
				break;
			case "add":
			default:
				$subtitle = ' - ' . __('Columns');
		}
	}
	else if (isset($_GET['action']) AND $_GET['action'] == 'del')
		$subtitle = ' - ' . __('Delete');
	else if (isset($_GET['table']))
		$subtitle = ' - ' . __('Edit');
	else
		$subtitle = ' - ' . __('Add');
	
	include('./tab.php');
	include('./form.php');
}

exit();
