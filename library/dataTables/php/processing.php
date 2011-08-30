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
include('../../../s_datatables/func.php');

$plugin = '';
$table = '';

if ($_GET AND isset($_GET['plugin']))
	$plugin = $_GET['plugin'];

if ($_GET AND isset($_GET['table']))
	$table = $_GET['table'];

if ($plugin == '' || $table == '')
{
	exit();
}

checkip();
checken($plugin);
checken($table, 'table');
checkref('host');

$vars = table_get($table);
$base_cols_name = base_cols_name($vars[1]);
$fcols = cols_get($table);
$order_cols = cols_order_get($table);

if (count($order_cols) > 0)
{
	$columns = array();
	foreach ($order_cols as $key => $val)
	{
		if (array_key_exists($key, $base_cols_name))
		{
			if (isset($columns[$val]))
			{
				$columns[$val+1] = $key;
			}
			else
				$columns[$val] = $key;
			unset($col_arrs);
		}
	}

	if (isset($columns) AND is_array($columns))
	{
		ksort($columns);
		$ordc = array();
		foreach ($columns as $ci => $cv)
		{
			$ordc[] = $cv;
		}
		$columns = $ordc;
		unset($ordc);
		if ( ! empty($fcols[2]))
		{
			$end_cols = explode(chr(10), $fcols[2]);
			$num_cols += count($end_cols);
			$a_content = array();
			foreach ($end_cols as $val)
			{
				$content = $val;
				$del = explode(":", $val);
				if (count($del) > 1)
				{
					unset($del[0]);
					$content = implode(":", $del);
				}
				$a_content[] = trim($content);
			}
		}
	}
}

$gaSql['user']       = DB_USERNAME;
$gaSql['password']   = DB_PASSWORD;
$gaSql['db']         = DB_NAME;
$gaSql['server']     = DB_HOST . ':' . DB_PORT;

$aColumns = $columns;
$sIndexColumn = ($vars[1] == 'member') ? 'member_id' : 'biblio_id';
$sTable = $vars[1];

$leftjoin = '';
if ($vars[1] == 'member')
{
	$leftjoin = " LEFT JOIN mst_member_type ON mst_member_type.member_type_id = member.member_type_id ";
}
else
{
	$leftjoin = "";
}

$gaSql['link'] =  @mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
	die( 'Could not open connection to server' );

mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
	die( 'Could not select database '. $gaSql['db'] );

/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
{
	$sLimit = "LIMIT ".mysql_real_escape_string( $_POST['iDisplayStart'] ).", ".
		mysql_real_escape_string( $_POST['iDisplayLength'] );
}

/*
 * Ordering
 */
$sOrder = "";
$first_order = (in_array($fcols[0], array('radio', 'checkbox'))) ? 1 : 0;
if ( isset( $_POST['iSortCol_0'] ) )
{
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
	{
		if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
		{
			$sOrder .= $aColumns[ intval( $_POST['iSortCol_'.$i] ) - $first_order ]."
				".mysql_real_escape_string( $_POST['sSortDir_'.$i] ) .", ";
		}
	}
	
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == "ORDER BY" )
	{
		$sOrder = "";
	}
}

/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ( $_POST['sSearch'] != "" )
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
	if ( $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )
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

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "" .
	" SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)).
	" FROM $sTable " .
	" %s " .
	" $sWhere " .
	" $sOrder " .
	" $sLimit " .
"";
$sQuery = sprintf($sQuery, $leftjoin);
/*
echo $sQuery;
*/
$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS()
";
$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
	SELECT COUNT(".$sIndexColumn.")
	FROM   $sTable
";
$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];

/*
 * Output
 */
$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : '';
$sOutput = '{' .
	'"sEcho": '.$sEcho.', ' .
	'"iTotalRecords": '.$iTotal.', ' .
	'"iTotalDisplayRecords": '.$iFilteredTotal.', ' .
	'"aaData": [ ';
while ( $aRow = mysql_fetch_array( $rResult ) )
{
	$sOutput .= "[";
	if (in_array($fcols[0], array('radio', 'checkbox')))
	{
		$sOutput .= '"<input type=\"' . $fcols[0] . '\" id=\"' . $aRow[$sIndexColumn] . '\" name=\"' . $vars[1] . '[]\" value=\"' . $aRow[$sIndexColumn] .  '\" />", ';
	}
/*
	print_r($aColumns);
*/
/*
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
*/
	foreach ($aColumns as $vColumns)
	{
		$sOutput .= '"'. str_replace('"', '\"', $aRow[ $vColumns ]).'",';
	}
	
	if (isset($a_content) AND is_array($a_content) AND count($a_content > 0))
	{
		foreach ($a_content as $end_col)
		{
			$sOutput .= sprintf('"%s",', $end_col);
		}
	}
	
	$sOutput = substr_replace( $sOutput, "", -1 );
	$sOutput .= "],";
}
$sOutput = substr_replace( $sOutput, "", -1 );
$sOutput .= '] }';

header('Content-type: text/plain');
echo $sOutput;
