<?php
/*
 *      processing.php
 *      
 *      from examples_support, DataTables 1.8.1
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
		die(sprintf('<div class="errorBox">%s</div>', __('You dont have enough privileges to view this section')));
	}

	// memanggil konfigurasi dan fungsi
	$conf = $_SESSION['plugins_conf'];
	include('../../../func.php'); // fungsi umum plugin
	include('../../../s_datatables/func.php'); // fungsi khusus datatables

	$plugin = '';
	$table = '';

	// mengambil nama plugin dan table
	if ($_GET AND isset($_GET['plugin']))
		$plugin = $_GET['plugin'];

	if ($_GET AND isset($_GET['table']))
		$table = $_GET['table'];

	// keluar bila nama plugin dan table tidak valid
	if ($plugin == '' || $table == '')
	{
		exit();
	}

	// mengecek ip, plugin aktif, table aktif dan referer
	checkip();
	checken($plugin);
	checken($table, 'table');
	checkref('plugin');

	// mengambil data table, nama kolom, kolom dan pengurutan kolom
	$dtables = table_get($table);
	$base_cols_name = base_cols_name($dtables[1]);
	$fcols = cols_get($table);

	$trender = table_render($table, false);
	extract($trender);

	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = $precols;
	
	/* Indexed column (used for fast and accurate table cardinality) */
	if ($dtables[1] === 'member')
		$sIndexColumn = 'member_id';
	elseif ($dtables[1] === 'biblio')
		$sIndexColumnt = 'biblio_id';
	else
		$sIndexColumnt = 'content_id';
	
	/* DB table to use */
	$sTable = $dtables[1];

	/* Database connection information */
	$gaSql['user']       = DB_USERNAME;
	$gaSql['password']   = DB_PASSWORD;
	$gaSql['db']         = DB_NAME;
	$gaSql['server']     = DB_HOST . ':' . DB_PORT;

	/* 
	 * MySQL connection
	 */
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
	if ( isset($_POST['sSearch']) AND $_POST['sSearch'] != "" )
	{
		$sWhere = 'WHERE (';
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
		if ( isset($_POST['bSearchable_'.$i]) AND $_POST['bSearchable_'.$i] == "true" AND isset($_POST['sSearch_'.$i]) AND $_POST['sSearch_'.$i] != '' )
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

	/* Left joining table */
	$sJoin = '';
	if ($dtables[1] == 'member')
	{
		$sJoin = " LEFT JOIN `mst_member_type` ON `mst_member_type`.`member_type_id` = `member`.`member_type_id` ";
	}
	else if ($dtables[1] == 'biblio')
	{
		$sJoin = "";
	}
	else
	{
		$sJoin = "";
	}

	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns))
		. " FROM " . $sTable
		. " " . $sJoin
		. " " . $sWhere
		. " " . $sOrder
		. " " . $sLimit
	. "";
	$sQuery = trim($sQuery);
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

	if ( ! empty($fcols[4]))
	{
		$add_func = '<?php ' . $fcols[4] . ' ?>';
		ob_start();
		print eval('?>' . $add_func);
		ob_get_contents();
		ob_end_clean();
	}

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
			$sOutput .= '"<input type=\"' . $fcols[0] . '\" id=\"' . $aRow[$sIndexColumn] . '\" name=\"' . $dtables[1] . '[]\" value=\"' . $aRow[$sIndexColumn] .  '\" />", ';
		}
		
		foreach ($aColumns as $vColumns)
		{
			$sOutput .= '"'. str_replace('"', '\"', $aRow[ $vColumns ]).'",';
		}
		
		if (isset($a_content) AND is_array($a_content) AND count($a_content > 0))
		{
			foreach ($a_content as $end_col)
			{
				if ($fcols[3] == true)
				{
					ob_start();
					print eval('?>' . $end_col);
					$end_col = ob_get_contents();
					ob_end_clean();
				}
				$sOutput .= sprintf('"%s",', htmlentities($end_col));
			}
		}
		
		$sOutput = substr_replace( $sOutput, "", -1 );
		$sOutput .= "],";
	}
	$sOutput = substr_replace( $sOutput, "", -1 );
	$sOutput .= '] }';

	header('Content-type: text/plain');
	echo $sOutput;
