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

function menu_items_get($menu = '')
{
	global $dbs;
	
	$sql = "SELECT `item_id`, `path`, `label`, `desc`, `parent_id` "
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

function menu_build_links($menus, $parent = 0)
{
	
}

function menu_build_list($menus, $parent = 0)
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
					menu_build_list($menus, $itemId)
				);
			}
		}
		$list .= '</ul>';
	}
	return $list;
}

function menu_build_opt($menus, $parent = 0)
{
	$opt = '';
	if (isset($menus['parents'][$parent]))
	{
		foreach ($menus['parents'][$parent] as $itemId)
		{
			$opt .= sprintf('<option value="%s">%s</option>',
				$itemId,
				str_repeat('-', $parent+1) . $menus['items'][$itemId]['label']
			);
			if (isset($menus['parents'][$itemId]))
			{
				$opt .= menu_build_opt($menus, $itemId);
			}
		}
	}
	return $opt;
}

function set_parent_options($parent_id)
{
	global $dbs;
	
	$sql = "SELECT `menu`, `title` FROM `plugins_menus`";
	$rows = $dbs->query($sql);
	$options = '';
	if ($rows->num_rows > 0)
	{
		while ($row = $rows->fetch_assoc())
		{
			$options .= sprintf('<option value="%s">%s</option>',
				$row['menu'],
				$row['title']
			);
			$menus = menu_items_get($row['menu']);
			$options .= menu_build_opt($menus);
		}
	}
	return $options;
}

function display_children($parent, $level)
{
    $sql = "SELECT `a`.`item_id`, `a`.`label`, `a`.`path`, Deriv1.Count "
		. "FROM `plugins_menu_items` `a` "
		. "LEFT OUTER JOIN (SELECT `parent_id`, COUNT(*) AS `cnt` FROM `plugins_menu_items` GROUP BY `parent_id`) `deriv` ON `a`.`item_id` = `defiv`.`parent_id` "
		. "WHERE `a`.`parent_id` = " . $parent;
/*
    echo "<ul>";
    while ($row = mysql_fetch_assoc($result)) {
        if ($row['Count'] > 0) {
            echo "<li><a href='" . $row['link'] . "'>" . $row['label'] . "</a>";
			display_children($row['id'], $level + 1);
			echo "</li>";
        } elseif ($row['Count']==0) {
            echo "<li><a href='" . $row['link'] . "'>" . $row['label'] . "</a></li>";
        } else;
    }
    echo "</ul>";
*/
}

/*
$result=mysql_query("SELECT item_id, label, link, parent FROM menu ORDER BY parent, sort, label");
// Create a multidimensional array to conatin a list of items and parents
$menu = array(
    'items' => array(),
    'parents' => array()
);
// Builds the array lists with data from the menu table
while ($items = mysql_fetch_assoc($result))
{
    // Creates entry into items array with current menu item id ie. $menu['items'][1]
    $menu['items'][$items['id']] = $items;
    // Creates entry into parents array. Parents array contains a list of all items with children
    $menu['parents'][$items['parent']][] = $items['id'];
}
*/

// Menu builder function, parentId 0 is the root
/*
function buildMenu($parent, $menu)
{
   $html = "";
   if (isset($menu['parents'][$parent]))
   {
      $html .= "
      <ul>\n";
       foreach ($menu['parents'][$parent] as $itemId)
       {
          if(!isset($menu['parents'][$itemId]))
          {
             $html .= "<li>\n  <a href='".$menu['items'][$itemId]['link']."'>".$menu['items'][$itemId]['label']."</a>\n</li> \n";
          }
          if(isset($menu['parents'][$itemId]))
          {
             $html .= "
             <li>\n  <a href='".$menu['items'][$itemId]['link']."'>".$menu['items'][$itemId]['label']."</a> \n";
             $html .= buildMenu($itemId, $menu);
             $html .= "</li> \n";
          }
       }
       $html .= "</ul> \n";
   }
   return $html;
}
echo buildMenu(0, $menu);
*/
