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
 * name: remote_addr
 * @param
 *   none
 * @return
 *   ip address of remote host
 */
function remote_addr()
{
	return $_SERVER['REMOTE_ADDR'];
}

/*
 * 
 * name: php_version
 * @param
 *   none
 * @return
 *   version of php
 */
function php_version()
{
	return substr(PHP_VERSION, 0, 5);
}

/*
 * 
 * name: ipconfirmation
 * @param
 *   none
 * @return
 *   $confirmation, 1 if valid ip address
 */
function ipconfirmation()
{
	$confirmation = false;
	$allowed_ip = json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true);
	
	foreach ($allowed_ip as $ip) {
		if ($ip == remote_addr()) {
			$confirmation = true;
		}
	}
	return $confirmation;
}

/*
 * 
 * name: scinfo
 * @param
 *   none
 * @return
 *   array of host, directory and filename
 */
function scinfo()
{
	$host = $_SERVER['HTTP_HOST'];
	$path = $_SERVER['SCRIPT_NAME'];
	$dir = explode('/', $path);
	$file = $dir[count($dir)-1];
	unset($dir[count($dir)-1]);
	$dir = implode('/', $dir);
	return array($host, $dir, $file);
}

/*
 * 
 * name: checkall
 * @param
 *   none
 * @return
 *   combination of checkip, checken, checkref
 */
function checkall()
{
	checkip();
	checken();
	checkref();
}

/*
 * 
 * name: checkip
 * @param
 *   none
 * @return
 *   redirect if ip address invalid
 */
function checkip()
{	
	$confirmation = ipconfirmation();
	if ( ! $confirmation)
	{
		exit();
	}
}

/*
 * 
 * name: checkref
 * @param $mode
 *   mode of checking referer. available mode:
 *   'module' (default): validating referer must from plugin page
 *   'plugin': validating referer is from one plugin page
 *   'admin': validating referer is from one administration page
 *   'host': validating referer have same host name
 *   'ip': validating referer have same ip address
 * @return
 *   true or display invalid message if failure
 */
function checkref($mode = 'module')
{
	if ( ! isset($_SERVER['HTTP_REFERER']))
		$ref = false;
	else
	{
		$ref_url = $_SERVER['HTTP_REFERER'];
		$ref_part = (object) parse_url($ref_url);
		$ref_host = isset($ref_part->host) ? $ref_part->host : '';
		$ref_ip = isset($ref_part->host) ? gethostbyname($ref_host) : '';
		$ref_path = isset($ref_part->path) ? $ref_part->path : '/';
		$ref_dir = explode('/', $ref_path);
		unset($ref_dir[count($ref_dir)-1]);
		$ref_dir = implode('/', $ref_dir);
		$ref_admin = $ref_host . $ref_dir;
		$ref_q = isset($ref_part->query) ? $ref_part->query : '';
		$ref_req = $ref_admin . '?' . $ref_q;

		list($dest_host, $dest_dir, $dest_file) = scinfo();
		$dest_path = $_SERVER['SCRIPT_NAME'];
		$dest_ip = gethostbyname($dest_host);
		$dest_admin = $dest_host . SENAYAN_WEB_ROOT_DIR . 'admin';
		$dest_plugin = $dest_admin . '/modules/plugins';
		$dest_q = 'mod=plugins';
		$dest_req = $dest_admin . '?' . $dest_q;
		switch ($mode)
		{
			case "host":
				if ($ref_host == $dest_host)
					$ref = true;
				break;
			case "ip":
				if ($ref_ip == $dest_ip)
					$ref = true;
				break;
			case "admin":
				if ($ref_admin == $dest_admin)
					$ref = true;
				break;
			case "plugin":
				$from_plugin = explode($dest_plugin, $ref_host . $ref_path);
				if (count($from_plugin) > 0 AND $from_plugin[0] == 0)
					$ref = true;
				else
					checkref();
			case "module":
			default:
				if ($ref_req == $dest_req)
					$ref = true;
		}
		if ($ref_path == $dest_path)
			$ref = true;
	}
	if ( ! isset($ref))
		die(sprintf('<div>%s!</div>', __('Invalid referer')));
	else
		return;
}

/*
 * 
 * name: checken
 * @param $name
 * @param $type
 * @return
 *   true or invalid message if failure
 */
function checken($name = '', $type = 'plugin')
{
	$toset = false;
	$search = true;
	if (defined('MODULES_WEB_ROOT_DIR'))
	{
		$name = trim($name);
		switch ($type)
		{
			case "table" :
			case "tables" :
			case "dtable" :
				$table = "plugins_dtables";
				$table_id = "table";
				break;
			case "menu" :
			case "menus" :
				$table = "plugins_menus";
				$table_id = "menu";
				break;
			case "plugins" :
			case "plugin" :
			default:
				if (empty($name))
					$plugin = checkname();
				else
					$plugin = $name;
				$name = $plugin;
				
				$table = "plugins";
				$table_id = "plugin_id";
				$en_plugins = $_SESSION['plugins_enabled'];
				if ($_SESSION['plugins_enabled'] AND array_key_exists($plugin, $en_plugins))
				{
					$toset = true;
					$search = false;
				}
				
		}
		
		if ($search === true AND ! empty($name))
		{
			global $dbs;
			
			$sql = sprintf("SELECT * FROM `%s` WHERE `%s` = '%s'",
				$table,
				$table_id,
				$name
			);
			$en = $dbs->query($sql);
			if ($en->num_rows > 0)
				$toset = true;
		}
	}
	if ( ! $toset)
		die('<div>' . __('Your requested page is not availabled/enabled!') . '</div>');
	else
		return;
}

/*
 * 
 * name: checkname
 * @param
 *   none
 * @return
 *   $plugin: name of plugin
 */
function checkname()
{
	if ( ! defined('MODPLUGINS_WEB_ROOT_DIR'))
		define('MODPLUGINS_WEB_ROOT_DIR', MODULES_WEB_ROOT_DIR . 'plugins/include/');
	$plugins = MODPLUGINS_WEB_ROOT_DIR;
	$self = $_SERVER['PHP_SELF'];
	$path = explode("/", str_replace($plugins, "", $self));
	$plugin = $path[0];
	return $plugin;
}

/*
 * 
 * name: checksess
 * @param
 *   none
 * @return
 *   none
 */
function checksess()
{
	global $conf;
	if ( ! isset($_SESSION['plugins_conf']))
		$_SESSION['plugins_conf'] = $conf;
}

/*
 * 
 * name: labeltype
 * @param $type
 *   type of plugin
 * @return
 *   string of $type
 */
function labeltype($type)
{
	switch ($type)
	{
		case 0: $t = __('None'); break;
		case 1: $t = __('Sibling'); break;
		case 2: $t = __('New Window'); break;
		default: $t = __('None');
	}
	return $t;
}

/*
 * 
 * name: enable_plugins
 * @param $key
 *   mixed array of plugins to enabled
 * @return
 *   none
 */
function enable_plugins($key)
{
	global $dbs;
	$enplugins = $_SESSION['plugins_enabled'];
	$avplugins = $_SESSION['plugins_available'];
	$values = array();
	if (count($key) > 0)
	{
		foreach ($key as $k)
		{
			$plugin_remove = $avplugins[$k]['plugin_remove'];
			$plugin_install = $avplugins[$k]['plugin_install'];
			if ($plugin_remove == null OR empty($plugin_remove) OR ! file_exists($plugin_remove))
				$avplugins[$k]['plugin_remove'] = '';
			
			if ($plugin_install == null OR empty($plugin_install) OR ! file_exists($plugin_install))
				$avplugins[$k]['plugin_install'] = '';
			else
				require($plugin_install);
			
			$cols = ! isset($cols) ? implode(",", array_keys($avplugins[$k])) : $cols;
			$vals = array();
			foreach ($avplugins[$k] as $col => $val)
			{
				$vals[] = sprintf("'%s'", $val);
			}
			$values[] = "(" . implode(", ", $vals) . ")";
			$enplugins = array_merge($enplugins, array($k => $avplugins[$k]));
		}
		$values = implode(", ", $values);
		$sql_ins = "INSERT INTO plugins ($cols) VALUES $values";
		$dbs->query($sql_ins);
		$_SESSION['plugins_enabled'] = $enplugins;
	}
}

/*
 * 
 * name: disable_plugins
 * @param $key
 *   mixed array of plugins to disabled
 * @return
 *   none
 */
function disable_plugins($key)
{
	global $dbs;
	$enplugins = $_SESSION['plugins_enabled'];
	$sql_get = "SELECT plugin_remove FROM plugins ";
	$sql_del = "DELETE FROM plugins ";
	$q = array();
	foreach ($key as $k)
	{
		$q[] = sprintf(" plugin_id = '%s'", $k);
		unset($enplugins[$k]);
	}
	$criteria = " WHERE " . implode(' OR ', $q);
	$sql_get .= $criteria . " AND plugin_remove != ''";
	$sql_del .= $criteria;
	$get = $dbs->query($sql_get);
	if ($get->num_rows != 0)
	{
		$arrays = array();
		while ($array = $get->fetch_assoc())
		{
			$plugin_remove = $array['plugin_remove'];
			if ($plugin_remove != null AND ! empty($plugin_remove) AND file_exists($plugin_remove))
				require($plugin_remove);
		}
	}
	$dbs->query($sql_del);
	$_SESSION['plugins_enabled'] = $enplugins;
}

/*
 * 
 * name: plugin_get
 * @param $plugin string
 * @return $info array
 */
function plugin_get($plugin)
{
	global $dbs;
	$sql = sprintf("SELECT * FROM `plugins` WHERE `plugin_id` = '%s'", $plugin);
	$info = $dbs->query($sql);
	if ($info->num_rows > 0)
	{
		return $info->fetch_assoc();
	}
	else
	{
		return array();
	}
}

/*
 * 
 * name: variable_set
 * @param $name
 *   name of variable
 * @param $value
 *   value of variable
 * @param $method
 *   method to store data. available method:
 *   - json
 *   - none (default)
 * @return
 *   none
 */
function variable_set($name, $value, $method='none')
{
	global $conf;
	global $dbs;
	
	switch ($method)
	{
		case "json":
			if (php_version() >= "5.3")
				$value = json_encode($value, JSON_FORCE_OBJECT);
			else
				$value = json_encode($value);
			break;
		case "none":
		default:
			$value = $value;
	}

	$query = sprintf("SELECT `name` FROM `plugins_vars` WHERE `name`='%s'", $name);
	$rows = $dbs->query($query);
	if ($rows->num_rows != 0)
		$query = sprintf("UPDATE `plugins_vars` SET `value`='%s' WHERE `name`='%s'", $value, $name);
	else
		$query = sprintf("INSERT INTO `plugins_vars` (`name`, `value`) VALUES ('%s', '%s')", $name, $value);
	$dbs->query($query);

	$conf[$name] = $value;
	$_SESSION['plugins_conf'] = $conf;
}

/*
 * 
 * name: variable_del
 * @param $name
 *   name of variable
 * @return
 *   none
 */
function variable_del($name)
{
	global $conf;
	global $dbs;
	
	$query = sprintf("DELETE FROM `plugins_vars` WHERE name = '%s'", $name);
	$dbs->query($query);

	unset($conf[$name]);
	$_SESSION['plugins_conf'] = $conf;
}

/*
 * 
 * name: variable_get
 * @param $name
 *   name of variable
 * @param $default
 *   default value of variable
 * @return
 *   none
 */
function variable_get($name, $default = NULL)
{
	global $conf;
	global $dbs;

	$value = $default;
	if ( ! isset($conf[$name]))
	{
		$query = sprintf("SELECT `value` FROM `plugins_vars` WHERE `name` = '%s'", $name);
		$rows = $dbs->query($query);
		if ($rows->num_rows > 0)
		{
			$row = $rows->fetch_assoc();
			$value = $row['value'];
			$conf[$name] = $value;
			$_SESSION['plugins_vars'] = $conf;
		}
	}
	else
	{
		$value = $conf[$name];
	}

	return $value;
}

/*
 * 
 * name: dtable_set
 * @param $mixed
 * @return none
 */
function dtable_set($mixed)
{
	global $dbs;
	if (count($mixed) > 0)
	{
		$cols = array();
		$vals = array();
		$sql = sprintf("SELECT * FROM `plugins_dtables` WHERE `table` = '%s'",
			$mixed['table']
		);
		$rows = $dbs->query($sql);
		$num_rows = $rows->num_rows;
		if ($num_rows == 0)
		{
			foreach($mixed as $col => $val)
			{
				$cols[] = "`$col`";
				$vals[] = "'$val'";
			}
			$column = implode(", ", $cols);
			$value = implode(", ", $vals);
			$sql = sprintf("INSERT INTO `plugins_dtables` (%s) VALUE (%s)",
				$column,
				$value
			);
			$dbs->query($sql);
		}
	}
}

/*
 * name: dtable_check
 * @param $

/*
 * name: dtable_del
 * @param $table
 * @return none
 */
function dtable_del($table)
{
	global $dbs;
	$query = sprintf("DELETE FROM `plugins_dtables` WHERE `table` = '%s'", $table);
	$dbs->query($query);
}

/*
 * 
 * name: css_get
 * @param $path boolean
 * @return $css_name string
 */
function css_get($path = true)
{
	$version = trim(variable_get('ui_css_version', ''));
	$fname = 'jquery-ui%s.css';
	if ( ! empty($version) AND $version != '')
	{
		$css_name = sprintf($fname, '-' . $version . '.custom.css');
	}
	$css_name = sprintf($fname, '.custom');
	if ($path === true)
	{
		$css_name = 'library/ui/css/' . variable_get('ui_theme') . '/' . $css_name;
	}
	return $css_name;
}

/*
 * 
 * name: unknown
 * @param
 * @return
 */
function ip_info($detail = true)
{
	$r = sprintf(': <strong>%s</strong>.', remote_addr());
	$info = __('You access this page from IP address') . $r;
	if ($detail === true)
	{
		$rs = sprintf(': <strong>%s</strong>', implode(', ', json_decode(variable_get('allowed_ip', '["127.0.0.1", "::1"]'), true)));
		$info .= __(' This page can accessed from following IP addresses') . $rs;
	}
	return $info;
}

/*
 * 
 * name: fs_render
 * @param $title
 * @param $params
 * @return string html
 */
function fs_render($title, $params = array())
{
	if (empty($title))
	{
		$title = __('Plugins');
	};
	extract($params);
	$logo = ( ! isset($logo) || trim($logo) == '') ? 'logo.png' : $logo;
	$bottom = ( ! isset($bottom) || trim($bottom) == '') ? '' : $bottom;
	$ip = ( ! isset($ip)) ? true : $ip;
	$ip_detail = ( ! isset($ip_detail)) ? true : $ip_detail;

	$fs = '<fieldset class="menuBox" style="font-weight: normal;">';
	$fs .= sprintf('<div style="padding: 3px; padding-left: 59px; background: url(%s) no-repeat -10px 5px;">', MODULES_WEB_ROOT_DIR . '/plugins/' . $logo);
	$fs .= sprintf('<strong>%s</strong>', $title);
	$fs .= '<hr />';
	if (trim($bottom) !== '')
		$fs .= $bottom;
	if ($ip === true)
		$fs .= ip_info($ip_detail);

	$fs .= '</div>';
	$fs .= '</fieldset>';
	return $fs;
}

function drupal_parse_info_file($filename)
{
	$info = array();
	$constants = get_defined_constants();

	if ( ! file_exists($filename))
	{
		return $info;
	}
	
	$data = file_get_contents($filename);
	if (preg_match_all('
	@^\s*								# Start at the beginning of a line, ignoring leading whitespace
	((?:
		[^=;\[\]]|						# Key names cannot contain equal signs, semi-colons or square brackets,
		\[[^\[\]]*\]					# unless they are balanced and not nested
	)+?)
	\s*=\s*								# Key/value pairs are separated by equal signs (ignoring white-space)
	(?:
		("(?:[^"]|(?<=\\\\)")*")|		# Double-quoted string, which may contain slash-escaped quotes/slashes
		(\'(?:[^\']|(?<=\\\\)\')*\')|	# Single-quoted string, which may contain slash-escaped quotes/slashes
		([^\r\n]*?)						# Non-quoted string
	)\s*$								# Stop at the next end of a line, ignoring trailing whitespace
	@msx', $data, $matches, PREG_SET_ORDER))
    {
		foreach ($matches as $match)
		{
			// Fetch the key and value string
			$i = 0;
			foreach (array('key', 'value1', 'value2', 'value3') as $var)
			{
				$$var = isset($match[++$i]) ? $match[$i] : '';
			}
			$value = stripslashes(substr($value1, 1, -1)) . stripslashes(substr($value2, 1, -1)) . $value3;

			// Parse array syntax
			$keys = preg_split('/\]?\[/', rtrim($key, ']'));
			$last = array_pop($keys);
			$parent = &$info;

			// Create nested arrays
			foreach ($keys as $key)
			{
				if ($key == '')
				{
					$key = count($parent);
				}
				if (!isset($parent[$key]) || !is_array($parent[$key]))
				{
					$parent[$key] = array();
				}
				$parent = &$parent[$key];
			}

			// Handle PHP constants.
			if (isset($constants[$value]))
			{
				$value = $constants[$value];
			}

			// Insert actual value
			if ($last == '')
			{
				$last = count($parent);
			}
			$parent[$last] = $value;
		}
	}
	return $info;
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
 * name: nodir
 * @param none
 * @return array
 */
function nodir()
{
	return array('.', '..');
}

/*
 * 
 * name: relist_avtheme
 * @param none
 * @return none
 */
function relist_avtheme()
{
	if (isset($_SESSION['opac_themes']))
	{
		unset($_SESSION['opac_themes']);
		list_avtheme();
	}
}

/*
 * 
 * name: list_avtheme
 * @param none
 * @return array
 */
function list_avtheme()
{
	global $sysconf;
	
	$opac_themes = array();
	$nodir = nodir();
	if ( ! isset($_SESSION['opac_themes']))
	{
		$tpl_dir = SENAYAN_BASE_DIR . $sysconf['template']['dir'] . '/fatin/sub/';
		$dirs = scandir($tpl_dir);
		sort($dirs);
		foreach ($dirs as $file)
		{
			$info_file = $tpl_dir . $file . '/tpl.info';
			if ( ! in_array($file, $nodir) AND is_dir($tpl_dir . $file) AND file_exists($info_file))
			{
				$info = drupal_parse_info_file($info_file);
				$opac_themes[$file] = isset($info['name']) ? $info['name'] : $file;
			}
		}
		$_SESSION['opac_themes'] = $opac_themes;
	}
	return $_SESSION['opac_themes'];
}
