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

list($host, $dir, $file) = scinfo();
$ips = implode(" ", json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true));
$ui_theme = variable_get('ui_theme', 'base');
$ui_css_version = variable_get('ui_css_version', '');

?>

<!-- formulir mulai -->
<fieldset class="menuBox" style="font-weight: normal;">
	<div style="padding: 3px; padding-left: 59px; background: url(<?php echo MODULES_WEB_ROOT_DIR;?>/plugins/logo.png) no-repeat -10px 5px;">
		<strong>Plugins - <?php echo __('Configure');?></strong>
		<hr />
		<?php echo __('You access this page from IP address');?>: <strong><?php echo remote_addr();?></strong>.
	</div>
</fieldset>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php";?>" target="submitExec">
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
			<td class="alterCell" style="font-weight: bold;"><label for="ips" style="cursor: pointer;"><?php echo __('Allowed IPs');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="ips" name="ips" type="text" size="50" value="<?php echo $ips;?>" />
				<br />
				<span><?php echo __('Separate IP addresses with space');?></span>
			</td>
		</tr>
		<tr valign=top>
			<th colspan="4">OPAC</th>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="opac_theme" style="cursor: pointer;"><?php echo __('OPAC Plugin Theme');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="opac_theme" name="opac_theme">
					<option value="base">Base</option>
				</select>
				<br />
				<span><?php echo __('Not implemented yet!');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="opac_theme" style="cursor: pointer;"><?php echo __('OPAC Frontpage');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="text" id="opac_frontpage" name="opac_frontpage" />
				<br />
				<span><?php echo __('Not implemented yet!');?></span>
			</td>
		</tr>
		<tr valign=top>
			<th colspan="4">jQuery-UI</th>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="ui_theme" style="cursor: pointer;"><?php echo __('JQuery UI Theme');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="text" id="ui_theme" name="ui_theme" value="<?php echo $ui_theme;?>" />
				<br />
				<span><?php echo __('Entry jQuery-UI Theme for new window plugins theme.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="ui_css_version" style="cursor: pointer;"><?php echo __('JQuery UI CSS Version');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="text" id="ui_css_version" name="ui_css_version" value="<?php echo $ui_css_version;?>" />
				<br />
				<span><?php echo __('Entry jQuery-UI CSS version. Leave blank or enter 0 for use css file without version.');?></span>
			</td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
		<tr>
			<td>
				<input type="submit" name="saveData" value="<?php echo __('Save');?>" class="button" />
			</td>
			<td align="right"></td>
		</tr>
	</table>
</form>
<iframe name="submitExec" class="noBlock" style="visibility: hidden; width: 100%; height: 0;"></iframe>
