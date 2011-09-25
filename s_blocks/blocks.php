<?php
/*
 *      blocks.php
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

function block_menu_list()
{
	global $dbs;
	
	$sql = "SELECT * FROM `plugins_menus`";
	$rows = $dbs->query($sql);
	$blocks = array();
	if ($rows->num_rows > 0)
	{
		while ($row = $rows->fetch_assoc())
		{
			$blocks[$row['menu']] = array('desc' => __($row['title']));
		}
	}
	return $blocks;
}

function block_menu($op = 'list', $delta = 0)
{
	switch ($op)
	{
		case "list":
			$blocks = block_menu_list();
	}
	return $blocks;
}

function block_core($op = 'list', $delta = 0)
{
	switch ($op)
	{
		case "list":
			$blocks = array();
			$blocks['advanced_search'] = array('desc' => __('Advanced Search'));
			$blocks['award'] = array('desc' => __('Award'));
			$blocks['language'] = array('desc' => __('Language'));
			$blocks['license'] = array('desc' => __('License'));
			$blocks['librarian_login'] = array('desc' => __('Librarian Login'));
			$blocks['member_login'] = array('desc' => __('Member Login'));
			$blocks['search'] = array('desc' => __('Search'));
			$blocks['welcome'] = array('desc' => __('Welcome'));
			break;
	}
	return $blocks;
}
