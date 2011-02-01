<?php
/*
 *      script.php
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
	die('<div class="errorBox">You dont have enough privileges to view this section</div>');
}

require('./func.php');
require('./conf.php');

checkip();
checkref();

?>
<html>
	<head>
		<script language="JavaScript">
			function checkall(f)
			{
				cb=f.elements
				for(n=0;n<cb.length;n++){
					if(cb[n].type=="checkbox")
						cb[n].checked=true
				}
			}
			function checkinvert(f)
			{
				cb=f.elements
				for(n=0;n<cb.length;n++){
					if(cb[n].type=="checkbox")
						cb[n].checked=cb[n].checked==true?false:true
				}
			}
			function uncheckall(f)
			{
				cb=f.elements
				for(n=0;n<cb.length;n++){
					if(cb[n].type=="checkbox")
						cb[n].checked=false
				}
			}
		</script>
	</head>
	<body></body>
</html>
