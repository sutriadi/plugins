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

// key to authenticate
define('INDEX_AUTH', '1');

if (!defined('SENAYAN_BASE_DIR')) {
    // main system configuration
    require '../../../sysconfig.inc.php';
    // start the session
    require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}

require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

// privileges checking
$can_read = utility::havePrivilege('plugins', 'r');
$can_write = utility::havePrivilege('plugins', 'w');

if (!$can_read) {
	die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
}

require('./func.php');

checkip();
checkref();

if ( ! isset($_SESSION['plugins_enabled']))
	$_SESSION['plugins_enabled'] = array();
if ( ! isset($_SESSION['plugins_available']))
	$_SESSION['plugins_available'] = array();

$enplugins = $_SESSION['plugins_enabled'];
$avplugins = $_SESSION['plugins_available'];

if ($_POST) {

	$to_enable = array();
	$to_disable = array();
	foreach ($_POST as $key => $value)
	{
		if (array_key_exists($key, $avplugins) AND ! array_key_exists($key, $enplugins))
			$to_enable[] = $key;
	}

	foreach ($enplugins as $key => $value)
	{
		if ( ! array_key_exists($key, $_POST))
			$to_disable[] = $key;
	}

	$json = json_encode($_POST);

	if (count($to_enable) != 0) enable_plugins($to_enable);
	if (count($to_disable) != 0) $body = disable_plugins($to_disable);

	echo "<html><head><script type=\"text/javascript\">window.parent.location.href = \"../../index.php?mod=plugins\";</script></head><body>$body</body></html>";
	exit();
}
