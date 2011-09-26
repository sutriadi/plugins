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

if ( ! defined('MODPLUGINS_WEB_ROOT_DIR'))
	define('MODPLUGINS_WEB_ROOT_DIR', MODULES_WEB_ROOT_DIR . 'plugins/');
if ( ! defined('MODPLUGINS_BASE_DIR'))
	define('MODPLUGINS_BASE_DIR', MODULES_BASE_DIR . 'plugins/');

require(MODPLUGINS_BASE_DIR . 's_blocks/blocks.php');

/*
 * 
 * name: block_get
 * @param $block string, name of block
 * @param $mode string, return mode
 * @return array or number
 */
function block_custom_get($block, $mode = 'array')
{
	global $dbs;
	
	$sql = sprintf("SELECT `block`, `desc`, `title`, `code`, `filter` FROM `plugins_blocks_custom` WHERE `block` = '%s'", $block);
	$block = '';
	$desc = '';
	$title = '';
	$code = '';
	$filter = '';
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($mode == 'array')
	{
		if ($num_rows > 0)
		{
			$row = (object) $rows->fetch_assoc();
			$block = $row->block;
			$desc = $row->desc;
			$title = stripslashes($row->title);
			$code = stripslashes($row->code);
			$filter = $row->filter;
		}
		return array($block, $desc, $title, $code, $filter);
	}
	else if ($mode == 'check')
		return $num_rows;
}

/*
 * 
 * name: block_settings_get
 * @param $block
 * @param $delta
 * @return
 */
function block_settings_get($block, $delta)
{
	global $dbs, $theme;
	
	$sql = sprintf("SELECT `plugin`, `delta`, `region`, `weight`, `title`, `classes` "
		. "FROM `plugins_blocks` WHERE `plugin` = '%s' AND `delta` = '%s' AND `theme` = '%s'",
		$block,
		$delta,
		$theme
	);
	$plugin = '';
	$delta = '';
	$region = '';
	$weight = '';
	$title = '';
	$classes = '';
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($num_rows > 0)
	{
		$row = (object) $rows->fetch_assoc();
		$plugin = $row->plugin;
		$delta = $row->delta;
		$region = $row->region;
		$weight = $row->weight;
		$title = stripslashes($row->title);
		$classes = $row->classes;
	}
	return array($plugin, $delta, $region, $weight, $title, $classes);
}

/*
 * 
 * name: block_default_regions
 * @param none
 * @return array
 */
function block_default_regions()
{
	return array(
		'header' => __('Header'),
		'left' => __('Left Sidebar'),
		'top-node' => __('Content Top'),
		'bottom-node' => __('Content Bottom'),
		'right' => __('Right Sidebar'),
		'footer' => __('Footer'),
		'none' => __('Disabled'),
	);
}

/*
 * 
 * name: block_get_regions
 * @param none
 * @return array
 */
function block_get_regions()
{
	global $info;

	$default_regions = block_default_regions();
	if ( ! isset($info['regions']))
	{
		$info['regions'] = $default_regions;
	}
	else
	{
		foreach ($info['regions'] as $region => $region_name)
		{
			if ( ! array_key_exists($region, $default_regions))
				unset($info['regions'][$region]);
		}
	}
	return $info['regions'];
}

/*
 * 
 * name: block_all_list
 * @param
 * @return
 */
function block_all_list($return = false)
{
	global $dbs, $theme, $theme_dir;
	
	$preblocks = array();

	$sql = "SELECT * FROM `plugins_blocks_custom`";
	$rows = $dbs->query($sql);
	$num_rows = $rows->num_rows;
	if ($num_rows > 0)
	{
		$preblocks['block'] = array();
		while($row = $rows->fetch_assoc())
		{
			$preblocks['block'][$row['block']] = array(
				'desc' => $row['desc']
			);
		}
	}
	
	$preblocks['menu'] = block_menu();
	$preblocks['core'] = block_core();

	$info = drupal_parse_info_file($theme_dir . '/tpl.info');

	$info['regions'] = block_get_regions();

	$sql = "SELECT * FROM `plugins_blocks` WHERE `theme` = '%s' ORDER BY `region` ASC, `weight` ASC, `delta` ASC";
	$sqlf = sprintf($sql, $theme);
	$rows = $dbs->query($sqlf);
	$num_rows = $rows->num_rows;
	$blocks = array();
	if ($num_rows > 0)
	{
		while($row = $rows->fetch_assoc())
		{
			$block_array = array(
				$preblocks[$row['plugin']][$row['delta']]['desc'] => array(
					'block' => $row['plugin'],
					'delta' => $row['delta'],
					'title' => $row['title'],
					'theme' => $row['theme'],
					'weight' => isset($row['weight']) ? $row['weight'] : 0,
					'desc' => $preblocks[$row['plugin']][$row['delta']]['desc'],
					'classes' => ! empty($row['classes']) ? explode(' ', trim($row['classes'])) : array(),
				)
			);
			if (isset($row['region']) AND ! empty($row['region']))
			{
				if (isset($blocks[$row['region']]))
					$blocks[$row['region']] = array_merge($blocks[$row['region']], $block_array);
				else
					$blocks[$row['region']] = $block_array;
			}
			else
			{
				if (isset($blocks['none']))
					$blocks['none'] = array_merge($blocks['none'], $block_array);
				else
					$blocks['none'] = $block_array;
			}
		}
	}
	else if ($num_rows == 0 AND $return === false)
	{
		$sqli = sprintf("INSERT INTO `plugins_blocks` "
			. "(`plugin`, `delta`, `theme`, `region`, `weight`) "
			. "(SELECT `plugin`, `delta`, '%s', `region`, `weight` FROM plugins_blocks WHERE `theme` = 'base')",
			$theme
		);
		$dbs->query($sqli);
		$blocks = block_all_list(true);
	}
	return $blocks;
}

/*
 * 
 * name: set_region_options
 * @param $default string
 * @return string html
 */
function set_region_options($default = 'none')
{
	global $default_regions;
	$block_region_options = '';
	if (empty($default))
		$default = 'none';
	foreach ($default_regions as $region => $region_name)
	{
		$selected = ($default === $region) ? "selected" : "";
		$block_region_options .= '<option value="' . $region . '" ' . $selected . '>' . $region_name . '</option>';
	}
	return $block_region_options;

}

/*
 * 
 * name: set_action_links
 * @param $block string
 * @return string html
 */
function set_action_links($block)
{
	global $dir;
	
	$block = (object) $block;
	
	$params = 'block=' . $block->block . '&delta=' . $block->delta . '&theme=' . $block->theme;
	$links = sprintf('<a href="%s">%s</a>',
		$dir . '/add.php?' . $params,
		__('Configure')
	);
	if ($block->block == 'block')
	{
		$links .= sprintf(' <a href="%s">%s</a>',
			$dir . '/add.php?act=del&' . $params,
			__('Delete')
		);
	}
	return $links;
}

function setup_block($plugin = 'block', $delta, $title)
{
	global $dbs;
	
	$sqlth = "INSERT INTO `plugins_blocks` (`plugin`, `delta`, `theme`, `title`) VALUES ";
	$tplval = sprintf("('%s', '%s', '%s', '%s')",
		$plugin,
		$delta,
		'%s',
		$title
	);
	$valth = array();
	$sql = sprintf("SELECT `theme` FROM `plugins_blocks` GROUP BY `theme`");
	$themes = $dbs->query($sql);
	if ($themes->num_rows > 0)
	{
		while ($theme = $themes->fetch_assoc())
		{
			$valth[] = sprintf($tplval, $theme['theme']);
		}
	}
	else
	{
		$valth[] = sprintf($tplval, 'base');
	}
	$valsth = implode(", ", $valth);
	$sqlth .= $valsth;
	$dbs->query($sqlth);
	unset($valth);
	unset($valsth);
}
