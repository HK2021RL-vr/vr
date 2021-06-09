<?php

require_once "../../../conf.php";
require_once "fnc_upload_photo.php";

// tubeb fnc failist
$pictures_to_html = show_pic();

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
	<h1>Galerii</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
    <div class="galerii">
    <?php echo $pictures_to_html; ?>
	</div>
    <div class="nupp">
    <p><a href="home.php">Avalehele</a></p>
    <p><a href="add_news.php">Uudiste lisamine</a></li></p>
    <p><a href="show_news.php">Uudiste lugemine</a></p>
    <p><a href="upload_photo.php">Fotode üleslaadimine</a></p>
    </div>
    </div>
	
</body>
</html>