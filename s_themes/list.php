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

$theme_list = '';
$row = 1;
$bgtr = array(0 => 'lightgray', 1 => 'white');
$fgtr = array(0 => 'black', 1 => 'gray');
foreach ($themes as $theme_key => $theme_name)
{
	$info = $theme_key == $theme_name ? array() : drupal_parse_info_file($theme_base_dir . $theme_key . '/tpl.info');
	$theme_list .= 	sprintf('<tr style="background: %s; color: %s">'
			. '<td>%s</td>'
			. '<td>%s</td>'
			. '<td>%s</td>'
			. '<td>%s</td>'
		. '</tr>',
		$bgtr[$row % 2],
		$fgtr[$row % 2],
		(isset($info['screenshot']) AND file_exists($theme_base_dir . $theme_key . '/' . $info['screenshot'])) ? '<img src=' . $theme_base_url . $theme_key . '/' . $info['screenshot'] . ' />' : __('No screenshot'),
		(isset($info['name'])) ? "<strong>{$info['name']}</strong>" . (( isset($info['description'])) ? "<br /><span>{$info['description']}</span>" : '') : $theme,
		(isset($info['version'])) ? $info['version'] . (( isset($info['build'])) ? '<br />' . __('build') . ' ' . $info['build'] : '') : '',
		sprintf('<a href="%s">%s</a>', $dir . '/?act=configure&theme=' . $theme_key, __('Configure'))
	);
	$row++;
}
?>

<table width="100%" cellspacing="0" cellpadding="5" style="text-align: left;">
	<thead>
		<tr style="background: gray; color: white;">
			<th><?php echo __('Screenshot');?></th>
			<th><?php echo __('Name');?></th>
			<th><?php echo __('Version');?></th>
			<th><?php echo __('Operations');?></th>
		</tr>
	</thead>
	<tbody><?php echo $theme_list;?></tbody>
	<tfoot>
		<tr style="background: gray; color: white;">
			<th><?php echo __('Screenshot');?></th>
			<th><?php echo __('Name');?></th>
			<th><?php echo __('Version');?></th>
			<th><?php echo __('Operations');?></th>
		</tr>
	</tfoot>
</table>
