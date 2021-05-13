<?php
	require_once "usesession.php";
	// alustame sessiooniga
	/*
	session_start();
	//kas kasutaja on loginud sisse - kui ei ole siis tagasi page lehele
	if(!isset($_SESSION["user_id"])) {
		header("Location: page.php");
	}
	//väljalogimine
	if(isset($_GET["logout"])) {
		session_destroy();
		header("Location: page.php");
	}
	*/
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Sisseloginud kasutaja, vinge süsteem!</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<ul>
		<li><a href="?logout=1">Logi välja</a></li>
		<li><a href="add_news.php">Uudiste lisamine</a></li>
		<li><a href="show_news.php">Uudiste lugemine</a></li>
		<li><a href="upload_photo.php">Fotode üleslaadimine</a></li>
	</ul>
</body>
</html>