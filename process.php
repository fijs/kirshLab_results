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

<?php
//connect to DB
require 'configuration.php';

$demoIDS= $_POST['demo_ids'];

foreach ($demoIDS as $demoID)

	mysqli_query($mysqli, "CREATE TABLE `processing-".$demoID."` AS (SELECT `data-input`.*, 
	1000000 * (`all_words`.ngram_value) AS word_freq, char_length(`data-input`.word) AS char_count 
	FROM `all_words`, `data-input` WHERE `demographics_id`=".$demoID." 
	AND `data-input`.word = `all_words`.word GROUP BY `data-input`.time ORDER BY `data-input`.word ASC)");
	mysqli_query($mysqli, "CREATE TABLE `temp-".$demoID."` AS ( SELECT @dupe_count:=CASE WHEN @word=`word` 
	AND @status=`status` THEN @dupe_count+1 ELSE 0 END AS dupe_count,
	@word:=`word` AS word, @status:=`status` AS status, @time:=`time` AS time 
	FROM `processing-".$demoID."`, (SELECT @dupe_count:=0,@word:='',@status:='',@time:='') AS t
	ORDER BY word )");
	mysqli_query($mysqli, "ALTER TABLE `temp-".$demoID."` MODIFY COLUMN `time` datetime, MODIFY COLUMN `dupe_count` int(5)");
	mysqli_query($mysqli, "CREATE TABLE `processed-".$demoID."` AS ( SELECT DISTINCT `processing-".$demoID."`.*, 
	`temp-".$demoID."`.dupe_count FROM `processing-".$demoID."` INNER JOIN `temp-".$demoID."` 
	ON (`processing-".$demoID."`.`word` = `temp-".$demoID."`.word) 
	AND (`processing-".$demoID."`.`status` = `temp-".$demoID."`.status) 
	AND (`processing-".$demoID."`.`time` = `temp-".$demoID."`.time) )
	ORDER BY time");	
	mysqli_query($mysqli, "DROP TABLE `processing-".$demoID."`, `temp-".$demoID."` ");
	mysqli_query($mysqli, "ALTER TABLE `processed-".$demoID."` ADD COLUMN binned_min int(1)");
	mysqli_query($mysqli, "UPDATE `processed-".$demoID."`
	SET `binned_min`= (CASE WHEN `paradigm_time` BETWEEN 0.00 AND 60.00 THEN 1
	WHEN `paradigm_time` BETWEEN 60.00 AND 120.00 THEN 2
	WHEN `paradigm_time` BETWEEN 120.00 AND 180.00 THEN 3 
	ELSE 0 END)");
	mysqli_query($mysqli, "INSERT INTO `processed_subjects` SELECT * FROM `processed-".$demoID."` ");
	mysqli_query($mysqli, "INSERT INTO `summary-table` (`demographics_id`,`stim`,`status`,`condition`, 
	`num_valids`,`words_min1`,`words_min2`,`words_min3`) 
	select `t`.`demographics_id`, `t`.`stim`, `t`.`status`, `t`.`condition`, 
	(`t`.words_min1+`t`.words_min2+`t`.words_min3) as num_valids, `t`.words_min1, `t`.words_min2, `t`.words_min3 
	from( 
	select `demographics_id`, `stim`, `status`, `condition`, 
	sum(case when `binned_min`=1 then 1 else 0 end) as words_min1, 
	sum(case when `binned_min`=2 then 1 else 0 end) as words_min2, 
	sum(case when `binned_min`=3 then 1 else 0 end) as words_min3
	from `processed-".$demoID."` group by status )t");
	mysqli_query($mysqli, "DROP TABLE `processed-".$demoID."`, `temp-".$demoID."` ");	

//close connection and return to index.php
mysqli_close($mysqli);
header('Location: index.php');
?>
</div>