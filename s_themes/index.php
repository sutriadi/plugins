<?php
/*
 *      index.php
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

define('MODPLUGINS_WEB_ROOT_DIR', MODULES_WEB_ROOT_DIR . 'plugins/');

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if ( ! $can_read || ! $can_write)
{
	die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
}

require('./func.php');
require('../func.php');

checksess();
checkip();
checkref();

list($host, $dir, $file) = scinfo();
$ips = implode(" ", json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true));
$theme = isset($_GET['theme']) ? $_GET['theme'] : variable_get('opac_theme');
$theme_fatin_dir = SENAYAN_BASE_DIR . $sysconf['template']['dir'] . '/fatin/';
$theme_base_dir = $theme_fatin_dir . 'sub/';
$theme_base_url = $dir . '../../../../../' . $sysconf['template']['dir'] . '/fatin/sub/';
$theme_dir = $theme_base_dir . $theme;

$themes = (isset($_GET['act']) AND $_GET['act'] == 'reindex') ? relist_avtheme() : list_avtheme();
if (isset($_GET['act']) AND $_GET['act'] == 'configure')
	$subtitle = ' - ' . __('Configure') . sprintf(' : <em>%s</em>', $themes[$theme]);

if ($can_write)
{
	include('./tab.php');
	if (isset($_GET['act']) AND $_GET['act'] == 'configure')
		include('./form.php');
	else
		include('./list.php');
}

exit();
