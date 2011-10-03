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
checkref();

if ($_POST)
{
	list($host, $dir, $file) = scinfo();
	$post = (object) $_POST;
	if (isset($post->ips)) variable_set('allowed_ip', explode(" ", $post->ips), "json");
	if (isset($post->opac_theme)) variable_set('opac_theme', $post->opac_theme);
	if (isset($post->opac_frontpage)) variable_set('opac_fronpage', $post->opac_frontpage);
	if (isset($post->ui_theme)) variable_set('ui_theme', $post->ui_theme);
	if (isset($post->ui_theme_opac)) variable_set('ui_theme_opac', $post->ui_theme_opac);
	if (isset($post->ui_css_version)) variable_set('ui_css_version', $post->ui_css_version);
	if (isset($post->ui_css_version)) variable_set('allowed_tags', $post->allowed_tags);
	if (isset($post->ui_css_version)) variable_set('allowed_tags', $post->allowed_tags);
	if (isset($post->main_links)) variable_set('main_links', $post->main_links);
	if (isset($post->main_links_items)) variable_set('main_links_items', $post->main_links_items);
	
	echo "<html><head><script type=\"text/javascript\">alert('" . __('Configuration has been saved!') . "');parent.$('#mainContent').simbioAJAX('". $dir . "/');</script></head><body></body></html>";
	exit();
}
