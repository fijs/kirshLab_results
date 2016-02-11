<?php 
	session_start();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8" />
<title>Kirsh Lab</title>
<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo rand(1,1000000) ?>" media="screen">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet" type="text/css">
<!--<script src="myscripts.js"></script>-->
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
</head>
<body>
<div id="header">
	
	<div style='float: right; padding-right: 20px; padding-top: 25px;'>
	Experiment Coding - INTERACTIVE COGNITION LAB
	</div>
</div>
<div style="width: 1000px; margin-right: auto; margin-left: auto">
<div style="width: 300px; float: left;">
<h1>Not Processed</h1>
<form action='process.php' method='post'>
<table>
<tr><td>Process</td><td>Demographics ID</td></tr>
<?php
	
	// connect to the SQL server
	require 'configuration.php';
	$result = $mysqli->query("SELECT DISTINCT `demographics_id` FROM  `data-input` WHERE `demographics_id` NOT IN 
		(SELECT `demographics_id` FROM `processed_subjects`) ");
	// call fetch_assoc() function from the mysqli object $result and store the row information in an array called $row
	while ($row = $result->fetch_assoc())
	{
		echo "<tr><td><input type='checkbox' name='demo_ids[]' value='".$row['demographics_id']."'></td><td>".$row['demographics_id']."</td></tr>";
	}
		
?>
</table>
<input type="submit">
</form>
</div>


<div style="width: 300px; float: left">
<h1>Processed</h1>

<table>
<tr><td>Delete</td><td>Demographics ID</td></tr>
<?php

	$result = $mysqli->query("SELECT DISTINCT `demographics_id` FROM  `processed_subjects`");
	// call fetch_assoc() function from the mysqli object $result and store the row information in an array called $row
	while ($row = $result->fetch_assoc())
	{
		echo "<tr><td><input type='checkbox' value='".$row['demographics_id']."'></td><td>".$row['demographics_id']."</td></tr>";
	}
		
?>
</table>
<input type="submit">
</div>

<div style='float: left; padding-left: 20px; padding-top: 50px;'>
		<form action='download.php' method='post'>
			<input type="submit" value="Download CSV file">
		</form>
</div>

</div>