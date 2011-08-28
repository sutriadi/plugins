<?php
/*
 *      func.php
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

/*
 * 
 * name: table_get
 * @param $table
 * @param $mode
 * @return array or number
 */
function table_get($table, $mode = 'array')
{
	global $dbs;
	$sql = sprintf("SELECT `table`, `type`, `title`, `desc` FROM `plugins_dtables` WHERE `table` = '%s'", $table);
	$table = '';
	$type = '';
	$title = '';
	$desc = '';
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($mode == 'array')
	{
		if ($num_rows > 0)
		{
			$row = (object) $rows->fetch_assoc();
			$table = $row->table;
			$type = $row->type;
			$title = $row->title;
			$desc = $row->desc;
		}
		return array($table, $type, $title, $desc);
	}
	else if ($mode == 'check')
		return $num_rows;
}

/*
 * 
 * name: cols_get
 * @param $table
 * @param $mode
 * @return array or number
 */
function cols_get($table, $mode = 'array')
{
	global $dbs;
	$sql = sprintf("SELECT `first_col`, `base_cols`, `end_cols`, `php_code`, `add_code`, `windowed` FROM `plugins_dtables` WHERE `table` = '%s'", $table);
	$first_col = '';
	$base_cols = '';
	$end_cols = '';
	$php_code = false;
	$add_func = '';
	$windowed = false;
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($mode == 'array')
	{
		if ($num_rows > 0)
		{
			$row = (object) $rows->fetch_assoc();
			$first_col = $row->first_col;
			$base_cols = json_decode(stripslashes($row->base_cols), true);
			$end_cols = stripslashes($row->end_cols);
			$php_code = $row->php_code;
			$add_code = stripslashes($row->add_code);
			$windowed = $row->windowed;
		}
		return array($first_col, $base_cols, $end_cols, $php_code, $add_code, $windowed);
	}
	else if ($mode == 'check')
		return $num_rows;
}

/*
 * 
 * name: cols_order_get
 * @param $table
 * @param $mode
 * @return string or number
 */
function cols_order_get($table, $mode = 'array')
{
	global $dbs;
	$sql = sprintf("SELECT `base_cols`, `sort` FROM `plugins_dtables` WHERE `table` = '%s'", $table);
	$order_cols = array();
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($mode == 'array')
	{
		if ($num_rows > 0)
		{
			$row = (object) $rows->fetch_assoc();
			$sort = $row->sort;
			$base_cols = $row->base_cols;
			if ($sort !== NULL AND ! empty($sort))
			{
				$order_cols = json_decode($sort);
			}
			else if ($base_cols !== NULL AND ! empty($base_cols))
			{
				$order_arrs = json_decode($base_cols);
				foreach ($order_arrs as $key => $val)
				{
					$order_cols[$val] = $key;
				}
			}
		}
		return $order_cols;
	}
	else if ($mode == 'check')
		return $num_rows;
}

/*
 * 
 * name: set_options
 * @param $num
 * @return html $options
 */
function set_options($num = 0)
{
	$low = -20;
	$high = 20;
	$options = '';
	for($n = $low; $n <= $high; $n++)
	{
		$selected = ($num === $n) ? "selected" : "";
		$options .= '<option value="' . $n . '" ' . $selected . '>' . $n . '</option>';
	}
	return $options;
}
