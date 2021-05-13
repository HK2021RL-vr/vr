<?php
	$my_name = "Renida Lumiste";
	$current_time = date("d.m.Y H:i:s");
	$time_html = "\n <p>Leht avanes: " .$current_time .".</p> \n";
	$semester_begin = new DateTime("2021-1-25");
	$semester_end = new DateTime("2021-6-30");
	$semester_duration = $semester_begin-> diff($semester_end);
	$semester_duration_days = $semester_duration->format("%r%a");
	$semester_dur_html = "\n <p> 2021 kevadsemestri kestus on " .$semester_duration_days ." päeva.</p> \n";

	// Semestri kulgemine ja kontrolli lisamine ÜL 2
	$today = new DateTime("now");
	$today_set = date_create();
	$today_set->setDate(2021, 1, 1);					//Lisame kuupäeva mida soovime kontrollida

	$from_semester_begin = $semester_begin->diff($today);
	$from_semester_begin_days = $from_semester_begin->format("%r%a");

	if($from_semester_begin_days <= $semester_duration_days) {
		$semester_progress_now = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semester_duration_days 
		.'" value="' .$from_semester_begin_days .'"></meter>.</p>' ."\n";} 						//progressiriba
		else {

			$semester_progress_now = "\n <p>Semester on lõppenud! </p> \n";
			}
		
	// Sisestatud kuupäevast alates kontrollimine.

	$from_semester_begin = $semester_begin->diff($today_set);
	$from_semester_begin_days = $from_semester_begin->format("%r%a");

	if($from_semester_begin_days <= $semester_duration_days && $from_semester_begin_days >=0) {
		$semester_progress = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semester_duration_days 
		.'" value="' .$from_semester_begin_days .'"></meter>.</p>' ."\n";} 						//progressiriba
		else {
			if ($from_semester_begin_days <0){
				$semester_progress = "\n <p>Semester pole veel alanud!</p>";
			}
			else {
			$semester_progress = "\n <p>Semester on lõppenud! </p> \n";
			}
		}
	 //loeme piltide kataloogi sisu
	$picsdir = "../pics/";
	$all_files = array_slice(scandir($picsdir), 2); 		//viska ära esimesest otsast elemente, hetkel oli neid 2
	
	// echo $all_files[5];
	// var_dump($all_files); //kogu info väljastamine
 	$allow_photo_types = ["image/jpeg", "image/png"]; 		// sorteerime , et oleks ainult pildid
	$pic_files = [];
	// kontrollime kas kuulub lubatud formaati
	//kui tsüklit läbtakse siis iga kord nimetatakse seda file-na
	foreach($all_files as $file) {
		$file_info = getimagesize($picsdir .$file);
		//kas muutujal on väärtus isset
		if(isset($file_info["mime"])) {
			if(in_array($file_info["mime"], $allow_photo_types)) {
				array_push($pic_files, $file);
			}
		}
	}

	// $photo_count = count($pic_files);							// juhusliku foto leidmine
	// $photo_num = mt_rand(0, $photo_count-1); 					//mt on kiirem
	// $random_photo = $pic_files[$photo_num];

	// ÜL 1 kolme erineva pildi lisamine
	$random_photo = array_rand($pic_files, 3);

	// ÜL 3 nädalapäeva lisamine:
	$weekday_number = date('w');
	$weekday = ['Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede', 'Laupäev', 'Pühapäev'];
	
	$today_weekday_html = "\n <p> Täna on " .$weekday[$weekday_number-1] .".</p>";


?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="UTF-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>
	<?php
		echo $my_name;
	?>
	</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<?php
		echo $time_html;
		echo $semester_dur_html;
		echo $semester_progress_now;
		echo $semester_progress;
		echo $today_weekday_html;
	?>

<!-- ÜL 1 kolme pildi lisamine-->
	<img width="300px" src="<?php echo $picsdir .$pic_files[$random_photo[0]] ?>" alt="vaade Haapsalus">
	<img width="300px" src="<?php echo $picsdir .$pic_files[$random_photo[1]] ?>" alt="vaade Haapsalus">
	<img width="300px" src="<?php echo $picsdir .$pic_files[$random_photo[2]] ?>" alt="vaade Haapsalus">
	<!--<img src="../pics/IMG_0177.JPG" alt="vaade Haapsalus">>	-->
</body>
</html>