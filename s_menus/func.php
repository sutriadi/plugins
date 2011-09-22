<?php
/*
 *      func.php
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

/*
 * 
 * name: menu_get
 * @param $menu
 * @param $mode
 * @return
 */
function menu_get($menu, $mode = 'array')
{
	global $dbs;
	$sql = sprintf("SELECT * FROM `plugins_menus` WHERE `menu` = '%s'", $menu);
	$menu = '';
	$title = '';
	$desc = '';
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($num_rows > 0)
	{
		$row = (object) $rows->fetch_assoc();
		$menu = $row->menu;
		$title = $row->title;
		$desc = $row->desc;
	}
	if ($mode == 'array')
		return array($menu, $title, $desc);
	else if ($mode == 'check')
		return $num_rows;
}

/*
 * 
 * name: menu_item_get
 * @param
 * @return
 */
function menu_item_get($item_id, $menu = '')
{
	global $dbs;
	$sql = "SELECT * FROM `plugins_menus_items` ";
	if ( ! empty($menu))
		$sql .= sprintf("WHERE `menu` = '%s' AND `item_id` = '%s'", $menu, $item_id);
	else
		$sql .= sprintf("WHERE `item_id` = '%s'", $item_id);

	$items = array('', 0, '', '', '', '', '', 0, '');
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($num_rows > 0)
	{
		$row = (object) $rows->fetch_assoc();
		$items = array(
			$row->item_id,
			$row->parent_id,
			$row->path,
			$row->label,
			$row->desc,
			$row->hidden,
			$row->external,
			$row->weight,
			$row->customized
		);
	}
	return $items;
}

/*
 * 
 * name: menu_items_get
 * @param
 * @return
 */
function menu_items_get($menu = '')
{
	global $dbs;
	
	$sql = "SELECT `item_id`, `path`, `label`, `desc`, `parent_id`, `weight`, `hidden`, `disabled` "
		. "FROM `plugins_menus_items` ";
	if ( ! empty($menu))
		$sql .= sprintf("WHERE `menu` = '%s' ", $menu);
	$sql .= "ORDER BY `parent_id`, `weight`, `label`";
	$menus = array(
		'items' => array(),
		'parents' => array(),
	);
	$rows = $dbs->query($sql);
	if ($rows->num_rows > 0)
	{
		while ($row = $rows->fetch_assoc())
		{
			$menus['items'][$row['item_id']] = $row;
			$menus['parents'][$row['parent_id']][] = $row['item_id'];
		}
	}
	return $menus;
}

/*
 * 
 * name: menu_build_list
 * @param
 * @return
 */
function menu_build_list($menu_items, $bullet = '+', $parent = 0, $level = 0)
{
	global $dir, $menu, $menus;
	
	$list = '';
	if (isset($menu_items['parents'][$parent]))
	{
		foreach ($menu_items['parents'][$parent] as $itemId)
		{
			$tolevel = $level;
			$link = array();
			$link[] = sprintf('<a href="%s">%s</a>', $dir . '/setup.php?item=' . $itemId, __('Hide'));
			$link[] = sprintf('<a href="%s">%s</a>', $dir . '/add.php?type=item&menu=' . $menu . '&item=' . $itemId, __('Edit'));
			$link[] = sprintf('<a href="%s">%s</a>', $dir . '/add.php?act=del&menu=' . $menu . '&item=' . $itemId, __('Delete'));
			$list .= sprintf('<tr>'
				. '<td>%s %s</td>'
				. '<td>%s</td>'
				. '<td>%s</td>'
				. '<td>%s</td>'
				. '</tr>',
				str_repeat($bullet, $tolevel),
				$menu_items['items'][$itemId]['label'],
				sprintf('<select name="sort[%s][parent]">%s</select>',
					$menu_items['items'][$itemId]['item_id'],
					set_parent_options($menu_items['items'][$itemId]['parent_id'])
				),
				sprintf('<select name="sort[%s][weight]>%s</select>',
					$menu_items['items'][$itemId]['item_id'],
					set_weight_options($menu_items['items'][$itemId]['weight'])
				),
				implode(' | ', $link)
			);
			if (isset($menu_items['parents'][$itemId]))
			{
				$tolevel++;
				$list .= menu_build_list($menu_items, $bullet, $itemId, $tolevel);
			}
		}
	}
	return $list;
}

/*
 * 
 * name: menu_build_links
 * @param
 * @return
 */
function menu_build_links($menus, $parent = 0)
{
	$list = '';
	if (isset($menus['parents'][$parent]))
	{
		$list .= '<ul>';
		foreach ($menus['parents'][$parent] as $itemId)
		{
			if ( ! isset($menus['parents'][$itemId]))
			{
				$list .= sprintf('<li><a href="%s">%s</a></li>',
					$menus['items'][$itemId]['path'],
					$menus['items'][$itemId]['label']
				);
			}
			else
			{
				$list .= sprintf('<li><a href="%s">%s</a>%s</li>',
					$menus['items'][$itemId]['path'],
					$menus['items'][$itemId]['label'],
					menu_build_links($menus, $itemId)
				);
			}
		}
		$list .= '</ul>';
	}
	return $list;
}

function menu_build_array($menus, $parent = 0, $level = 0)
{
	$array = array();
	if (isset($menus['parents'][$parent]))
	{
		foreach ($menus['parents'][$parent] as $itemId)
		{
			$tolevel = $level;
			$array[] = array('val' => $menus['items'][$itemId]['item_id'],
				'label' => str_repeat('-', $tolevel+1) . $menus['items'][$itemId]['label']
			);
			if (isset($menus['parents'][$itemId]))
			{
				$tolevel++;
				$array = array_merge($array, menu_build_array($menus, $itemId, $tolevel));
			}
		}
	}
	return $array;
}

function set_parent_array($menu = '')
{
	global $dbs;
	
	$sql = "SELECT `menu`, `title` FROM `plugins_menus`";
	if ( ! empty($menu))
		$sql .= " WHERE `menu` = '$menu'";
	$rows = $dbs->query($sql);
	$array = array();
	if ($rows->num_rows > 0)
	{
		while ($row = $rows->fetch_assoc())
		{
			$array[] = array('val' => $row['menu'], 'label' => ! empty($menu) ? __('&lt;Root&gt;') : sprintf('&lt;%s&gt;', $row['title']));
			$menus = menu_items_get($row['menu']);
			$array = array_merge($array, menu_build_array($menus));
		}
	}
	return $array;
}

/*
 * 
 * name: set_parent_options
 * @param
 * @return
 */
function set_parent_options($parent_id)
{
	global $menus, $menu;
	
	$opt = '';
	if (count($menus) > 0)
	{
		foreach ($menus as $key => $index)
		{
			$selected = ($index['val'] == $parent_id) ? 'selected' : (($parent_id == 0 AND $index['val'] == $menu) ? 'selected' : '');
			$opt .= sprintf('<option value="%s" %s>%s</option>',
				$index['val'],
				$selected,
				$index['label']
			);
		}
	}
	return $opt;
}
