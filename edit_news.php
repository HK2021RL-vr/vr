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
	$edit_news_id = null; 
    if(isset($_GET["news_id"])){ 
        $edit_news_id = $_GET["news_id"];
        $_SESSION['news_id'] = $edit_news_id; 
    }
	
	$news_content_html = get_news($_SESSION['news_id']);

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
            store_news($news_title, $news_content, $news_author, $news_photo_result, $_SESSION['news_id']);
			header('Location: show_news_edit.php');
		}
	
	}
	//Kood salvestab muudetud uudise, kui on valitud pilt. Muidu ei toimi.
	function store_news($news_title, $news_content, $news_author, $photo_id, $news_id){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("UPDATE vr21_news SET vr21_news_photo_id=?, vr21_news_news_title=?, vr21_news_news_content=?, vr21_news_news_author=? WHERE vr21_news_id=?");
		echo $conn -> error;
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("isssi", $photo_id, $news_title, $news_content, $news_author, $news_id);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
		$GLOBALS["news_input_error"] = null;
		$GLOBALS["news_title"] = null;
		$GLOBALS["news_content"] = null;
		$GLOBALS["news_author"] = null;
	}
	function get_news($news_id) { 
        $photo_folder = "../upload_photos_news/";
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $conn -> set_charset("utf8");
        $stmt = $conn -> prepare("SELECT vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_added, vr21_news_photos.vr21_photos_filename FROM vr21_news LEFT JOIN vr21_news_photos ON vr21_news.vr21_news_photo_id = vr21_news_photos.vr21_photos_photoid WHERE vr21_news_id = ?");
        echo $conn -> error;
        $stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db, $news_added_from_db, $news_photo_name_from_db);
        $stmt -> bind_param("i", $news_id);
        $stmt -> execute();
        $raw_news_html = null;
        while ($stmt -> fetch()) {
            $raw_news_html .= "\n <label for='news_title_input'>Uudise pealkiri</label>";
            $raw_news_html .= "\n <br> <input type='text' id='news_title_input' name='news_title_input' placeholder='Pealkiri' value='" .$news_title_from_db ."'>";     
            $raw_news_html .= "\n <br> <label for='news_content_input'>Uudise tekst</label>";
            $raw_news_html .= "\n <br> <textarea id='news_content_input' name='news_content_input' placeholder='Uudise tekst' rows='6' cols='40'>" .$news_content_from_db ."</textarea>";
            $raw_news_html .= "\n <br> <label for='news_author_input'>Uudise lisaja nimi</label>";
            $raw_news_html .= "\n <br> <input type='text' id='news_author_input' name='news_author_input' placeholder='Nimi' value='" .$news_author_from_db ."'>";
            //pildi kontroll
            if(!empty($news_photo_name_from_db)) {
                    $raw_news_html .= "\n <br> <label for='file_input'>Uudise pildina hetkel kasutusel: " .$news_photo_name_from_db;
					$raw_news_html .= "\n <br> <img src=" .$photo_folder .$news_photo_name_from_db ." width='100' height='100'" .">";
                    $raw_news_html .= "\n <br> Muuda uudise pilti: </label>";
                }
            else { // kui foto puudu
                $raw_news_html .= "\n <br> <label for='file_input'>Uudisel hetkel pilt puudub. <br> Lisa uudisele pilt: </label>";
            }
        }
        $stmt->close();
        $conn->close();
        return $raw_news_html; 
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
	<h1>Uudise muutmine</h1>
	<p>See leht on valminud õppetöö raames!</p>

	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
    <p><?php echo $news_content_html; ?></p>
        <input id="file_input" name="file_input" type="file">
        <br>
        <br>
        <input type="submit" name="news_submit" value="Muuda uudist!">
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