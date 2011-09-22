<?php
/*
 *      list.php
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

$mode = 'menu';
if ( ! empty($menu))
{
	$mode = 'item';
	$menu_items = menu_items_get($menu);
	$menus = set_parent_array($menu);
	$items = menu_build_list($menu_items);
	$list_item = sprintf('<tbody>%s</tbody>',
		( ! empty($items)) ? $items : sprintf('<tr><td colspan="2" align="center">%s</td></tr>', __('There are no menu items listed!'))
	);
}

if ($mode == 'menu'):
	$sql = "SELECT * FROM `plugins_menus`";
	$menus = $dbs->query($sql);
	$list_cat = '<tbody>';
	$list_item_label = __('List Items');
	$add_item_label = __('Add Menu Item');
	$edit_menu_label = __('Edit');
	$del_menu_label = __('Delete');
	if ($menus AND $menus->num_rows > 0)
	{
		while ($menu = $menus->fetch_assoc())
		{
			$list_item_click = "$('#mainContent').simbioAJAX('$dir/?menu={$menu['menu']}');";
			$add_item_click = "$('#mainContent').simbioAJAX('$dir/add.php?type=item&menu={$menu['menu']}');";
			$edit_menu_click = "$('#mainContent').simbioAJAX('$dir/add.php?menu={$menu['menu']}');";
			$del_menu_click = "$('#mainContent').simbioAJAX('$dir/add.php?act=del&menu={$menu['menu']}');";
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
		$list_cat .= "<tr><td colspan=\"4\" align=\"center\">" . __('There are no menus listed!') . "</td></tr>";
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

<?php

else:
	
?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?sort&menu=" . $menu;?>" target="submitExec">
	<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
		<tr>
			<td>
				<input type="submit" name="saveData" value="<?php echo __('Save');?>" class="button" />
			</td>
			<td align="right">
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing="0" cellpadding="5" style="text-align: left;">
		<tr style="background: gray; color: white;">
			<th><?php echo __('Item');?></th>
			<th><?php echo __('Parent');?></th>
			<th><?php echo __('Weight');?></th>
			<th><?php echo __('Action');?></th>
		</tr>
		<?php echo $list_item;?>
	</table>
	<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
		<tr>
			<td>
				<input type="submit" name="saveData" value="<?php echo __('Save');?>" class="button" />
			</td>
			<td align="right">
			</td>
		</tr>
	</table>
</form>
<iframe name="submitExec" class="noBlock" style="visibility: visible; width: 100%; height: 10;"></iframe>

<?php

endif;

?>
