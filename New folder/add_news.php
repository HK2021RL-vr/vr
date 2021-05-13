<?php
	//require_once "usersession.php";
	require_once "../../../conf.php";							// paneme juhise kus on serveri andmed/paroolid kus andmed asuvad
	//echo $server_host;
	$news_input_error = null;
	//var_dump($_POST);											// On olemas ka $_GET		// näitab kõiki postitusi
	$titleSave = null; 		// pealkirja väli
	$contentSave = null; 	// sisu väli
	$authorSave = null;		// autori siu
	if(empty($_POST["news_content_input"])){
		$news_input_error .= "Uudise tekst on puudu! ";		//.= lisa olemasolevale juurde
		$titleSave = (isset($_POST['news_title_input']) ? $_POST['news_title_input'] : ''); // sisu meeldejätt
		$authorSave = (isset($_POST['news_author_input']) ? $_POST['news_author_input'] : '');
		}

		if(empty($news_input_error)){	
			$news_title_input = test_input($_POST["news_title_input"]);
			$news_content_input = test_input($_POST["news_content_input"]);
			$news_author_input = test_input($_POST["news_author_input"]);
			//andmebaasi
			store_news("news_title_input", "news_content_input", "news_author_input");
		}

	function store_news($news_title, $news_content, $news_author){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];

		//ühenduse loomine
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"],);
		//lehe keel
		$conn -> set_charset("utf8");
		// SQL käsk
		$stmt = $conn -> prepare("INSERT INTO vr21_news (vr21_news_news_title, vr21_news_news_content, vr21_news_news_author) VALUES (?,?,?)");
		echo $conn -> error;
		// ?-ga andmete sidumine i-integer, s-string d-decimal, need üksühele väljaga
		$stmt -> bind_param("sss", $news_title, $news_content, $news_author);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
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
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label for="news_title_input">Pealkiri uudisele: </label> <br>
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri"><br>
		<br>
		<label for="news_content_input">Uudise tekst:</label> <br>
		<textarea name="news_content_input" id="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"></textarea><br>
		<br>
		<label for="news_author_input">Nimi:</label> <br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi"><br><br>
		<input type="submit" name="news_submit" value="Lisa Uudis...">
		<br>
	</form>
	<p><?php echo $news_input_error; ?></p>
</body>
</html>