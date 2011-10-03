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

$ro_table = '';
$params = '';

list($table, $type, $title, $desc) = (isset($_GET) AND isset($_GET['table'])) ? table_get($_GET['table']) : array('', '', '', '');
if ( ! empty($table))
{
	$ro_table = 'readonly';
	$params = '?table=' . $table;
}

$types = array(
	'member' => __('Member'),
	'biblio' => __('Bibliography'),
	'content' => __('Content'),
);

$opt_type = '';
foreach ($types as $val => $text)
{
	$selected = ($val == $type) ? "selected" : "";
	$opt_type .= "<option value=\"$val\" $selected>$text</option>";
}

$cols = '';
if (isset($_GET) AND isset($_GET['cols']))
{
	$base_cols_name = array();
	$base_cols_label = __('Base Columns');
	$base_cols_name = base_cols_name($type);
	if ($type == 'member')
		$base_cols_label = __('Base Columns of Member');
	else if ($type == 'biblio')
		$base_cols_label = __('Base Columns of Bibliography');
	else if ($type == 'content')
		$base_cols_label = __('Base Columns of Content');

	switch ($_GET['cols'])
	{
		case "sort":
			$cols = 'sort';
			$params .= '&cols=sort';
			$order_cols = cols_order_get($_GET['table']);
			if (count($order_cols) > 0)
			{
				foreach ($order_cols as $key => $val)
				{
					if (array_key_exists($key, $base_cols_name))
					{
						$col_arrs = array(
							'label' => $base_cols_name[$key],
							'name' => $key,
						);
						if (isset($columns[$val]))
						{
							$columns[$val+1] = $col_arrs;
						}
						else
							$columns[$val] = $col_arrs;
						unset($col_arrs);
					}
					
					if (isset($columns) AND is_array($columns))
					{
						$form = '';
						ksort($columns);
						foreach ($columns as $key => $arr)
						{
							$form .= ''
								. '<tr valign="top">'
									. '<td class="alterCell" style="font-weight: bold;"> '
										. '<label for="' . $arr['name'] . '" style="cursor: pointer;">' . $arr['label'] . '</label></td>'
									. '<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>'
									. '<td class="alterCell2">'
										. '<select id="' . $arr['name'] . '" name="' . $arr['name'] . '">'
										. set_options($key)
										. '</select>'
										. '<br />'
										. '<span>' . sprintf(__('Column number of <strong>%s</strong>.'), $arr['label']) . '</span>'
									. '</td>'
								. '</tr>'
							. '';
						}
					}
				}
			}
			break;
		case "add":
		default:
			$cols = 'add';
			$params .= '&cols=add';
			list($first_col, $base_cols, $end_cols, $php_code, $add_code, $windowed) = (isset($_GET['table'])) ? cols_get($_GET['table']) : array('', '', '', '', '', 1);


			// set first columns value
			$first_cols = array(
				'none' => __('None'),
				'checkbox' => __('Checkbox'),
				'radio' => __('Radiobutton'),
			);
			$opt_first_col = '';
			foreach ($first_cols as $val => $text)
			{
				$selected = ($val == $first_col) ? "selected" : "";
				$opt_first_col .= "<option value=\"$val\" $selected>$text</option>";
			}
			
			// set base columns value
			$opt_base_cols = '';
			$base_cols = (array) $base_cols;
			foreach ($base_cols_name as $key => $val)
			{
				$selected = (in_array($key, $base_cols)) ? "selected" : "";
				$opt_base_cols .= "<option value=\"$key\" $selected>$val</option>";
			}
			
			// set checkbox php code
			$ch_cols_php = ((bool) ($php_code) === true) ? "checked" : "";

			// set checkbox new window
			$windowed = ((bool) ($windowed) === true) ? "checked" : "";

	}
}

?>

<?php if (isset($_GET['action']) AND $_GET['action'] == 'del'): ?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/setup.php?act=del";?>" target="submitExec">
<p>
	<?php echo __('Are you sure to delete table');?>: <strong><?php echo isset($title) ? $title : $table;?></strong>?
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
			<td class="alterCell" style="font-weight: bold;"><label for="table" style="cursor: pointer;"><?php echo __('Table');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="table" name="table" type="text" size="50" value="<?php echo $table;?>" <?php echo $ro_table;?> />
				<br />
				<span><?php echo __('A unique name to construct the machine name for the table. It must only contain lowercase letters, numbers, underscore or dash.');?></span>
			</td>
		</tr>
<?php if (empty($cols)):?>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="type" style="cursor: pointer;"><?php echo __('Type');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="type" name="type"><?php echo $opt_type;?></select>
				<br />
				<span><?php echo __('Type of dataTables.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="title" style="cursor: pointer;"><?php echo __('Title');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="title" name="title" type="text" size="50" value="<?php echo $title;?>" />
				<br />
				<span><?php echo __('Display name of table.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="label" style="cursor: pointer;"><?php echo __('Description');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input id="desc" name="desc" type="text" size="50" value="<?php echo $desc;?>" />
				<br />
				<span><?php echo __('Description of your table.');?></span>
			</td>
		</tr>
<?php elseif ($cols == 'add'): ?>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="first_col" style="cursor: pointer;"><?php echo __('First Column');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="first_col" name="first_col"><?php echo $opt_first_col;?></select>
				<br />
				<span><?php echo __('Additional column at beginning of the table.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="base_cols" style="cursor: pointer;"><?php echo $base_cols_label;?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<select id="base_cols" name="base_cols[]" multiple size="<?php echo count($base_cols_name);?>"><?php echo $opt_base_cols;?></select>
				<br />
				<span><?php echo __('Columns to display. Hold Ctrl key for multiple check.');?></span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="end_cols" style="cursor: pointer;"><?php echo __('End Columns');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<textarea id="end_cols" name="end_cols" rows="5" style="width: 100%;"><?php echo $end_cols;?></textarea>
				<br />
				<span>
					<?php echo __('Additional columns at end of the table. Separate each other with newline.');?>
					<?php echo __('You can use format <strong>label</strong>: <strong>content</strong>.');?>
					<?php echo __('PHP code must be inside <strong>&lt;?php ?&gt;</strong> tag.');?>
				</span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="end_cols_php" style="cursor: pointer;"><?php echo __('PHP Code');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="checkbox" id="end_cols_php" name="end_cols_php" <?php echo $ch_cols_php;?> />
					<label for="end_cols_php" style="cursor: pointer;"><?php echo __('Yes');?></label>
				<br />
				<span>
					<?php echo __('Check it if your additional columns contain PHP code.');?>
				</span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="add_code" style="cursor: pointer;"><?php echo __('Additional PHP Code');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<textarea id="add_code" name="add_code" rows="5" style="width: 100%;"><?php echo $add_code;?></textarea>
				<br />
				<span>
					<?php echo __('Additional variables, functions or etc to handle your additional columns.');?>
					<?php echo __('<strong>&lt;?php ?&gt;</strong> tags do not need to be included.');?>
				</span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="windowed" style="cursor: pointer;"><?php echo __('New Window');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="checkbox" id="windowed" name="windowed" <?php echo $windowed;?> />
					<label for="windowed" style="cursor: pointer;"><?php echo __('Yes');?></label>
				<br />
				<span>
					<?php echo __('Check it if this tables displayed on new window type plugin.');?>
					<?php echo __('Not implemented yet!');?>
				</span>
			</td>
		</tr>
		<tr valign="top">
			<td class="alterCell" style="font-weight: bold;"><label for="reindex" style="cursor: pointer;"><?php echo __('Reindex');?></label></td>
			<td class="alterCell" style="font-weight: bold; width: 1%;">:</td>
			<td class="alterCell2">
				<input type="checkbox" id="reindex" name="reindex" />
					<label for="reindex" style="cursor: pointer;"><?php echo __('Yes');?></label>
				<br />
				<span><?php echo __('Check it if you want to reindex columns.');?></span>
			</td>
		</tr>
<?php elseif ($cols == 'sort'): ?>
		<?php echo isset($form) ? $form : '';?>
<?php endif;?>
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
<?php endif;?>
<iframe name="submitExec" class="noBlock" style="visibility: hidden; width: 100%; height: 0;"></iframe>
