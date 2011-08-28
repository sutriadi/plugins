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

$sql = "SELECT * FROM `plugins_dtables`";
$dtables = $dbs->query($sql);
$list = '<tbody>';
$cols_dt_label = __('Columns');
$sort_dt_label = __('Sort Columns');
$edit_dt_label = __('Edit');
$del_dt_label = __('Delete');
if ($dtables AND $dtables->num_rows > 0)
{
	while ($dtable = $dtables->fetch_assoc())
	{
		$cols_dt_click = "$('#mainContent').simbioAJAX('$dir/add.php?cols=add&table={$dtable['table']}');";
		$sort_dt_click = "$('#mainContent').simbioAJAX('$dir/add.php?cols=sort&table={$dtable['table']}&action=sort');";
		$edit_dt_click = "$('#mainContent').simbioAJAX('$dir/add.php?table={$dtable['table']}');";
		$del_dt_click = "$('#mainContent').simbioAJAX('$dir/add.php?action=del&table={$dtable['table']}');";
		$list .= "<tr>"
			. "<td>{$dtable['table']}</td>"
			. "<td>{$dtable['title']}</td>"
			. "<td>{$dtable['type']}</td>"
			. "<td>{$dtable['desc']}</td>"
			. "<td>"
				. "<input type=\"button\" value=\"$edit_dt_label\" onclick=\"$edit_dt_click\" /> "
				. "<input type=\"button\" value=\"$cols_dt_label\" onclick=\"$cols_dt_click\" /> "
				. "<input type=\"button\" value=\"$sort_dt_label\" onclick=\"$sort_dt_click\" /> "
				. "<input type=\"button\" value=\"$del_dt_label\" onclick=\"$del_dt_click\" /> "
			. "</td>"
		. "</tr>";
		unset($edit_dt_click);
		unset($del_dt_click);
	}
}
else
{
	$list .= "<tr><td colspan=\"5\" align=\"center\">" . __('DataTables empty!') . "</td></tr>";
}

$list .="</tbody>";

?>

	<table width="100%" cellspacing="0" cellpadding="5" style="text-align: left;">
		<tr style="background: gray; color: white;">
			<th><?php echo __('Table');?></th>
			<th><?php echo __('Title');?></th>
			<th><?php echo __('Type');?></th>
			<th><?php echo __('Description');?></th>
			<th><?php echo __('Action');?></th>
		</tr>
		<?php echo $list;?>
	</table>
