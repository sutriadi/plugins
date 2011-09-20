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

$themes = list_avtheme();
$blocks = block_all_list();

$default_regions = block_default_regions();

$block_list = '';

foreach ($default_regions as $region => $region_name)
{
	if (array_key_exists($region, $blocks))
	{
		if (count($blocks[$region]) > 0)
		{
			$block_list .= sprintf('<tr valign=top><th colspan="4">%s</th></tr>', $region_name);
			foreach ($blocks[$region] as $bargs)
			{
				$block_list .= sprintf('<tr valign="top">'
						. '<td class="alterCell" style="font-weight: bold; width: 100px;">'
						. '<label for="%s" style="cursor: pointer;">%s</label></td>'
						. '<td class="alterCell" style="font-weight: bold; width: 3px;">:</td>'
						. '<td class="alterCell2">'
						. '<select id="%s" name="%s[%s][region]">%s</select> '
						. '<select name="%s[%s][weight]">%s</select> '
						. '%s'
						. '</td>'
					. '</tr>',
					'input_' . $bargs['desc'], // label for
					$bargs['desc'], // label
					'input_' . $bargs['desc'], // select id
					$bargs['block'],
					$bargs['delta'],
					set_region_options($region),
					$bargs['block'],
					$bargs['delta'],
					set_weight_options($bargs['weight']),
					set_action_links($bargs)
				);
			}
		}
	}
	else
	{
		$block_list .= sprintf('<tr valign=top><th colspan="4">%s</th></tr>', $region_name);
		$block_list .= sprintf('<tr valign=top><td colspan="4" class="alterCell2">%s</td></tr>', __('There are no blocks listed'));
	}

}

$theme_list = array();
foreach ($themes as $theme_key => $theme_name)
{
	if ($theme != $theme_key)
		$theme_list[] = sprintf('<a href="%s">%s</a>', $dir . '/?theme=' . $theme_key, $theme_name);
	else
		$theme_list[] = sprintf('<a href="%s" style="font-weight: bold; font-style: italic;">%s</a>', $dir . '/?theme=' . $theme_key, $theme_name);
}
$theme_list = sprintf('%s : ', __('Select theme')) . implode(" | ", $theme_list);

?>
<form name="mainForm" id="mainForm" enctype="multipart/form-data" method="POST" action="<?php echo $dir . "/setup.php?sort&theme=" . $theme; ?>" target="submitExec">
	<table cellspacing="0" cellpadding="3" style="width: 100%; background-color: #dcdcdc;">
		<tr>
			<td>
				<?php echo $theme_list;?>
			</td>
			<td align="right">
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="saveData" value="<?php echo __('Save');?>" class="button" />
			</td>
			<td align="right">
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="5" cellspacing="0" style="border-collapsed: collapsed;" id="dataList">
		<?php echo $block_list;?>
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
<iframe name="submitExec" class="noBlock" style="visibility: visible; width: 100%; "></iframe>

