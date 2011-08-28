<?php
/*
 *      session.php
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

if ( ! isset($_SESSION['plugins_enabled']) OR count($_SESSION['plugins_enabled']) == 0)
	$_SESSION['plugins_enabled'] = array();
if ( ! isset($_SESSION['plugins_available']) OR count($_SESSION['plugins_available']) == 0)
	$_SESSION['plugins_available'] = array();

$enplugins = $_SESSION['plugins_enabled'];
$avplugins = $_SESSION['plugins_available'];

$sql = 'SELECT * FROM plugins';
$array = array();
if (count($enplugins) != 0)
{
	$sql .= ' WHERE ';
	$q = array();
	foreach ($enplugins as $key => $value)
		$q[] = sprintf(" plugins_id != '%s'", $key);

	$criteria = implode(' AND ', $q);
	$sql .= $criteria;
}
$query = $dbs->query($sql);
if (isset($query->num_rows) && $query->num_rows > 0)
{
	$arrays = array();
	while($array = $query->fetch_assoc())
		$arrays[$array['plugin_id']] = $array;
	$enplugins = array_merge($enplugins, $arrays);
}

$thisdir = "./include";
$plugins = scandir($thisdir);
sort($plugins);
$options = '';
$plugdirs = array();
foreach ($plugins as $plugin)
{
	$plugdir = $thisdir . '/' . $plugin;
	$pluginfo = $plugdir . '/info.php';
	if ($plugin != "." AND $plugin != ".." AND ! is_dir($plugdir))
		continue;
	$plugdirs[] = $plugin;
	if ( ! array_key_exists($plugin, $avplugins) AND file_exists($pluginfo))
	{
		$info = '';
		include($pluginfo);
		$thisplugin = array(
			'plugin_id' => $plugin,
			'plugin_name' => ! isset($info['name']) ? 'Untitled' : $info['name'],
			'plugin_author' => ! isset($info['author']) ? 'Unknown' : $info['author'],
			'plugin_version' => ! isset($info['version']) ? 'Unversion' : $info['version'],
			'plugin_description' => ! isset($info['description']) ? 'Undescription' : $info['description'],
			'plugin_type' => ! isset($info['type']) ? 0 : $info['type'],
			'plugin_install' => ! isset($info['install']) ? null : $plugdir . '/' . $info['install'] . '.php' ,
			'plugin_remove' => ! isset($info['remove']) ? null : $plugdir . '/' . $info['remove'] . '.php' ,
			'plugin_deps' => ! isset($info['deps']) ? null : $info['deps'],
		);
		$avplugins = array_merge($avplugins, array($plugin => $thisplugin));
		unset($info);
	}
}

foreach ($avplugins as $key => $value)
{
	if ( ! in_array($key, $plugdirs))
		unset($avplugins[$key]);
}

$_SESSION['plugins_enabled'] = $enplugins;
$_SESSION['plugins_available'] = $avplugins;
