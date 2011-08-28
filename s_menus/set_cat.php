<?php
/*
 *      set_cat.php
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

// key to authenticate
define('INDEX_AUTH', '1');

if (!defined('SENAYAN_BASE_DIR')) {
    // main system configuration
    require '../../../../sysconfig.inc.php';
    // start the session
    require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

// privileges checking
$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if (!$can_read) {
	die('<div class="errorBox">You dont have enough privileges to view this section</div>');
}

require('../func.php');
checkip();
checkref();

require('./func.php');

if ($_POST)
{
	list($host, $dir, $file) = scinfo();
	$post = (object) $_POST;
	$post->title = isset($post->title) ? trim($post->title) : '';
	$valid = ( ! preg_match("/^([-a-z0-9_-])+$/", $post->menu)) ? FALSE : TRUE;
	if ( ! $_GET AND ($valid === FALSE || empty($post->title)))
	{
		$alert = __('Error!\nMenu has not been saved!');
		$script = ($valid === FALSE) ? "parent.$('input[name=menu]').focus();" : "parent.$('input[name=title]').focus();";
	}
	else
	{
		$check = menu_get($post->menu, 'check');
		$alert = __('Menu has been saved!');
		if ($check > 0 || ($_GET AND isset($_GET['menu'])))
		{
			$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
			if (isset($_GET['act']) AND $_GET['act'] == 'del')
			{
				$alert = __('Menu has been deleted!');
				$sql = sprintf("DELETE FROM `plugins_menus` WHERE `menu` = '%s'", $post->menu);
			}
			else
			{
				$sql = sprintf("UPDATE `plugins_menus` SET `title` = '%s', `description` = '%s' WHERE `menu` = '%s'",
					$post->title,
					$post->desc,
					$_GET['menu']
				);
			}
			$dbs->query($sql);
		}
		else
		{
			$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/add_cat.php');";
			$sql = sprintf("INSERT INTO `plugins_menus` (`menu`, `title`, `description`) VALUE ('%s', '%s', '%s')",
				$post->menu,
				$post->title,
				$post->desc
			);
			$dbs->query($sql);
		}
	}

	echo "<html><head><script type=\"text/javascript\">alert('$alert');$script</script></head><body></body></html>";
}

exit();
