<?php
	//require_once "usersession.php";
	require_once "../../../conf.php";							// paneme juhise kus on serveri andmed/paroolid kus andmed asuvad
	//echo $server_host;
	$news_input_error = null;
	//var_dump($_POST);											// On olemas ka $_GET		// näitab kõiki postitusi
	$titleSave = null; 		// pealkirja väli
	$contentSave = null; 	// sisu väli
	$authorSave = null;		// autori siu
	
	if(isset($_POST["news_submit"])){
		if(empty($_POST["news_title_input"])){
			$news_input_error = "Uudise pealkiri puudub! ";
		} else {
			$news_title = test_input($_POST["news_title_input"]);
		}
		if(empty($_POST["news_content_input"])){
			$news_input_error .= "Uudise tekst puudub!";
		} else {
			$news_content = test_input($_POST["news_content_input"]);
		}
		if(!empty($_POST["news_author_input"])){
			$news_author = test_input($_POST["news_author_input"]);
		}
		
		if(empty($news_input_error)){
			//salvestame andmebaasi
			$news_title_input = test_input($_POST["news_title_input"]);
			$news_content_input = test_input($_POST["news_content_input"]);
			$news_author_input = test_input($_POST["news_author_input"]);
			store_news($news_title_input $news_content_input, $news_author_input);
		}
	}

	function test_input($input) { 		// sisendandmete valideerimise funktsioon
		$data = trim($input);			// üleliigsed tühikute korrigeerimine
		$data = stripslashes($input);	// kaldriipsude kaotamine
		$data = htmlspecialchars($input);	// ülejäänud mudru
		return $input;
	  }

?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="UTF-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lisamine !!!</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<form method="POST"action="<?php echo htmlspecialchars ($_SERVER["PHP_SELF"]);?>">
		<label for="news_title_input">Pealkiri uudisele: </label> 
		<br>
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo $contentSave; ?>">
		<br>
		<br>
		<label for="news_content_input">Uudise tekst:</label> <br>
		<textarea name="news_content_input" id="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $contentSave; ?></textarea><br>
		<br>
		<label for="news_author_input">Nimi:</label> <br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value="<?php echo $authorSave; ?>"><br><br>
		<input type="submit" name="news_submit" value="Lisa Uudis...">
		<br>
	</form>
	<p><?php echo $news_input_error; ?></p>
</body>
</html>