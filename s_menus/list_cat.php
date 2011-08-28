<?php
/*
 *      list_cat.php
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

if (!defined('MODULES_WEB_ROOT_DIR')) {
	exit();
}

$sql = "SELECT * FROM `plugins_menus`";
$menus = $dbs->query($sql);
$list_cat = '<tbody>';
$list_item_label = __('List Items');
$add_item_label = __('Add Item');
$edit_menu_label = __('Edit');
$del_menu_label = __('Delete');
if ($menus AND $menus->num_rows > 0)
{
	while ($menu = $menus->fetch_assoc())
	{
		$list_item_click = "$('#mainContent').simbioAJAX('$dir/list_item.php?menu={$menu['menu']}');";
		$add_item_click = "$('#mainContent').simbioAJAX('$dir/add_item.php?menu={$menu['menu']}');";
		$edit_menu_click = "$('#mainContent').simbioAJAX('$dir/add_cat.php?menu={$menu['menu']}');";
		$del_menu_click = "$('#mainContent').simbioAJAX('$dir/add_cat.php?action=del&menu={$menu['menu']}');";
		$list_cat .= "<tr>"
			. "<td>{$menu['menu']}</td>"
			. "<td>{$menu['title']}</td>"
			. "<td>{$menu['desc']}</td>"
			. "<td>"
				. "<input type=\"button\" value=\"$list_item_label\" onclick=\"$list_item_click\" /> "
				. "<input type=\"button\" value=\"$add_item_label\" onclick=\"$add_item_click\" /> "
				. "<input type=\"button\" value=\"$edit_menu_label\" onclick=\"$edit_menu_click\" /> "
				. "<input type=\"button\" value=\"$del_menu_label\" onclick=\"$del_menu_click\" /> "
			. "</td>"
		. "</tr>";
		unset($add_item_click);
		unset($edit_menu_click);
		unset($del_menu_click);
	}
}
else
{
	$list_cat .= "<tr><td colspan=\"4\" align=\"center\">" . __('Menu empty!') . "</td></tr>";
}

$list_cat .="</tbody>";

?>

	<table width="100%" cellspacing="0" cellpadding="5" style="text-align: left;">
		<tr style="background: gray; color: white;">
			<th><?php echo __('Name');?></th>
			<th><?php echo __('Title');?></th>
			<th><?php echo __('Description');?></th>
			<th><?php echo __('Action');?></th>
		</tr>
		<?php echo $list_cat;?>
	</table>
