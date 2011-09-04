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
 * @param $table string, name of table
 * @param $mode string, return mode
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
 * name: drupal_eval
 * @param $code string
 * @return $output
 */
function drupal_eval($code)
{
	global $conf, $dbs;
	ob_start();
	print eval('?>' . $code);
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

/*
 * 
 * name: php_rem
 * @param $str string
 * @return string
 */
function php_rem($str)
{
	$str = str_replace(array('<?php', '?>'), '', $str);
	$str = str_replace('<?', '', $str);
	return $str;
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
 * @return $options html
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

/*
 * 
 * name: base_cols_name
 * @param $type string
 * @return $base_cols_name array
 */
function base_cols_name($type = 'member')
{
	switch ($type)
	{
		case 'biblio':
			$base_cols_name = array(
				'biblio_id' => __('ID'),
				'gmd_id' => __('GMD'), // to join table
				'title' => __('Title'),
				'isbn_issn' => __('ISBN/ISSN'),
				'edition' => __('Edition'),
				'publisher_id' => __('Publisher'), // to join table
				'publish_year' => __('Publishing Year'),
				'collation' => __('Collation'),
				'series_title' => __('Series Title'),
				'call_number' => __('Call Number'),
				'language_id' => __('Language'),
				'publish_place_id' => __('Publishing Place'), // to join table
				'classification' => __('Classification'),
				'notes' => __('Abstract/Notes'),
				'image' => __('Image'),
				'spec_detail_info' => __('Specific Detail Info'),
				'opac_hide' => __('Hide in Opac'),
				'promoted' => __('Promote to Homepage'),
				'input_date' => __('Input Date'),
				'last_update' => __('Last Update'),
			);
			break;
		case 'member':
		default:
			$base_cols_name = array(
				'member_id' => __('ID'),
				'member_name' => __('Name'),
				'gender' => __('Gender'),
				'birth_date' => __('Birth Date'),
				'member_type_name' => __('Type'), // to join table
				'member_address' => __('Address'),
				'member_mail_address' => __('Mail Address'),
				'member_email' => __('E-Mail'),
				'postal_code' => __('Zip Code'),
				'inst_name' => __('Institution'),
				'member_image' => __('Photo'),
				'member_phone' => __('Phone'),
				'member_fax' => __('Fax'),
				'member_since_date' => __('Member Since'),
				'register_date' => __('Register Date'),
				'expire_date' => __('Expire Date'),
				'member_notes' => __('Notes'),
				'is_pending' => __('Pending Membership'),
				'last_login' => __('Last Login'),
				'last_login_ip' => __('Last Login From'),
				'input_date' => __('Input Date'),
				'last_update' => __('Last Update'),
			);
	}
	return $base_cols_name;
}

/*
 * 
 * name: set_table
 * @param $table string
 * @param $tag string
 * @param $options array
 * @return string html
 */
function set_table($table, $tag = 'head', $options = array())
{
	$order_cols = cols_order_get($table);
}

/*
 * 
 * name: table_render
 * @param $table string
 * @param $table boolean
 * @return
 */
function table_render($dtable, $table = true)
{

	global $base_cols_name, $fcols;
	$order_cols = cols_order_get($dtable);

	if (count($order_cols) > 0)
	{
		$precols = array();
		foreach ($order_cols as $key => $val)
		{
			if (array_key_exists($key, $base_cols_name))
			{
				if ($table === true)
				{
					$col_arrs = array(
						'label' => $base_cols_name[$key],
						'name' => $key,
					);
					if (isset($precols[$val]))
					{
						$precols[$val+1] = $col_arrs;
					}
					else
						$precols[$val] = $col_arrs;
				}
				else
				{
					if (isset($precols[$val]))
					{
						$precols[$val+1] = $key;
					}
					else
						$precols[$val] = $key;
				}
				unset($col_arrs);
			}
		}
		$num_cols = count($precols);
		
		if (isset($precols) AND is_array($precols))
		{
			// mengurutkan ulang kolom, menghitung jumlah kolom
			ksort($precols);
			
			if ($table === true)
			{
				$thead = '<thead><tr><th colspan="%d">' . __('Members Details') . '</th></tr><tr>';
				$tbody = '<tbody><tr><td colspan="%d" class="dataTables_empty">' . __('Loading data from server') . '</td></tr></tbody>';
				$tfoot = '<tfoot><tr>';
				$php_js = 'var phpDef = { %s };';
				$js_def = array();
				$js_def['aoColumnDefs'] = array();
				
				if (in_array($fcols[0], array('radio', 'checkbox')))
				{
					$num_cols++;
					$thead .= '<th></th>';
					$tfoot .= '<th></th>';
					$js_def['aoColumnDefs'][] = ' { "bSortable": false, "aTargets": [ 0 ] } ';
					$js_def['aoColumnDefs'][] = ' { "sClass": "center", "aTargets": [ 0 ] } ';
				}
			}

			$ordc = array();
			foreach ($precols as $ci => $cv)
			{
				$ordc[] = $cv;
				if ($table === true)
				{
					$label = $cv['label'];
					$thead .= '<th>' . $label . '</th>';
					$tfoot .= '<th style="padding:0px;"><input value="' . $label . '" type="text" class="search_init" /></th>';
				}
			}
			$precols = $ordc;
			unset($ordc);

			$a_content = array();
			if ( ! empty($fcols[2]))
			{
				$end_cols = explode(chr(10), $fcols[2]);
				$i_col = $num_cols;
				$num_cols += count($end_cols);

				foreach ($end_cols as $val)
				{
					$label = '';
					$content = $val;
					$del = explode(":", $val);

					if (count($del) > 1)
					{
						$label = $del[0];
						unset($del[0]);
						$content = implode(":", $del);
					}

					if ($table === true)
					{
						$thead .= sprintf('<th>%s</th>', $label);
						$tfoot .= '<th></th>';
						$js_def['aoColumnDefs'][] = sprintf(' { "bSortable": false, "aTargets": [ %d ] } ', $i_col);
					}

					$i_col ++;
					$a_content[] = trim($content);
				}
			}

			if ($table === true)
			{
				$thead .= '</tr></thead>';
				$tfoot .= '</tr></tfoot>';
				$thead = sprintf($thead, $num_cols);
				$tbody = sprintf($tbody, $num_cols);
				$tfoot = sprintf($tfoot, $num_cols);

				if (count($js_def > 0))
				{
					$v_arr = array();
					foreach ($js_def as $key => $val)
					{
						if (is_array($val) AND count($val) > 0)
						{
							$v_arr[$key] = sprintf(' "%s": [ %s ] ',
								$key,
								implode(', ', $val)
							);
						}
					}
					
					$v_str = implode(', ', $v_arr);
					$php_js = sprintf($php_js, $v_str);
				}
			}
		}
	}
	
	if ($table === true)
	{
		return array('php_js' => $php_js,
			'thead' => $thead,
			'tbody' => $tbody,
			'tfoot' => $tfoot
		);
	}
	else
	{
		return array(
			'precols' => $precols,
			'a_content' => $a_content,
		);
	}
}
