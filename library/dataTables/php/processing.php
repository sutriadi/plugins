<?php
/*
 *      processing.php
 *      
 *      from examples_support, DataTables 1.7.5
 *      Modified by Indra Sutriadi Pipii <indra.sutriadi@gmail.com>
 *      
 */

define('INDEX_AUTH', '1');

if (!defined('SENAYAN_BASE_DIR')) {
	require '../../../../../../sysconfig.inc.php';
	require SENAYAN_BASE_DIR.'admin/default/session.inc.php';
}
require SENAYAN_BASE_DIR.'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('plugins', 'r');
$can_read = utility::havePrivilege('plugins', 'w');

if (!$can_read) {
      die('<div class="errorBox">You dont have enough privileges to view this section</div>');
}

$conf = $_SESSION['plugins_conf'];
include('../../../func.php');

$plugin = '';
$table = '';

if ($_GET AND isset($_GET['plugin']))
	$plugin = $_GET['plugin'];

if ($_GET AND isset($_GET['table']))
	$table = $_GET['table'];

checkip();
checken($plugin);
checken($table, 'table');
checkref('host');

	$gaSql['user']       = DB_USERNAME;
	$gaSql['password']   = DB_PASSWORD;
	$gaSql['db']         = DB_NAME;
	$gaSql['server']     = DB_HOST . ':' . DB_PORT;

	$aColumns = array( 'member_id', 'member_name', 'member_type_name', 'inst_name', 'member_email' );
	$sIndexColumn = "member_id";
	$sTable = "member";
		
	$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
	
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_POST['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_POST['iDisplayLength'] );
	}
	
	$sOrder = "";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_POST['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	$sWhere = "";
	if ( isset($_POST['sSearch']) != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_POST['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_POST['bSearchable_'.$i]) && $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_POST['sSearch_'.$i])."%' ";
		}
	}
	
	$sQuery = "" .
		" SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)).
		" FROM $sTable " .
		" LEFT JOIN mst_member_type ON mst_member_type.member_type_id = member.member_type_id " .
		" $sWhere " .
		" $sOrder " .
		" $sLimit " .
	"";
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : '';
	$sOutput = '{' .
		'"sEcho": '.$sEcho.', ' .
		'"iTotalRecords": '.$iTotal.', ' .
		'"iTotalDisplayRecords": '.$iFilteredTotal.', ' .
		'"aaData": [ ';
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$sOutput .= "[";
		$sOutput .= '"<input type=\"checkbox\" id=\"' . $aRow['member_id'] . '\" name=\"members[]\" value=\"' . $aRow['member_id'] .  '\" />", ';
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sOutput .= '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
		}
		$sOutput = substr_replace( $sOutput, "", -1 );
		$sOutput .= "],";
	}
	$sOutput = substr_replace( $sOutput, "", -1 );
	$sOutput .= '] }';

	header('Content-type: text/plain');
	echo $sOutput;
