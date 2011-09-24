<?php
/*
 *      tab.php
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

?>

<?php
	$subtitle = isset($subtitle) ? ' ' . $subtitle : '';
	$title = sprintf('%s - %s', __('Plugins'), __('Menus')) . $subtitle;
	echo fs_render($title);
?>

<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
	<tr>
		<td>
			<input type="button" name="listMenu" value="<?php echo __('List');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/" ;?>');" />
			<input type="button" name="addMenu" value="<?php echo __('Add Menu');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/add.php?type=menu" ;?>');" />
		</td>
		<td align="right">
		</td>
	</tr>
</table>

<?php if ($item_tab === true):?>

<table style="width: 100%; background-color: rgb(220, 220, 220);" cellpadding="3" cellspacing="0">
	<tbody>
		<tr>
			<td>
				<input type="submit" value="<?php echo __('List Items');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/?menu=" . $menu ;?>');" />
				<input type="submit" value="<?php echo __('Add Menu Item');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/add.php?type=item&menu=" . $menu ;?>');" />
			</td>
			<td align="right">
			</td>
		</tr>
	</tbody>
</table>

<?php endif;?>
