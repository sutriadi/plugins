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
 * name: menu_get
 * @param $menu
 * @param $mode
 * @return
 */
function menu_get($menu, $mode = 'array')
{
	global $dbs;
	$sql = sprintf("SELECT * FROM `plugins_menus` WHERE `menu` = '%s'", $menu);
	$menu = '';
	$title = '';
	$desc = '';
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($num_rows > 0)
	{
		$row = (object) $rows->fetch_assoc();
		$menu = $row->menu;
		$title = $row->title;
		$desc = $row->desc;
	}
	if ($mode == 'array')
		return array($menu, $title, $desc);
	else if ($mode == 'check')
		return $num_rows;
}
