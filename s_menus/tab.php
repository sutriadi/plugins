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

<!-- formulir mulai -->
<fieldset class="menuBox" style="font-weight: normal;">
	<div style="padding: 3px; padding-left: 59px; background: url(<?php echo MODULES_WEB_ROOT_DIR;?>/plugins/logo.png) no-repeat -10px 5px;">
		<strong>Plugins - <?php echo __('Menu');?></strong>
		<hr />
		<?php echo __('You access this page from IP address');?>: <strong><?php echo remote_addr();?></strong>.
		<?php echo __('This page can accessed from following IP addresses');?>: <strong><?php echo implode(', ', json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true));?></strong>
	</div>
</fieldset>

<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
	<tr>
		<td>
			<input type="button" name="listMenu" value="<?php echo __('List');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/" ;?>');" />
			<input type="button" name="addMenu" value="<?php echo __('Add Menu');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/add_cat.php" ;?>');" />
			<input type="button" name="addMenu" value="<?php echo __('Settings');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/settings.php" ;?>');" />
			<!--
			<input type="button" name="addMenu" value="<?php echo __('Add Item');?>" class="button" onclick="$('#mainContent').simbioAJAX('<?php echo $dir . "/add_item.php" ;?>');" />
			-->
		</td>
		<td align="right">
		</td>
	</tr>
</table>
