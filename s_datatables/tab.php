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
	$title = sprintf('%s - %s', __('Plugins'), __('DataTables')) . $subtitle;
	echo fs_render($title);
?>

<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
	<tr>
		<td>
			<input type="button" name="listTables" value="<?php echo __('List');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/" ;?>');" />
			<input type="button" name="addTable" value="<?php echo __('Add DataTables');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/add.php" ;?>');" />
		</td>
		<td align="right">
		</td>
	</tr>
</table>
