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

$alert = '';
$script = '';
if ($_POST)
{
	list($host, $dir, $file) = scinfo();
	$get = (object) $_GET;
	$post = (object) $_POST;
	
	if (isset($get->sort))
	{
		unset($_POST['saveData']);
		foreach($_POST['sort'] as $item_id => $items)
		{
			if (is_array($items))
			{
				$sql = sprintf("SELECT `parent_id` FROM `plugins_menus_items` WHERE `item_id` = '%s' AND `parent_id` != 0", $items['parent']);
				$rows = $dbs->query($sql);
				if ($rows->num_rows > 0)
				{
					$row = (object) $rows->fetch_assoc();
					$parent_of_parent = $row->parent_id;
				}
				$valid = ((isset($parent_of_parent) AND ($parent_of_parent != $item_id)) || ( ! isset($parent_of_parent))) ? true : false;
				if ($item_id != $items['parent'] AND $valid === true)
				{
					$parent_id = ! is_numeric($items['parent']) ? 0 : $items['parent'];
					$sql = sprintf("UPDATE `plugins_menus_items` "
						. "SET `parent_id` = '%s', `weight` = '%s' "
						. "WHERE `item_id` = '%s'",
						$parent_id,
						$items['weight'],
						$item_id
					);
					$dbs->query($sql);
				}
			}
		}
		$alert = __('Menu items has been saved!');
		$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/?menu=" . $get->menu . "');";
	}
	else if (isset($get->act) AND $get->act == 'del')
	{
		if (isset($post->item))
		{
			$alert = __('Menu item has been deleted!');
			$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/?menu=" . $get->menu . "');";
			list($item, $parent, $path, $label, $hidden, $external, $weight, $customized) = menu_item_get($post->item);
			$sql = sprintf("DELETE FROM `plugins_menus_items` WHERE `item_id` = '%s'", $item);
			$dbs->query($sql);
			$sql = sprintf("UPDATE `plugins_menus_items` SET `parent_id` = '%s' WHERE `parent_id` = '%s'",
				$parent,
				$item
			);
			$dbs->query($sql);
		}
		else if (isset($post->menu))
		{
			$alert = __('Menu has been deleted!');
			$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
			$sql = sprintf("DELETE FROM `plugins_menus` WHERE `menu` = '%s'", $post->menu);
			$dbs->query($sql);
			$sql = sprintf("DELETE FROM `plugins_menus_items` WHERE `menu` = '%s'", $post->menu);
			$dbs->query($sql);
			$sql = sprintf("DELETE FROM `plugins_blocks` WHERE `plugin` = 'menu' AND `delta` = '%s'", $post->menu);
			$dbs->query($sql);
		}
	}
	else
	{
		$post->title = isset($post->title) ? trim($post->title) : '';
		$post->path = isset($post->path) ? trim($post->path) : '';
		$post->label = isset($post->label) ? addslashes(trim($post->label)) : '';
		$post->desc = isset($post->desc) ? addslashes(trim($post->desc)) : '';
		$segment = array('.', '=', '&', '?');
		$valid = array();
		$valid['menu'] = isset($post->menu) ? (( ! preg_match("/^([-a-z0-9_-])+$/", $post->menu)) ? FALSE : TRUE) : FALSE;
		$valid['item'] = isset($post->path) ? (( ! preg_match("/^([-a-z0-9_-])+$/", str_replace($segment, '', $post->path))) ? FALSE : TRUE) : FALSE;
		$alert = __('Error!\nMenu has not been saved!');
		$script = ($valid['menu'] === FALSE) ? "parent.$('input[name=menu]').select();" : "parent.$('input[name=title]').select();";
		if ( ! isset($get->menu))
		{
			if ($valid['menu'] AND ! empty($post->title))
			{
				$check = menu_get($post->menu, 'check');
				if ($check > 0)
				{
					$alert = __('Error!\nMenu you entered already exists');
					$script = "parent.$('input[name=menu]').select();";
				}
				else
				{
					$alert = __('Menu has been saved!');
					$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/add.php');";
					$sql = sprintf("INSERT INTO `plugins_menus` (`menu`, `title`, `desc`) VALUE ('%s', '%s', '%s')",
						$post->menu,
						$post->title,
						$post->desc
					);
					$dbs->query($sql);
					require('../s_blocks/func.php');
					setup_block('menu', $post->menu, $post->title);
				}
			}
		}
		else if (isset($get->menu) AND ! empty($get->menu) AND ! isset($get->item))
		{
			if ($valid['menu'] AND ! empty($post->title))
			{
				$check = menu_get($post->menu, 'check');
				$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
				if ($check == 0)
					$alert = __('Error!\nMenu you entered not exists');
				else
				{
					$alert = __('Menu has been saved!');
					$sql = sprintf("UPDATE `plugins_menus` SET `title` = '%s', `desc` = '%s' WHERE `menu` = '%s'",
						$post->title,
						$post->desc,
						$post->menu
					);
					$dbs->query($sql);
				}
			}
			else
			{
				if ($valid['item'] === false || empty($post->path) || empty($post->label))
				{
					$alert = __('Menu item has not been saved!');
					if ($valid['item'] === false || empty($post->path))
						$script = "parent.$('input[name=path]').select();";
					else
						$script = "parent.$('input[name=label]').select();";
				}
				else
				{
					if (isset($get->menu) AND $post->parent == $get->menu)
					{
						$post->menu = $post->parent;
						$post->parent = 0;
					}
					else if ($post->parent != $get->menu)
					{
						$post->menu = $get->menu;
						if (is_numeric($post->parent))
						{
							$sql = sprintf("SELECT `menu` FROM `plugins_menus_items` WHERE `parent_id` = '%s'", $post->parent);
							$rows = $dbs->query($sql);
							if ($rows->num_rows > 0)
							{
								$row = (object) $rows->fetch_assoc();
								$post->menu = $row->menu;
							}
						}
						else
						{
							$post->menu = $post->parent;
							$post->parent = 0;
						}
					}
					$alert = __('Menu item has been saved!');
					$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/add.php?type=item&menu=" . $post->menu . "');";
					$sql = sprintf("INSERT INTO `plugins_menus_items` "
						. "(`menu`, `path`, `label`, `desc`, `parent_id`, `weight`) "
						. "VALUE ('%s', '%s', '%s', '%s', '%s', '%s')",
						$post->menu,
						$post->path,
						$post->label,
						$post->desc,
						$post->parent,
						$post->weight
					);
					$dbs->query($sql);
				}
			}
		}
		else if (isset($get->item) AND ! empty($get->item))
		{
			if ($valid['item'] === false || empty($post->path) || empty($post->label))
				{
					$alert = __('Menu item has not been saved!');
					if ($valid['item'] === false || empty($post->path))
						$script = "parent.$('input[name=path]').select();";
					else
						$script = "parent.$('input[name=label]').select();";
				}
				else
				{
					if (isset($get->menu) AND $post->parent == $get->menu)
					{
						$post->menu = $post->parent;
						$post->parent = 0;
					}
					else if ($post->parent != $get->menu)
					{
						$post->menu = $get->menu;
						if (is_numeric($post->parent))
						{
							$sql = sprintf("SELECT `menu` FROM `plugins_menus_items` WHERE `parent_id` = '%s'", $post->parent);
							$rows = $dbs->query($sql);
							if ($rows->num_rows > 0)
							{
								$row = (object) $rows->fetch_assoc();
								$post->menu = $row->menu;
							}
						}
						else
						{
							$post->menu = $post->parent;
							$post->parent = 0;
						}
					}
					$alert = __('Menu item has been saved!');
					$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/?menu=" . $post->menu . "');";
					$sql = sprintf("UPDATE `plugins_menus_items` "
						. "SET `menu` = '%s', `path` = '%s', `label` = '%s', `desc` = '%s', `parent_id` = '%s', `weight` = '%s' "
						. "WHERE `item_id` = '%s'",
						$post->menu,
						$post->path,
						$post->label,
						$post->desc,
						$post->parent,
						$post->weight,
						$get->item
					);
					$dbs->query($sql);
				}
		}
	}
	if ( ! empty($alert) AND ! empty($script))
	{
		echo "<html><head><script type=\"text/javascript\">alert('$alert');$script</script></head><body></body></html>";
	}
}
else
{
	$get = (object) $_GET;
	if (isset($get->hide) AND ! empty($get->hide))
	{
		$sql = sprintf("UPDATE `plugins_menus_items` "
			. "SET `hidden` = 1 WHERE `item_id` = '%s'", $get->hide);
		$dbs->query($sql);
	}
	else if (isset($get->unhide) AND ! empty($get->unhide))
	{
		$sql = sprintf("UPDATE `plugins_menus_items` "
			. "SET `hidden` = 0 WHERE `item_id` = '%s'", $get->unhide);
		$dbs->query($sql);
	}
	header("Location: " . $dir . "/?menu=" . $get->menu);
}

exit();
