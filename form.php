<?php
/*
 *      form.php
 *      
 *      Copyright 2011 Indra Sutriadi Pipii <indra.sutriadi@gmail.com>
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

$av_plugins = '';
$row = 1;
$bgtr = array(0 => 'lightgray', 1 => 'white');
$fgtr = array(0 => 'black', 1 => 'gray');
foreach ($avplugins as $plugin => $info)
{
	$checked = array_key_exists($plugin, $enplugins) ? 'checked' : '';
	$trbg = $bgtr[$row % 2];
	$trfg = $fgtr[$row % 2];
	$av_plugins .= "<tr id =\"tr_$plugin\" style=\"background: $trbg; color: $trfg\">"
			."<td align=\"center\"><input type=\"checkbox\" $checked id=\"ch_$plugin\" name=\"$plugin\" /></td>"
			."<td><label for=\"ch_$plugin\" style=\"cursor: pointer; cursor: hand;\"><strong>{$info['plugin_name']}</strong></label></td>"
			."<td>{$info['plugin_version']}</td>"
			."<td>{$info['plugin_build']}</td>"
			."<td>"
				.labeltype($info['plugin_type'])
			."</td>"
			."<td>{$info['plugin_description']}</td>"
		."</tr>";
	$row++;
}

if (empty($av_plugins))
	$av_plugins = '<tr align="center"><td colspan="5">' . __('No plugins availabled') . '</td></tr>';

?>

<!-- formulir mulai -->
<fieldset>
	<legend><strong><?php echo __('Available Plugins');?></strong></legend>
	<form name="mainForm" id="mainForm" method="POST" action="<?php echo MODULES_WEB_ROOT_DIR . "plugins/setup.php";?>" target="submitExec">
		<table width="100%" cellpadding="5" cellspacing="0" style="border-collapsed: collapsed;">
			<thead style="border: 2px solid gray;">
				<tr>
					<td colspan="5">
						<input type="submit" name="save" value="<?php echo __('Save');?>" />
						<input type="button" value="<?php echo __('Check All');?>" onclick="submitExec.checkall(this.form);" />
						<input type="button" value="<?php echo __('Check Invert');?>" onclick="submitExec.checkinvert(this.form);" />
						<input type="button" value="<?php echo __('Uncheck All');?>" onclick="submitExec.uncheckall(this.form);" />
					</td>
				</tr>
				<tr align="left" style="background: gray; color: white;">
					<th width="50px"><?php echo __('Enabled');?></th>
					<th width="100px"><?php echo __('Name');?></th>
					<th width="50px"><?php echo __('Version');?></th>
					<th width="50px"><?php echo __('Build');?></th>
					<th width="50px"><?php echo __('Type');?></th>
					<th><?php echo __('Description');?></th>
				</tr>
			</thead>
			<tbody><?php echo $av_plugins;?></tbody>
			<tfoot>
				<tr>
					<td colspan="4">
						<input type="submit" name="save" value="<?php echo __('Save');?>" />
						<input type="button" value="<?php echo __('Check All');?>" onclick="submitExec.checkall(this.form);" />
						<input type="button" value="<?php echo __('Check Invert');?>" onclick="submitExec.checkinvert(this.form);" />
						<input type="button" value="<?php echo __('Uncheck All');?>" onclick="submitExec.uncheckall(this.form);" />
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</fieldset>

<?php
	$_SESSION['plugins_available'] = $avplugins;
	unset($av_plugins);
?>

<iframe src="<?php echo MODULES_WEB_ROOT_DIR . "plugins/script.php";?>" name="submitExec" class="noBlock" style="visibility: hidden; width: 100%; height:0;"></iframe>
<!-- formulir akhir -->
