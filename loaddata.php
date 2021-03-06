<?php     


/*
 * examples/mysql/loaddata.php
 * 
 * This file is part of EditableGrid.
 * http://editablegrid.net
 *
 * Copyright (c) 2011 Webismymind SPRL
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://editablegrid.net/license
 */
                              


/**
 * This script loads data from the database and returns it to the js
 *
 */
       
require_once('config.php');      
require_once('EditableGrid.php');            

/**
 * fetch_pairs is a simple method that transforms a mysqli_result object in an array.
 * It will be used to generate possible values for some columns.
*/
function fetch_pairs($mysqli,$query){
	if (!($res = $mysqli->query($query)))return FALSE;
	$rows = array();
	while ($row = $res->fetch_assoc()) {
		$first = true;
		$key = $value = null;
		foreach ($row as $val) {
			if ($first) { $key = $val; $first = false; }
			else { $value = $val; break; } 
		}
		$rows[$key] = $value;
	}
	return $rows;
}


// Database connection
$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 
                    
// create a new EditableGrid object
$grid = new EditableGrid();

/* 
*  Add columns. The first argument of addColumn is the name of the field in the databse. 
*  The second argument is the label that will be displayed in the header
*/

//$grid->addColumn('id', 'ID', 'integer', NULL, false, NULL, false, true);
$grid->addColumn('date', 'Due Date', 'date');
$grid->addColumn('partNum', 'Part Number', 'string');
$grid->addColumn('partRev', 'Part Rev.', 'string');
$grid->addColumn('customerID', 'Customer Name', 'string' , fetch_pairs($mysqli,'SELECT id, customerName FROM global_customername'),true);
$grid->addColumn('partType', 'Type', 'string');
$grid->addColumn('po', 'PO#', 'string');
$grid->addColumn('so', 'SO#', 'string');
$grid->addColumn('qtyOrder', 'QTY ORDERED', 'string');
$grid->addColumn('tmp_shipped', 'QTY SHIPPED', 'string');
$grid->addColumn('finishID', 'FINISH', 'string' , fetch_pairs($mysqli,'SELECT finishID, finishDesc FROM global_finishid'),true);
//$grid->addColumn('archived', 'Archived?', 'boolean');
$grid->addColumn('id', 'ID', 'integer', NULL, false, NULL, false, true);
$grid->addColumn('hot', 'HOT', 'string');
$grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

$mydb_tablename = (isset($_GET['db_tablename'])) ? stripslashes($_GET['db_tablename']) : 'due_ordersdue';
                                                                       
//$result = $mysqli->query('SELECT *, date_format(lastvisit, "%d/%m/%Y") as lastvisit FROM '.$mydb_tablename );
$result = $mysqli->query('SELECT *,  date_format(date, "%d/%m/%Y") as date FROM '.$mydb_tablename . ' where archived = "0" ORDER by date');


//$result = $mysqli->query('SELECT * FROM '.$mydb_tablename);
$mysqli->close();

// send data to the browser
$grid->renderJSON($result);

