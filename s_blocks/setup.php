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


define('INDEX_AUTH', '1');

if (!defined('SENAYAN_BASE_DIR')) {
    // main system configuration
    require '../../../../sysconfig.inc.php';
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

require('../func.php');
checkip();
checkref();

require('./func.php');

if ($_POST)
{
	list($host, $dir, $file) = scinfo();
	$theme = variable_get('opac_theme');
	if (isset($_GET['sort']))
	{
		$alert = __('Blocks configuration has not been saved!');
		$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
/*
		$script = "";
*/
		unset($_POST['saveData']);
		if (count($_POST) > 0)
		{
			foreach ($_POST as $plugin => $blocks)
			{
				if (is_array($blocks) AND count($blocks) > 0)
				{
					foreach ($blocks as $delta => $prefs)
					{
						$sql = sprintf("UPDATE `plugins_blocks` "
							. "SET `region` = '%s', `weight` = '%s' "
							. "WHERE `plugin` = '%s' AND `delta` = '%s' AND `theme` = '%s';",
							$prefs['region'],
							$prefs['weight'],
							$plugin,
							$delta,
							$theme
						);
						$dbs->query($sql);
					}
				}
			}
			$alert = __('Blocks configuration has been saved!');
		}
	}
	else
	{
		$post = (object) $_POST;
		$post->desc = isset($post->desc) ? trim($post->desc) : '';
		$post->code = isset($post->code) ? trim($post->code) : '';
		$post->block = trim($post->block);
		$valid = ( ! preg_match("/^([-a-z0-9_-])+$/", $post->block)) ? FALSE : TRUE;
		if ( ! $_GET AND ($valid === FALSE || empty($post->desc) || empty($post->code)))
		{
			$alert = __('Error!\nBlock has not been saved!');
			if ($valid === FALSE)
				$script = "parent.$('#block]').focus();";
			else if (empty($post->desc))
				$script = "parent.$('#desc]').focus();";
			else
				$script = "parent.$('#code').focus();";
		}
		else
		{
			$check = block_custom_get($post->block, 'check');
			$alert = __('Block has been saved');
			if ($check > 0 || ($_GET AND isset($_GET['block'])))
			{
				$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
				
			}
			else
			{
				$script = "parent.$('#mainContent').simbioAJAX('". $dir . "/');";
				$sql = sprintf("INSERT INTO `plugins_blocks_custom` (`block`, `desc`, `title`, `code`, `filter`) VALUE ('%s', '%s', '%s', '%s', '%s')",
					$post->block,
					$post->desc,
					$post->title,
					$post->code,
					$post->filter
				);
				$dbs->query($sql);
				$themes = list_avtheme();
				if (count($themes) > 0)
				{
					$sqlth = "INSERT INTO `plugins_blocks` (`plugin`, `delta`, `theme`, `title`) VALUES ";
					$valth = array();
					foreach ($themes as $th_id => $th_name)
					{
						$valth[] = sprintf("('%s', '%s', '%s', '%s')",
							'block',
							$post->block,
							$th_id,
							$post->title
						);
					}
					$valsth = implode(", ", $valth);
					$sqlth .= $valsth;
				}
				$dbs->query($sqlth);
				unset($valth);
				unset($valsth);
			}
		}
	}

	echo "<html><head><script type=\"text/javascript\">alert('$alert');$script</script></head><body></body></html>";
}

exit();
