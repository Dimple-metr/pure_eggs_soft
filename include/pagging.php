<?php
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
	//error_reporting(E_ALL);
    /*
     * Local functions
     */
 if(!isset($isJOIN)) {
        $isJOIN = "";
    }

    function fatal_error ( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
        die( $sErrorMessage );
    }


    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
            intval( $_GET['iDisplayLength'] );
    }

	
    /*
     * Ordering
     */
	 
    $sOrder = "ORDER BY $hOrder ";
    if ( isset( $_GET['iSortCol_0'] ) && $_GET['iSortCol_0']!='0')
    {
       $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
            }
        }

        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( trim($sOrder) == "ORDER BY" )
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
	
    $sWhere = "where ( 1 ";
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
		$sWhere .= "AND	( ";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
            {				
                $sWhere .= $aColumns[$i]." LIKE '%".$dbcon->real_escape_string( $_GET['sSearch'] )."%' OR ";
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ") ";
    }
	if(isset($isWhere) && is_array($isWhere) && $isWhere != NULL) {
		$sWhere .= "AND ".implode(" AND ",$isWhere);
	}

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            /*if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }*/
			$sWhere .= " AND ";
            $sWhere .= $aColumns[$i]." LIKE '%".$db->real_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }

    $sWhere .= ')';

	if(str_replace(" ","",$sWhere) == "WHERE()")
		$sWhere == "";
	$sGroup='';
	if(isset($hGroupby) && is_array($hGroupby) && $hGroupby != NULL)		
	{
		$sGroup='Group by '.implode(",",$hGroupby);
	}
    /*
     * SQL queries
     * Get data to display
     */
    if($isJOIN == "") {
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM   $sTable
			$sWhere
            $sGroup			
            $sOrder			
            $sLimit
        ";
    }
    else {
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM   $sTable
            ".implode(" ",$isJOIN)." 
			$sWhere
            $sGroup			
            $sOrder			
            $sLimit
        ";
    }

	
	//echo $sQuery."<br><br><br>";
	//echo '123';	
    $rResult = $dbcon -> query( $sQuery ) or fatal_error( 'MySQL Error: ' . $dbcon -> error );

    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = $dbcon->query( $sQuery ) or fatal_error( 'MySQL Error: ' . $dbcon->error);
    $aResultFilterTotal = mysqli_fetch_assoc($rResultFilterTotal );
    $iFilteredTotal = $aResultFilterTotal['FOUND_ROWS()'];

    /* Total data set length */
    $counting = "COUNT(".$sIndexColumn.")";
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM   $sTable ".implode(" ",$isJOIN)." $sWhere";
	//echo $sQuery."<br><br><br>";
    $rResultTotal = $dbcon->query( $sQuery ) or fatal_error( 'MySQL Error: ' . $dbcon->error );
    $aResultTotal = mysqli_fetch_assoc($rResultTotal);
    $iTotal = $aResultTotal[$counting];

    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    $sqlReturn = array();
    $sr = intval( $_GET['iDisplayStart'] )+1;
    while ( $aRow = mysqli_fetch_assoc($rResult) )
    {
        $aRow['sr'] = $sr;
        $sqlReturn[] = $aRow;
		$sr++;
    }
    
    //echo json_encode( $output );
?>