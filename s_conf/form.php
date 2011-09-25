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
$allowed_ip = implode(" ", json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true));
$opac_theme = variable_get('opac_theme', 'base');
$opac_frontpage = variable_get('opac_frontpage', '');
$ui_theme = variable_get('ui_theme', 'base');
$ui_css_version = variable_get('ui_css_version', '');
$allowed_tags = variable_get('allowed_tags', '<a> <em> <strong> <cite> <code> <ul> <ol> <li> <dl> <dt> <dd>');
$main_links = variable_get('main_links', 'primary-links');
$main_links_items = variable_get('main_links_items', 'top');

$nodir = array('.', '..');
$links_type = array('top' => __('Top items only'), 'full' => __('All items'));

require(SENAYAN_BASE_DIR . 'template/fatin/php/function.php');
$list_avtheme = list_avtheme();
$opt_opac_theme = '';
foreach ($list_avtheme as $theme_dir => $theme_name)
{
	$selected = ($opac_theme == $theme_dir) ? 'selected' : '';
	$opt_opac_theme .= sprintf('<option value="%s" %s>%s</option>',
		$theme_dir,
		$selected,
		$theme_name
	);
}

$dir_ui_theme = __DIR__ . '/../library/ui/css/';
$dirs_ui_theme = scandir($dir_ui_theme);
sort($dirs_ui_theme);
$opt_ui_theme = '';
foreach ($dirs_ui_theme as $file_ui_theme)
{
	if ( ! in_array($file_ui_theme, $nodir) AND is_dir($dir_ui_theme . $file_ui_theme))
	{
		$selected = ($ui_theme == $file_ui_theme) ? 'selected' : '';
		$opt_ui_theme .= sprintf('<option value="%s" %s>%s</option>',
			$file_ui_theme,
			$selected,
			$file_ui_theme
		);
	}
}

require(MODPLUGINS_BASE_DIR . 's_menus/func.php');
$list_menu = set_parent_array('', false, true, 0, 0, false, false);
$opt_main_links = '';
foreach ($list_menu as $key => $val)
{
	if (is_array($val))
	{
		$selected = ($main_links == $val['val']) ? 'selected' : '';
		$opt_main_links .= sprintf('<option value="%s" %s>%s</option>',
			$val['val'],
			$selected,
			$val['label']
		);
	}
}

$opt_main_links_items = '';
foreach ($links_type as $key => $val)
{
	$selected = $key == $main_links_items ? 'selected' : '';
	$opt_main_links_items .= sprintf('<option value="%s" %s>%s</option>',
		$key,
		$selected,
		$val
	);
}

?>

<?php
	$title = sprintf('%s - %s', __('Plugins'), __('Configure'));
	echo fs_render($title, array('ip_detail' => false));
?>

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
			<td class="alterCell" style="font-weight: bold;"><label for="allowed_ip" style="cursor: pointer;"><?php echo __('Allowed IPs');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="allowed_ip" name="allowed_ip" type="text" size="50" value="<?php echo $allowed_ip;?>" />
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
				<select id="opac_theme" name="opac_theme"><?php echo $opt_opac_theme;?></select>
				<br />
				<span><?php echo __('Select OPAC Theme which compatible with Fatin template engine!');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="opac_frontpage" style="cursor: pointer;"><?php echo __('OPAC Frontpage');?></label></td>
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
				<select id="ui_theme" name="ui_theme"><?php echo $opt_ui_theme;?></select>
				<br />
				<span><?php echo __('Select jQuery-UI Theme for new window plugins theme.');?></span>
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
		<tr valign=top>
			<th colspan="4"><?php echo __('Main Links');?></th>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="main_links" style="cursor: pointer;"><?php echo __('Menu as Main Links');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="main_links" name="main_links"><?php echo $opt_main_links;?></select>
				<br />
				<span><?php echo __('Select menu to use as main links.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="main_links_items" style="cursor: pointer;"><?php echo __('Main Links Itema');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="main_links_items" name="main_links_items"><?php echo $opt_main_links_items;?></select>
				<br />
				<span><?php echo __('Select main links type.');?></span>
			</td>
		</tr>
		<tr valign=top>
			<th colspan="4"><?php echo __('Input Formats');?></th>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="allowed_tags" style="cursor: pointer;"><?php echo __('Allowed HTML Tags');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="text" id="allowed_tags" style="width:100%;" name="allowed_tags" value="<?php echo $allowed_tags;?>" />
				<br />
				<span><?php echo __('Specify tags which should not be stripped.');?></span>
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
