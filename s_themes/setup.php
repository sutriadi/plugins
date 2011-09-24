<?php
/*
 *      setup.php
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

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if (!$can_read) {
	die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
}

require('../func.php');
checkip();
checkref();

require('./func.php');

list($host, $dir, $file) = scinfo();

if ($_POST)
{
	if (isset($_POST['theme']) AND isset($_GET['theme']) AND $_POST['theme'] == $_GET['theme'])
		$theme = $_GET['theme'];
	else
		$theme = variable_get('opac_theme');

	$alert = __('Theme configuration has been saved!');
	$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/?act=configure&theme=" . $theme . "');";
	
	unset($_POST['saveData']);
	unset($_POST['theme']);
	variable_set('theme_' . $theme . '_settings', $_POST, 'serial');

	echo "<html><head><script type=\"text/javascript\">alert('$alert');$script</script></head><body></body></html>";
}
exit();
