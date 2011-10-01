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

$ro_block = '';
$params = '';
$plugin = 'block';

$get = (object) $_GET;

list($block, $desc, $title, $code, $filter) = (isset($get->block) AND isset($get->delta) AND $get->block == 'block') ? block_custom_get($get->delta) : array('', '', '', '', '');

if (isset($get->block) AND isset($get->delta))
{
	$get->block = ! empty($get->block) ? $get->block : 'block';
	$get->delta = ! empty($get->delta) ? $get->delta : '';
	list($plugin, $delta, $region, $weight, $title, $classes) = isset($get->delta) ? block_settings_get($get->block, $get->delta) : array('', '', '', '');
	$default_regions = block_default_regions();
	$ro_block = 'readonly';
	$params = '?plugin=' . $plugin . '&delta=' . $delta . '&theme=' . $theme;
}

$filters = array(
	'text' => __('Plain Text'),
	'simple' => __('Filtered HTML'),
	'full' => __('Full HTML'),
	'php' => __('PHP Code')
);

$opt_filter = '';
foreach ($filters as $f => $val)
{
	$selected = ($f == $filter) ? "selected" : '';
	$opt_filter .= sprintf('<option value="%s" %s>%s</option>', $f, $selected, $val);
}

?>

<?php if (isset($get->act) AND $get->act == 'del'): ?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?act=del&delta=" . $delta;?>" target="submitExec">
<p>
	<?php echo __('Are you sure to delete block');?>: <strong><?php echo $desc;?></strong>?
	<input type="hidden" name="table" value="<?php echo $block;?>" />
	<input type="submit" value="<?php echo __('Delete');?>" />
	<input type="button" onclick="parent.$('#mainContent').simbioAJAX('<?php echo $dir . '/';?>');" value="<?php echo __('Cancel');?>" />
</p>
</form>

<?php else: ?>
<form name="mainForm" id="mainForm" enctype="multipart/form-data" method="POST" action="<?php echo $dir . "/setup.php" . $params;?>" target="submitExec">
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
			<td class="alterCell" style="font-weight: bold;"><label for="block" style="cursor: pointer;"><?php echo __('Block');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="block" name="block" type="text" size="50" value="<?php echo $block;?>" <?php echo $ro_block;?> />
				<br />
				<span><?php echo __('A unique name to construct the machine name for the block. It must only contain lowercase letters, numbers, underscore or dash.');?></span>
			</td>
		</tr>
	<?php if ($plugin == 'block'): ?>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="desc" style="cursor: pointer;"><?php echo __('Description');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="desc" name="desc" type="text" size="50" value="<?php echo $desc;?>" />
				<br />
				<span><?php echo __('Description of your block and will display on block list.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="title" style="cursor: pointer;"><?php echo __('Title');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="title" name="title" type="text" size="50" value="<?php echo $title;?>" />
				<br />
				<span><?php echo __('Title of your block and will display on block header.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="code" style="cursor: pointer;"><?php echo __('Content');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<textarea id="code" name="code" rows="5" style="width: 100%;"><?php echo $code;?></textarea>
				<br />
				<span><?php echo __('Content of block.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="filter" style="cursor: pointer;"><?php echo __('Filter');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="filter" name="filter"><?php echo $opt_filter;?></select>
				<br />
				<span><?php echo __('Type of filter.');?></span>
			</td>
		</tr>

	<?php else: ?>

		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="title" style="cursor: pointer;"><?php echo __('Title');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="title" name="title" type="text" size="50" value="<?php echo $title;?>" />
				<br />
				<span><?php echo __('Title of your block and will display on block header.');?></span>
			</td>
		</tr>

	<?php endif;?>

	<?php if (isset($region) AND isset($weight)): ?>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="region" style="cursor: pointer;"><?php echo __('Region');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="region" name="region"><?php echo set_region_options($region);?></select>
				<br />
				<span><?php echo __('Select region of block.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="weight" style="cursor: pointer;"><?php echo __('Weight');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="weight" name="weight"><?php echo set_weight_options($weight);?></select>
				<br />
				<span><?php echo __('Select weight of block.');?></span>
			</td>
		</tr>
	<?php endif; ?>
	<?php if (isset($classes)): ?>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="classes" style="cursor: pointer;"><?php echo __('Additional Classes');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="classes" name="classes" type="text" size="50" value="<?php echo $classes;?>" />
				<br />
				<span>
					<?php echo __('Type the name of the CSS class that will be added to the block.');?>
					<?php echo __('You may define multiples classes separated by spaces.');?>
				</span>
			</td>
		</tr>
	<?php endif; ?>

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
<?php endif; ?>

<iframe name="submitExec" class="noBlock" style="visibility: visible; width: 100%; height: 10;"></iframe>
