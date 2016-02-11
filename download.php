<?php 
	//call to ob_start(); to start recording ouput stream
	ob_start();
	session_start();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8" />
<title>Kirsh Lab</title>
<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo rand(1,1000000) ?>" media="screen">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet" type="text/css">
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
</head>

<body>

<div id="header">
	<div style='float: right; padding-right: 20px; padding-top: 25px;'>
	Experiment Coding - INTERACTIVE COGNITION LAB
	</div>
</div>

<!--<div style="width: 1000px; margin-right: auto; margin-left: auto">-->
<div style="width: 300px; float: left;"></div>


<?php
	//clean recorded ouput stream so it does not print to csv file
	ob_end_clean();
	ob_start();
	// connect to the SQL server
	require 'configuration.php';
	//query to order table by demographics_id
	$pre_result = $mysqli->query("ALTER TABLE `summary-table` ORDER BY `demographics_id` ASC;");
	// query to download table
	$result = $mysqli->query(
		"SELECT 
		'demographics_id', 'stim', 'status', 'condition', 'num_valids', 'words_min1', 'words_min2', 'words_min3'
		UNION ALL
		SELECT `demographics_id`, `stim`, `status`, `condition`, `num_valids`, `words_min1`, `words_min2`, `words_min3`
		FROM `summary-table`"
	); 
	$fp = fopen('php://output', 'w'); 
	if ($fp && $result) { 
		header('Content-Type: text/csv'); 
		header('Content-Disposition: attachment; filename="summary_table_export.csv"'); 
		while ($row = $result->fetch_array(MYSQLI_NUM)) { 
			fputcsv($fp, array_values($row)); 
		} 
		fclose($fp); 
		$mysqli->close();
	};
?>