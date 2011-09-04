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

$en_plugins = '';
$row = 1;
$bgtr = array(0 => 'lightgray', 1 => 'white');
$fgtr = array(0 => 'black', 1 => 'gray');
foreach ($enplugins as $plugin => $info)
{
	$trbg = $bgtr[$row % 2];
	$trfg = $fgtr[$row % 2];
	if (array_key_exists($plugin, $avplugins))
	{
		$disabled = '';
		switch ($info['plugin_type'])
		{
			case 1:
				$on = "onclick=\"$('#mainContent').simbioAJAX('". MODULES_WEB_ROOT_DIR . "plugins/include/" . $plugin . "\"";
				break;
			case 2:
				$on = "onclick=\"window.open('" . MODULES_WEB_ROOT_DIR . 'plugins/include/' . $plugin . "', '$plugin'); return false;\" href=\"#\"";
				break;
			case 0:
			default:
				$on = 'style="display: none;"';
		}
		$en_plugins .= "<tr style=\"background: $trbg; color: $trfg\">"
				."<td><label for=\"ch_$plugin\"><strong>{$info['plugin_name']}</strong></label></td>"
				."<td>{$info['plugin_version']}</td>"
				."<td>{$info['plugin_build']}</td>"
				."<td>"
					. labeltype($info['plugin_type'])
				."</td>"
				."<td><input type=\"button\" value=\"Launch\" $on /></td>"
			."</tr>";
		$row++;
	}
}

if (empty($en_plugins))
	$en_plugins = '<tr align="center"><td colspan="4">' . __('No plugins enabled!') . '</td></tr>';

?>

<?php
	$title = sprintf('%s - %s', __('Plugins'), __('General Informations'));
	echo fs_render($title);
?>

<fieldset>
	<legend><strong><?php echo __('Enabled Plugins List');?></strong></legend>
	<table width="100%" cellspacing="0" cellpadding="5" style="text-align: left;">
		<tr style="background: gray; color: white;">
			<th><?php echo __('Name');?></th>
			<th><?php echo __('Version');?></th>
			<th><?php echo __('Build');?></th>
			<th><?php echo __('Type');?></th>
			<th><?php echo __('Action');?></th>
		</tr>
		<?php echo $en_plugins;?>
	</table>
</fieldset>

<?php
	$_SESSION['plugins_enabled'] = $enplugins;
	unset($en_plugins);
?>
