<?php
	global $db_table;
	global $felix_survey_name;
	
	$host = 'localhost';
	$user = '';
	$password = '';
	$dbname = '';
	$db_table = '';
	
	// Open connection
	$link = mysql_connect($host, $user, $password);

	if(!$link): die('Failed to connect to the database'); endif;

	mysql_select_db($dbname);
	
	$felix_survey_name = '';
	
	function getColNames() {
		global $db_table;
		$sql = "SELECT `column_name` FROM `information_schema`.`columns` WHERE `table_name` = '$db_table' ORDER BY `ordinal_position`";
		$res = mysql_query($sql);
		
		$cols = array();
		while($row = mysql_fetch_object($res)) {
			$cols[] = $row->column_name;
		}
		
		return $cols;
	}
