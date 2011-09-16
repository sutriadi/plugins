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

list($block, $desc, $title, $code, $filter) = (isset($_GET) AND isset($_GET['block'])) ? block_custom_get($_GET['block']) : array('', '', '', '', '');
if ( ! empty($table))
{
	$ro_table = 'readonly';
	$params = '?block=' . $block;
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

if (isset($_GET) AND isset($_GET['']))
{
	
}
?>

<?php if (isset($_GET['action']) AND $_GET['action'] == 'del'): ?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?act=del";?>" target="submitExec">
<p>
	<?php echo __('Are you sure to delete table');?>: <strong><?php echo $table;?></strong>
	<input type="hidden" name="table" value="<?php echo $table;?>" />
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
				<input id="block" name="block" type="text" size="50" value="<?php echo $block;?>" <?php echo $ro_table;?> />
				<br />
				<span><?php echo __('A unique name to construct the machine name for the block. It must only contain lowercase letters, numbers, underscore or dash.');?></span>
			</td>
		</tr>
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
