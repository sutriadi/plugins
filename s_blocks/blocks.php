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

function block_menu_build($delta = 'primary-links')
{
	if ( ! function_exists('menu_items_get'))
		@require(MODPLUGINS_BASE_DIR . '/s_menus/func.php');

	list($menu, $title, $desc) = menu_get($delta);
	
	$block = array(
		'title' => $title,
		'content' => menu_build_links(menu_items_get($menu))
	);
	return $block;
}

function block_block($op = 'view', $delta = 0)
{
	$blocks = array();
	list($block, $desc, $title, $code, $filter) = block_custom_get($delta);
	switch ($filter)
	{
		case "text":
			$content = nl2br(htmlentities($code));
			break;
		case "simple":
			$content = nl2br(strip_tags($code, variable_get('allowed_tags')));
			break;
		case "php":
			ob_start();
			print eval('?>' . nl2br($code));
			$content = ob_get_contents();
			ob_end_clean();
			break;
		case "full":
		default:
			$content = nl2br($code);
	}
	$blocks[$delta] = array(
		'title' => $title,
		'content' => $content
	);
	return $blocks;
}

function block_menu($op = 'list', $delta = 0)
{
	switch ($op)
	{
		case "list":
			$blocks = block_menu_list();
			break;
		case "view":
			$blocks = array();
			$blocks[$delta] = block_menu_build($delta);
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
		case "view":
			$blocks = array();
			$blocks[$delta] = call_user_func('block_core_' . $delta);
	}
	return $blocks;
}

function block_core_advanced_search()
{		
	global $gmd_list, $colltype_list, $location_list;
	
	$content = '<form name="advSearchForm" id="advSearchForm" action="%s" method="get">'
		. '<p><label for="txt_title">%s</label>'
			. '<input type="text" class="text keyword" id="txt_title" name="title" value="%s" /></p>'
		. '<p><label for="author">%s</label>'
			. '<input type="text" class="text keyword" id="author" name="author" value="%s" /></p>'
		. '<p><label for="subject">%s</label>'
			. '<input type="text" class="text keyword" id="subject" name="subject" value="%s" /></p>'
		. '<p><label for="isbn">%s</label>'
			. '<input type="text" class="text keyword" id="isbn" name="isbn" value="%s" /></p>'
		. '<p><label for="gmd">%s</label>'
			. '<select id="gmd" name="gmd" />%s</select></p>'
		. '<p><label for="colltype">%s</label>'
			. '<select id="colltype" name="colltype" />%s</select></p>'
		. '<p><label for="location">%s</label>'
			. '<select id="location" name="location" />%s</select></p>'
		. '<p>'
			. '<input type="hidden" name="advanced" value="Search" />'
			. '<input type="submit" class="button" name="search" value="%s" />'
		. '</p>'
	. '</form>';
	$content = sprintf($content,
		SENAYAN_WEB_ROOT_DIR,
		__('Title'),
		isset($_GET['title']) ? $_GET['title'] : '',
		__('Author(s)'),
		isset($_GET['author']) ? $_GET['author'] : '',
		__('Subject(s)'),
		isset($_GET['subject']) ? $_GET['subject'] : '',
		__('ISBN/ISSN'),
		isset($_GET['isbn']) ? $_GET['isbn'] : '',
		__('GMD'),
		$gmd_list,
		__('Collection Type'),
		$colltype_list,
		__('Location'),
		$location_list,
		__('Search!')
	);

	$block = array(
		'title' => __('Advanced Search'),
		'content' => $content
	);
	return $block;
}

function block_core_award()
{
	$block = array(
		'title' => __('Award'),
		'content' => sprintf('%s<br />%s',
			__('The Winner in the Category of OSS'),
			'<img src="template/fatin/img/logo-inaicta.png" alt="Indonesia ICT Award 2009" border="0" />'
		)
	);
	return $block;
}

function block_core_language()
{
	global $language_select;
	
	$block = array(
		'title' => __('Language'),
		'content' => '<form name="langSelect" action="index.php" method="get">'
			. '<select name="select_lang" style="width: 99%;" onchange="document.langSelect.submit();">'
			. $language_select
			. '</select>'
			. '</form>'
	);
	return $block;
}

function block_core_license()
{
	$block = array(
		'title' => __('License'),
		'content' => __('This software and this template are released Under <a href="http://www.gnu.org/copyleft/gpl.html" title="GNU GPL License" target="_blank">GNU GPL License</a> Version 3.')
	);
	return $block;
}

function block_core_librarian_login()
{
	$title = __('Librarian Login');
	if (isset($_COOKIE['admin_logged_in']) && $_COOKIE['admin_logged_in'] == 1)
	{
		$content = sprintf('<a href="admin/logout.php">%s</a>', __('Logout'));
	}
	else
	{
		$content = '<form action="%s" method="post">'
			. '<p><span id="info">%s</span></p>'
			. '<p>'
				. '<label for="userName">%s</label>'
				. '<input type="text" id="userName" name="userName" class="text login" class="text" />'
			. '</p>'
			. '<p>'
				. '<label for="passWord">%s</label>'
				. '<input type="password" id="passWord" name="passWord" class="text login" class="text" />'
			. '</p>'
			. '<p><input type="submit" name="logMeIn" value="%s" id="loginButton" class="text button" /></p>'
		. '</form>';
		$content = sprintf($content,
			SENAYAN_WEB_ROOT_DIR . '/?p=login',
			__('Sign in to your account'),
			__('Username'),
			__('Password'),
			__('Logon')
		);
	}

	$block = array(
		'title' => __('Librarian Login'),
		'content' => $content
	);
	return $block;
}

function block_core_member_login()
{
	if (utility::isMemberLogin())
	{
		$content = sprintf('<ul class="links"><li><a href="%s">%s</a></li></ul>',
			SENAYAN_WEB_ROOT_DIR . '?p=member&logout=1',
			__('Logout')
		);
	}
	else
	{
		$content = '<form action="%s" method="post">'
			. '<p><span id="info">%s</span></p>'
			. '<p>'
				. '<label for="block-memberID">%s</label>'
				. '<input type="text" id="block-memberID" name="memberID" class="text login" class="text" />'
			. '</p>'
			. '<p>'
				. '<label for="block-memberPassWord">%s</label>'
				. '<input type="password" id="block-memberPassWord" name="memberPassWord" class="text login" class="text" />'
			. '</p>'
			. '<p><input type="submit" name="logMeIn" value="%s" id="loginButton" class="text button" /></p>'
		. '</form>';
		$content = sprintf($content,
			SENAYAN_WEB_ROOT_DIR . '/?p=member',
			__('Sign in to your account'),
			__('Member ID'),
			__('Password'),
			__('Logon')
		);
	}

	$block = array(
		'title' => __('Member Login'),
		'content' => $content
	);
	return $block;
}

function block_core_search()
{
	$content = '<form action="%s" accept-charset="UTF-8" method="get" id="search-theme-form">'
		. '<div class="form-item" id="search-form-wrapper">'
		. '<label for="keywords">%s: </label>'
		. '<input maxlength="128" id="keywords" name="keywords" value="%s" title="Enter the terms you wish to search for." class="text" type="text" />'
		. '</div>'
		. '<input name="search" value="%s" class="button" type="submit" />'
		. '</form>';
	$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';

	$block = array(
		'title' => __('Search'),
		'content' => sprintf($content, SENAYAN_WEB_ROOT_DIR, __('Keywords'), $keywords, __('Search!'))
	);
	return $block;
}

function block_core_welcome()
{
	$block = array(
		'title' => __('Welcome'),
		'content' => __('Welcome To Senayan Library\'s Online Public Access Catalog (OPAC). Use OPAC to search collection in our library.')
	);
	return $block;
}
