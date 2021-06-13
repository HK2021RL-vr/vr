<?php
	require_once "usesession.php";
	require_once "../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_upload_photo.php";
	require_once "classes/Upload_photo.class.php";
	
$photo_upload_error = null;
$image_file_type = null;
//$image_file_name_prefix = "vr_";//$allowed_image_types = ['image/jpeg', 'image/png'];//$type_names = ['jpg', 'png'];//$alias_name = null;
$image_file_name = null;
$file_size_limit = 1*1024*1024;
$image_max_w = 600;
$image_max_h = 400;
$image_thumbnail_size = 100;
$notice = null;
$watermark = "../images/vr_watermark.png";
//$notice_photo = null;//$save_original_photo = true;

if(isset($_POST["photo_submit"])) {
  // "tmp_name" kasutame ajutiseks muutujaks.

  //algab Upload_photo klassi
  $photo_upload = new Upload_photo($_FILES["file_input"], $file_size_limit);

  $photo_upload_error .= $photo_upload->photo_upload_error;
  //$image_file_type = $photo_upload->image_file_type;

  if (empty($photo_upload->$photo_upload_error)) {
   // $image_file_name = $photo_upload->create_image_name($image_file_name_prefix, $alias_name);

    $photo_upload->resize_photo($image_max_w, $image_max_h);

   
    $photo_upload->add_watermark($watermark);
/*rida30
    //salvestame pikslikogumi faili
    //$target_file = "../upload_photos_normal/" .$image_file_name;
    $result = $photo_upload->save_image_to_file($target_file, $image_file_type);
    if($result == 1) {
      $notice = "Vähendatud pilt laeti üles! ";
    } else {
      $photo_upload_error = "Vähendatud pildi salvestamisel tekkis viga!";
    }

    $photo_upload->resize_photo($image_thumbnail_size, $image_thumbnail_size, false);

    $target_file = "../upload_photos_thumbnail/" .$image_file_name;
    $result = $photo_upload->save_image_to_file($target_file);
    if($result == 1) {
      $notice .= " Pisipilt laeti üles! ";
    } else {
      $photo_upload_error .= " Pisipildi salvestamisel tekkis viga!";
    }
    */
    $image_file_name = $photo_upload->generate_filename();
		$target_file = "../upload_photos_normal/" .$image_file_name;
		$result = $photo_upload->save_image_to_file($target_file, false);
		if($result == 1) {
			$notice = "Vähendatud pilt laeti üles! ";
		} else {
			$photo_upload_error = "Vähendatud pildi salvestamisel tekkis viga!";
		}

//teen pisipildi
$photo_upload->resize_photo($image_thumbnail_size, $image_thumbnail_size, false);
		
//salvestame pisipildi faili
$target_file = "../upload_photos_thumbnail/" .$image_file_name;
$result = $photo_upload->save_image_to_file($target_file, false);
if($result == 1) {
  $notice .= " Pisipilt laeti üles! ";
} else {
  $photo_upload_error .= " Pisipildi salvestamisel tekkis viga!";
}

/*
    //orininaali salvestamine
    if ($save_original_photo) {
      $photo_upload->save_orig_photo();
    }

  }

  //kõik ok, salvestus andmebaasi
  if($photo_upload_error == null){
    $result = $photo_upload->store_photo_data($image_file_name, $_POST["alt_input"], $_POST["privacy_input"], $_FILES["file_input"]["name"]);
    if($result == 1){
      $notice .= " Pildi andmed lisati andmebaasi!";
    } else {
      $photo_upload_error = "Pildi andmete lisamisel andmebaasi tekkis tehniline tõrge: " .$result;
    }
  }

  unset ($photo_upload);
}
*/
// originaal faili puhul kasutan näitena orginaal nime
$target_file = "../upload_photos_orig/" .$_FILES["file_input"]["name"];
$result = $photo_upload->save_image_to_file($target_file, true);
if($result == 1){
  $notice .= " Originaalfoto üleslaadimine õnnestus!";
} else {
  $photo_upload_error .= " Originaalfoto üleslaadimine ebaõnnestus!";
}

$photo_upload_error = $photo_upload->photo_upload_error;
unset($photo_upload);
//kui kõik hästi, salvestame info andmebaasi!!!
if($photo_upload_error == null){
  $result = store_photo_data($image_file_name, $_POST["alt_text"], $_POST["privacy_input"], $_FILES["file_input"]["name"]);
  if($result == 1){
    $notice .= " Pildi andmed lisati andmebaasi!";
  } else {
    $photo_upload_error = "Pildi andmete lisamisel andmebaasi tekkis tehniline tõrge: " .$result;
  }
}

}
}


?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
  <script src="javascript/checkImageSize.js" defer></script>
	<link rel="stylesheet" href="stiil.css">
</head>
<body>
<div class="container">
	<h1>Fotode üleslaadimine</h1>
	<p>See leht on valminud õppetöö raames!</p>
<!--  <p> Kasutaja: <?php echo $_SESSION["vr21_users_firstname"] . ' ' .$_SESSION["vr21_users_lastname"]; ?> </p><p><a href="?logout=1">Logi välja</a></p>-->
	
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto fail! </label>
		<input id="file_input" name="file_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
		<br>
		<label>Privaatsustase: </label>
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
		<label for="privacy_input_1">Privaatne</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2">
		<label for="privacy_input_2">Registreeritud kasutajatele</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="1">
		<label for="privacy_input_3">Avalik</label>
		<br>
		<input type="submit" id="js_photo_submit" name="photo_submit" value="Lae pilt üles!">
	</form>
  
	<hr>
  <p><a href="home.php">Avalehele</a></p>
  <p><a href="add_news.php">Uudiste lisamine</a></p>
  <p><a href="show_news.php">Uudiste lugemine</a></p>
  <p><a href="galerii.php">Galerii</a></p>
	<p><a href="?logout=1">Logi välja</a></p>
	<hr>
	
</div>
</body>
</html>