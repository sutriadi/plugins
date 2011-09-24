<?php
/*
 *      setup.php
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

define('INDEX_AUTH', '1');

if (!defined('SENAYAN_BASE_DIR')) {
    require '../../../../sysconfig.inc.php';
    require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if (!$can_read) {
	die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
}

require('../func.php');
checkip();
checkref();

require('./func.php');

if ($_POST)
{
	list($host, $dir, $file) = scinfo();
	$post = (object) $_POST;
	$post->title = isset($post->title) ? trim($post->title) : '';
	$valid = ( ! preg_match("/^([-a-z0-9_-])+$/", $post->table)) ? FALSE : TRUE;
	if ( ! $_GET AND ($valid === FALSE || empty($post->title)))
	{
		$alert = __('Error!\nTable has not been saved!');
		$script = ($valid === FALSE) ? "parent.$('input[name=table]').focus();" : "parent.$('input[name=title]').focus();";
	}
	else
	{
		$check = table_get($post->table, 'check');
		$alert = __('Table has been saved!');
		if ($check > 0 || ($_GET AND isset($_GET['table'])))
		{
			$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
			if (isset($_GET['cols']))
			{
				$alert = __('Columns has been saved!');
				switch ($_GET['cols'])
				{
					case "sort":
						unset($post->saveData);
						unset($post->title);
						$columns = (array) $post;
						$presorted = array();
						foreach ($columns as $key => $val)
						{
							if ($key !== 'table')
							{
								if ( ! is_numeric($val))
									$val = count($presorted);
								if ( ! in_array($val, $presorted))
									$presorted[$key] = $val;
								else
									$presorted[$key] = $val+1;
							}
						}
						if (count($presorted) > 0)
							$sorted = json_encode($presorted);
						unset($presorted);
						unset($columns);

						$sql = sprintf("UPDATE `plugins_dtables` "
							. " SET `sort` = '%s' "
							. " WHERE `table` = '%s'",
							$sorted,
							$post->table
						);
						break;
					case "add":
					default:
						$base_cols = (isset($post->base_cols)) ? json_encode($post->base_cols) : '';
						$php_code = (isset($post->end_cols_php)) ? true : false;
						$windowed = (isset($post->windowed)) ? true : false;
						$in_sql = '';
						if (isset($post->reindex))
						{
							$in_sql .= ", `sort` = '' ";
						}
						$sql = sprintf("UPDATE `plugins_dtables` "
							. " SET `first_col` = '%s', `base_cols` = '%s', `end_cols` = '%s', `php_code` = %d, `add_code` = '%s', `windowed` = '%s' %s"
							. " WHERE `table` = '%s';",
							$post->first_col,
							$base_cols,
							trim(addslashes($post->end_cols)),
							$php_code,
							trim(addslashes(php_rem($post->add_code))),
							$windowed,
							$in_sql,
							$post->table
						);
				}
			}
			else
			{
				if (isset($_GET['act']) AND $_GET['act'] == 'del')
				{
					$alert = __('Table has been deleted!');
					$sql = sprintf("DELETE FROM `plugins_dtables` WHERE `table` = '%s'", $post->table);
				}
				else
				{
					$sql = sprintf("UPDATE `plugins_dtables` SET `type` = '%s', `title` = '%s', `desc` = '%s' WHERE `table` = '%s'",
						$post->type,
						$post->title,
						$post->desc,
						$_GET['table']
					);
				}
			}
			$dbs->query($sql);
		}
		else
		{
			$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/add.php');";
			$sql = sprintf("INSERT INTO `plugins_dtables` (`table`, `type`, `title`, `desc`) VALUE ('%s', '%s', '%s', '%s')",
				$post->table,
				$post->type,
				$post->title,
				$post->desc
			);
			$dbs->query($sql);
		}
	}

	echo "<html><head><script type=\"text/javascript\">alert('$alert');$script</script></head><body></body></html>";
}

exit();
