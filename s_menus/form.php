<?php
/*
 *      form.php
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

$get = (object) $_GET;

$ro_menu = '';
$ro_item = '';
$params = '';
$mode = isset($get->type) ? $get->type : 'menu';
list($menu, $title, $desc) = (isset($get->menu) AND ! empty($get->menu)) ? menu_get($get->menu) : array('', '', '');
if ( ! empty($menu))
{
	$params .= 'menu=' . $menu;
	$mode = ( ! empty($menu) AND isset($get->type)) ? $get->type : 'menu';
	$desc = $mode == 'menu' ? $desc : '';
	$menus = set_parent_array();
	list($item, $parent, $path, $label, $desc, $hidden, $external, $weight, $customized) = (isset($get->item) AND ( ! empty($get->item) || $get->item != 0)) ? menu_item_get($get->item, $menu) : array('',0,'','',$desc,'','',0,'');
	if ( ! empty($item))
	{
		$mode = 'item';
		$params .= '&item=' . $item;
	}
	$as_main_links = variable_get('main_links', '') == $menu ? 'checked' : '';
}

?>

<?php if (isset($get->act) AND $get->act == 'del'): ?>
	<?php if ( ! empty($item) AND ! empty($menu)): ?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?act=del&" . $params;?>" target="submitExec">
<p>
	<?php echo __('Are you sure to delete menu item');?>: <strong><?php echo $label;?></strong>?
	<input type="hidden" name="item" value="<?php echo $item;?>" />
	<input type="submit" value="<?php echo __('Delete');?>" />
	<input type="button" onclick="parent.$('#mainContent').simbioAJAX('<?php echo $dir . '/';?>');" value="<?php echo __('Cancel');?>" />
</p>
</form>

	<?php else: ?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?act=del&" . $params;?>" target="submitExec">
<p>
	<?php echo __('Are you sure to delete menu');?>: <strong><?php echo $title;?></strong>?
	<input type="hidden" name="menu" value="<?php echo $menu;?>" />
	<input type="submit" value="<?php echo __('Delete');?>" />
	<input type="button" onclick="parent.$('#mainContent').simbioAJAX('<?php echo $dir . '/';?>');" value="<?php echo __('Cancel');?>" />
</p>
</form>

	<?php endif;?>

<?php else: ?>
	<?php if ($mode == 'menu'):?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?" . $params;?>" target="submitExec">
	<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
		<tr>
			<td>
				<input type="submit" name="saveData" value="<?php echo __('Save');?>" class="button" />
			</td>
			<td align="right">
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="5" cellspacing="0" style="border-collapsed: collapsed;" id="dataList">
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="menu" style="cursor: pointer;"><?php echo __('Menu');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="menu" name="menu" type="text" size="50" value="<?php echo $menu;?>" <?php echo $ro_menu;?> />
				<br />
				<span>
					<?php echo __('A unique name to construct the URL for the menu. It must only contain lowercase letters, numbers, underscore or dash.');?>
				</span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="path" style="cursor: pointer;"><?php echo __('Title');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="title" name="title" type="text" size="50" value="<?php echo $title;?>" />
				<br />
				<span><?php echo __('Display name of menu.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="label" style="cursor: pointer;"><?php echo __('Description');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="desc" name="desc" type="text" size="50" value="<?php echo $desc;?>" />
				<br />
				<span><?php echo __('Description of your menu.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="as_main_links" style="cursor: pointer;"><?php echo __('Main Links');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="as_main_links" name="as_main_links" size="50" type="checkbox" <?php echo $as_main_links;?> />
				<label for="as_main_links" style="cursor: pointer;"><?php echo __('Yes!');?></label>
				<br />
				<span><?php echo __('Check it if you want to use this menu as main links.');?></span>
			</td>
		</tr>
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

	<?php elseif ($mode == 'item'): ?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?" . $params;?>" target="submitExec">
	<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
		<tr>
			<td>
				<input type="submit" name="saveData" value="<?php echo __('Save');?>" class="button" />
			</td>
			<td align="right">
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="5" cellspacing="0" style="border-collapsed: collapsed;" id="dataList">
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="path" style="cursor: pointer;"><?php echo __('Path');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="path" name="path" type="text" size="50" value="<?php echo $path;?>" />
				<br />
				<span><?php echo __('The path this menu item links to. This can relative or an external URL.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="label" style="cursor: pointer;"><?php echo __('Label');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="label" name="label" type="text" size="50" value="<?php echo $label;?>" />
				<br />
				<span><?php echo __('The link text corresponding to this item that should appear in the menu.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="desc" style="cursor: pointer;"><?php echo __('Description');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="desc" name="desc" type="text" size="50" value="<?php echo $desc;?>" />
				<br />
				<span><?php echo __('The description displayed when hovering over a menu item.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="parent" style="cursor: pointer;"><?php echo __('Parent Item');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="parent" name="parent"><?php echo set_parent_options($parent);?></select>
				<br />
				<span><?php echo __('The maximum depth for an item and all its children is fixed at 9. Some menu items may not be available as parents if selecting them would exceed this limit.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="weight" style="cursor: pointer;"><?php echo __('Weight');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="weight" name="weight"><?php echo set_weight_options($weight);?></select>
				<br />
				<span><?php echo __('Optional. In the menu, the heavier items will sink and the lighter items will be positioned nearer the top.');?></span>
			</td>
		</tr>
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

	<?php endif; ?>
<?php endif; ?>

<iframe name="submitExec" class="noBlock" style="visibility: visible; width: 100%; height: 10;"></iframe>
