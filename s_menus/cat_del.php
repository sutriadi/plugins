<?php
/*
 *      cat_del.php
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

if ( ! isset($_GET) || ! isset($_GET['menu']))
{
	echo __('Something wrong to delete menu');
	exit();
}

$menu = $_GET['menu'];

?>

<form name="mainForm" id="mainForm" method="POST" action="<?php echo $dir . "/set_cat.php?act=del";?>" target="submitExec">
<p>
	<?php echo __('Are you sure to delete menu');?>: <strong><?php echo $menu;?></strong>
	<input type="hidden" name="menu" value="<?php echo $menu;?>" />
	<input type="submit" value="<?php echo __('Delete');?>" />
	<input type="button" onclick="parent.$('#mainContent').simbioAJAX('<?php echo $dir . '/';?>');" value="<?php echo __('Cancel');?>" />
</p>
</form>
<iframe name="submitExec" class="noBlock" style="visibility: visible; width: 100%;"></iframe>
