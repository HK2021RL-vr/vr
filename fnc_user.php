<?php

    function sign_up($name, $surname, $gender, $birth_date, $email, $password) {
        $notice = 0;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);  //andmebaasiga ühenduse loomine
        $stmt = $conn->prepare("INSERT INTO vr21_users (vr21_users_firstname, vr21_users_lastname, vr21_users_birthdate, vr21_users_gender, vr21_users_email, vr21_users_password) VALUES (?,?,?,?,?,?)");  //küsimärkide arv vastab väljade arvuga
        echo $conn->error;
        //Krüpteerime parooli   cost-mitu korda käiakse läbi, salt-lisatakse räsile juurde erinevaid väärtusi, et muutuks lahtimuukimine raskemaks
        //$options = ["cost" => 12, "salt" => substr(sha1(rand()), 0, 22)];
        $options = ["cost" => 12];
        $pwd_hash = password_hash($password, PASSWORD_BCRYPT, $options);

        $stmt -> bind_param("sssiss", $name, $surname, $birth_date, $gender, $email, $pwd_hash); //jälgida tuleb INSERT käsku andmete sisestamiseks

        if ($stmt -> execute()) {
            $notice = 1;
        }
        $stmt -> close();   // ühenduse lõpetamine 
		$conn -> close();   // ühenduse lõpetamine
        return $notice;
    }

    function sign_in ($email, $password) {
        $notice = 0; 
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $stmt = $conn -> prepare("SELECT vr21_users_id, vr21_users_firstname, vr21_users_lastname, vr21_users_password FROM vr21_users WHERE  vr21_users_email = ?");
        echo $conn -> error;
        $stmt -> bind_param("s", $email);
        $stmt -> bind_result($id_from_db, $first_name_from_db, $last_name_from_db, $password_from_db);
        $stmt -> execute();
        //kui leiti
        if($stmt -> fetch()) {
            //kas parool klapib ja võrdleme
            if (password_verify($password, $password_from_db)) {
                //olemegi sisseloginud
                $notice = 1;
                $_SESSION["user_id"] = $id_from_db;
				$_SESSION["user_first_name"] = $first_name_from_db;
				$_SESSION["user_last_name"] = $last_name_from_db;
                $stmt -> close();
                $conn -> close();
                header("Location: home.php");
                exit();
            }
        }

        $stmt -> close();
		$conn -> close(); //andmebaasist välja logimine kinni
        return $notice;

    }
	function verify_user($email){
		echo $email;
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn -> prepare("SELECT vr21_users_id FROM vr21_users  WHERE vr21_users_email = ?");
		
		echo $conn -> error;
		$stmt -> bind_param("s", $email);
		$stmt -> execute();

		if($stmt -> fetch()){
			$notice = 1;
		}
		
		$stmt -> close();
		$conn -> close();
		return $notice;
	}
    function insert_pic_db($pic_name,$pic_orig_name,$alt_text,$pic_privacy){
            $notice = 0;
            $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
            $stmt = $conn->prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?,?,?,?,?)");
            echo $conn->error;
    
            $stmt -> bind_param("isssi", $_SESSION["user_id"], $pic_name, $pic_orig_name, $alt_text, $pic_privacy);
            
            if($stmt -> execute()){
                $notice = 1;
            }
            $stmt -> close();
            $conn -> close();
            return $notice;
        }

        function resize_image($temp_image, $image_max_w, $image_max_h, $keep_ratio) {
            $image_w = imagesx($temp_image);
            $image_h = imagesy($temp_image);
            
            if ($keep_ratio){ 
                if($image_w / $image_max_w > $image_h / $image_max_h){
                    $image_size_ratio = $image_w / $image_max_w;
                } else {
                    $image_size_ratio = $image_h / $image_max_h;
                }
                $image_new_w = round($image_w / $image_size_ratio);
                $image_new_h = round($image_h / $image_size_ratio);
    
                $new_temp_image = imagecreatetruecolor($image_new_w, $image_new_h);
                imagecopyresampled($new_temp_image, $temp_image, 0, 0, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);
    
            } else {
                if($image_h<$image_w){
                    // Landscape picture
                    $src_x = ($image_w - $image_h) /2;
                    $src_width = $image_h;
                    $src_y = 0;
                    $src_height = $image_h;
    
                
                } else {
                    // Portrait picture
                    $src_x = 0;
                    $src_width = $image_h;
                    $src_y = ($image_h - $image_w) /2;
                    $src_height = $image_w;
                }
    
                $image_new_w = $image_max_w;
                $image_new_h = $image_max_h;
            
                $new_temp_image = imagecreatetruecolor($image_new_w, $image_new_h);
                imagecopyresampled($new_temp_image, $temp_image, 0, 0, $src_x, $src_y, $image_new_w, $image_new_h, $src_width, $src_height);
            }
    
            return $new_temp_image;
    
        }