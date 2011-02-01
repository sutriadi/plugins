<?php
/*
 *      func.php
 *      
 *      Copyright 2011 Indra Sutriadi Pipii <indra.sutriadi@gmail.com>
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

function ipconfirmation()
{
	global $conf;
	extract($conf);
	foreach ($allowed_ip as $ip) {
		if ($ip == $remote_addr) {
			$confirmation = 1;
		}
	}
	return $confirmation;
}

function checkall()
{
	checkip();
	checken();
	checkref();
}

function checkip()
{	
	$confirmation = ipconfirmation();
	if ( ! $confirmation)
	{
		header ("location:../index.php");
	}
}

function checkref()
{
	if ( ! $_SERVER['HTTP_REFERER'])
		$ref = false;
	else
	{
		$referer = $_SERVER['HTTP_REFERER'];
		preg_match('@^(?:http://)?([^/]+)@i', $referer, $matches);
		$refhost = $matches[1];
		$host = $_SERVER['HTTP_HOST'];
		$refhost_admin = $refhost . SENAYAN_WEB_ROOT_DIR . 'admin';
		$host_admin = $host . SENAYAN_WEB_ROOT_DIR . 'admin';
		if ($refhost != $_SERVER['HTTP_HOST'])
			$ref = false;
		else if ( ! preg_match("@^$refhost_admin@i", $host_admin))
			$ref = false;
		else
			$ref = true;
	}
	if ( ! $ref)
		die('<div>This plugin not request from plugins module page!</div>');
	else
		return;
}

function checken()
{
	$toset = false;
	if (defined('MODULES_WEB_ROOT_DIR'))
	{
		$plugin = checkname();
		$en_plugins = $_SESSION['plugins_enabled'];
		if ($_SESSION['plugins_enabled'] AND array_key_exists($plugin, $en_plugins))
			$toset = true;
		else
		{
			global $dbs;
			$en = $dbs->query("SELECT * FROM plugins WHERE plugin_id = '$plugin'");
			if ($en->num_rows > 0)
				$toset = true;
		}
	}
	if ( ! $toset)
		die('<div>This plugin not enabled!</div>');
	else
		return;
}

function checkname()
{
	if ( ! defined('MODPLUGINS_WEB_ROOT_DIR'))
		define('MODPLUGINS_WEB_ROOT_DIR', MODULES_WEB_ROOT_DIR . 'plugins/');
	$plugins = MODPLUGINS_WEB_ROOT_DIR;
	$self = $_SERVER['PHP_SELF'];
	$path = explode("/", str_replace($plugins, "", $self));
	$plugin = $path[0];
	return $plugin;
}

function labeltype($type)
{
	switch ($type)
	{
		case 0: $t = 'None'; break;
		case 1: $t = 'Sibling'; break;
		case 2: $t = 'New Window'; break;
		default: $t = 'None';
	}
	return $t;
}

function enable_plugins($key)
{
	global $dbs;
	$enplugins = $_SESSION['plugins_enabled'];
	$avplugins = $_SESSION['plugins_available'];
	$values = array();
	foreach ($key as $k)
	{
		$plugin_remove = $avplugins[$k]['plugin_remove'];
		if ($plugin_remove != null AND ! empty($plugin_remove) AND file_exists($k.'/'.$plugin_remove))
			$avplugins[$k]['plugin_remove'] = readfile($k.'/'.$plugin_remove);
		else
			$avplugins[$k]['plugin_remove'] = '';
		
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
			$arrays[] = $array['plugin_remove'];

		$sql_del .= "; " . implode('', $arrays);
	}
	$dbs->query($sql_del);
	$_SESSION['plugins_enabled'] = $enplugins;
}

function variable_set($name, $value)
{
	global $conf;
	global $dbs;

	$query = sprintf("INSERT INTO `plugins_vars` (`name`, `value`) VALUES ('%s', '%s')", $name, $value);
	$dbs->query($query);

	$conf[$name] = $value;
}

function variable_del($name)
{
	global $conf;
	global $dbs;
	
	$query = sprintf("DELETE FROM `plugins_vars` WHERE name = '%s'", $name, $value);

	unset($conf[$name]);
}

function variable_get($name, $default = NULL)
{
  global $conf;

  return isset($conf[$name]) ? $conf[$name] : $default;
}
