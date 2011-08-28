<?php
/*
 *      cat_add.php
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

$get = (isset($_GET) AND isset($_GET['menu'])) ? true : false;
$ro_menu = '';
$params = '';
list($menu, $title, $desc) = ($get === true) ? menu_get($_GET['menu']) : array('', '', '');
if ( ! empty($menu))
{
	$ro_menu = 'readonly';
	$params = '?menu=' . $menu;
}

?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/set_cat.php" . $params;?>" target="submitExec">
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
			<td class="alterCell" style="font-weight: bold;"><label for="path" style="cursor: pointer;"><?php echo __('Menu');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="menu" name="menu" type="text" size="50" value="<?php echo $menu;?>" <?php echo $ro_menu;?> />
				<br />
				<span><?php echo __('A unique name to construct the URL for the menu. It must only contain lowercase letters, numbers, underscore or dash.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="path" style="cursor: pointer;"><?php echo __('Title');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="title" name="title" type="text" size="50" value="<?php echo $title;?>" />
				<br />
				<span><?php echo __('Display name of menu');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="label" style="cursor: pointer;"><?php echo __('Description');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="desc" name="desc" type="text" size="50" value="<?php echo $desc;?>" />
				<br />
				<span><?php echo __('Description of your menu');?></span>
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
<iframe name="submitExec" class="noBlock" style="visibility: visible; width: 100%;"></iframe>
