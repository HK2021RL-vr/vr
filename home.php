<?php
	require_once "usesession.php";
	/* session_start();
	//kas on sisse loginud
	if(!isset($_SESSION["user_id"])){
		header("Location: page.php");
	}
	//välja logimine
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: page.php");
	} */
	
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
	<link rel="stylesheet" href="stiil.css">
</head>
<body>
<div class="container">
	<div class="kiri">
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<ul>
		<li><a href="add_news.php">Uudiste lisamine</a></li>
		<li><a href="show_news.php">Uudiste lugemine</a></li>
		<li><a href="show_news_edit.php">Uudiste muumine</a></li>
		<li><a href="upload_photo.php">Fotode üleslaadimine</a></li>
		<li><a href="galerii.php">Galerii</a></li>
		<li><a href="?logout=1">Logi välja</a></li>
	</ul>
</div>
</div>
</body>
</html>