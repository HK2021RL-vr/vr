<?php
	//echo $server_host;
	require_once "usesession.php";
    require_once "../../../conf.php";
    require_once "fnc_general.php";
    require_once "fnc_upload_photo.php";
    require_once "classes/Upload_photo.class.php";
    $news_photo_result = null;
    $news_input_error = null;
    $news_title = null;
    $news_content = null;
    $news_author = null;
    $file_size_limit = 1 * 1024 * 1024;
    $file_name_prefix = "vr_";
    $image_max_w = 600;
    $image_max_h = 400;
	$image_file_type = null;
	$allowed_image_types = ['image/jpeg', 'image/png'];
	$type_names = ['jpg', 'png'];
	$alias_name = null;

	//var_dump($_POST); // on olemas ka $_GET
	if(isset($_POST["news_submit"])){
		if(empty($_POST["news_title_input"])){
			$news_input_error = "Uudise pealkiri on puudu! ";
		} else {
			$news_title = test_input($_POST["news_title_input"]);
		}
		if(empty($_POST["news_content_input"])){
			$news_input_error .= "Uudise tekst on puudu!";
		} else {
			$news_content = test_input($_POST["news_content_input"]);
		}
		if(!empty($_POST["news_author_input"])){
			$news_author = test_input($_POST["news_author_input"]);
		}
		if(empty($news_input_error)){
			//salvestame andmebaasi

			if($_FILES["file_input"]["size"] > 0) { 
                $timestamp = microtime(1) * 10000;
                $photo_upload = new Upload_photo($_FILES["file_input"], $file_size_limit, $allowed_image_types, $type_names);
                $image_file_name = $photo_upload->create_image_name($file_name_prefix, $timestamp);
                $target_file = "../upload_photos_news/" .$image_file_name;
                if(empty($photo_upload->upload_error)) {
                    $photo_upload->resize_photo($image_max_w, $image_max_h);            
                    $result = $photo_upload->save_image_to_file($target_file);
                   // echo $photo_upload->upload_error;
                    //salvestame pildi andmebaasi
                    $news_photo_result = store_news_photo($_SESSION["user_id"], $image_file_name);
                }
            }
            store_news($news_title, $news_content, $news_author, $news_photo_result);
		}
	}
	
	function store_news($news_title, $news_content, $news_author, $photo_id){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("INSERT INTO vr21_news(vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_photo_id) VALUES (?,?,?,?)");
		echo $conn -> error;
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("sssi", $news_title, $news_content, $news_author, $photo_id);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
		$GLOBALS["news_input_error"] = null;
		$GLOBALS["news_title"] = null;
		$GLOBALS["news_content"] = null;
		$GLOBALS["news_author"] = null;
	}
	
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
	<h1>Uudiste lisamine</h1>
	<p>See leht on valminud õppetöö raames!</p>

	<form method="POST"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="news_title_input">Uudise pealkiri</label>
		<br>
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo $news_title; ?>">
		<br>
		<label for="news_content_input">Uudise tekst</label>
		<br>
		<textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $news_content; ?></textarea>
		<br>
		<label for="news_author_input">Uudise lisaja nimi</label>
		<br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value="<?php echo $news_author; ?>">
		<br>
		<label for="file_input">Lisa uudisele pilt!</label>
		<input id="file_input" name="file_input" type="file">
		<br>
		<input type="submit" name="news_submit" value="Salvesta uudis!">
	</form>
	<hr>
	<p><?php echo $news_input_error; ?></p>
	<hr>
	<p><a href="home.php">Avalehele</a></p>
	<a href="show_news.php">Uudiste lugemine</a>
	<p><a href="upload_photo.php">Fotode üleslaadimine</a></p>
	<p><a href="galerii.php">Galerii</a></p>
	<p><a href="?logout=1">Logi välja</a></p>
	<hr>

</div>	
</body>
</html>