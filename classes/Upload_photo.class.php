<?php
    class Upload_photo {
        // saadame pildi, failitüübi, võib saata ka vastava katalooginime, muud infot
        public $photo_to_upload; // see on $_FILES["file_input"];
        public $image_file_type;
        public $allowed_image_types;
        public $type_names;
        private $temp_image;
        public $new_temp_image;
        public $check;
        public $notice = null;
        public $image_file_name;
        public $original_date;
        public $target_file;
        var $exif;

        function __construct($photo_to_upload, $size_limit, $allowed_image_types, $type_names) {
            $this->photo_to_upload = $photo_to_upload;

            //kas on üldse pilt
            $this->check = getimagesize($this->photo_to_upload["tmp_name"]);
            if ($this->check !== false ) {
                // kas on aktsepteeritud failivorming ja fikseerime laiendi
                if ($this->check["mime"] == $allowed_image_types[0]) {
                    $this->image_file_type = $type_names[0];
                } elseif ($this->check["mime"] == $allowed_image_types[1]) {
                    $this->image_file_type = $type_names[1];
                } else {
                  $this->notice = "Pole sobiv formaat! Lubatud on jpg või png!";
                }
              } else {
                $this->notice = "Tegu pole pildifailiga!";
              }

            // ega pole liiga suur fail
                if ($this->photo_to_upload["size"] > $size_limit) {
                    $this->notice .= "Valitud fail on liiga suur – lubatud kuni 1MB!";
                }
                
                //teen sisestatud failist pikslikogumi:
                if ($this->notice !== null) {
                    return [$this->notice, $this->image_file_type];
                } else {
                    $this->temp_image = $this->create_image_from_file($this->photo_to_upload["tmp_name"], $this->image_file_type);
                    return [$this->notice, $this->image_file_type];
                }
        }


        //destruktori kustutame serveri mälust temp_image ja new_temp_image
        function __destruct() {
            if(isset($this->new_temp_image)) {
                @imagedestroy($this->new_temp_image);
            }
            if(isset($this->temp_image)) {
                @imagedestroy($this->temp_image);
            }
        }

        //teen pikslikogumi tegemise funktsiooni:
        private function create_image_from_file($image, $image_file_type) {
            $temp_image = null;
            if ($image_file_type == "jpg") {
                $temp_image = imagecreatefromjpeg($image); // The imagecreatefromjpeg() function is used to create a new image from JPEG file or URL.
            }
            if ($image_file_type == "png") {
                $temp_image = imagecreatefrompng($image);
            }
            return $temp_image; // $this->temp_image saab $temp_image-ks
        }

        //loon failinime
        public function create_image_name($prefix, $alias_name){
            $timestamp = microtime(1) * 10000; //microtime(return_float) – when set to TRUE it specifies that the function should return a float, instead of a string
            if ($alias_name !== null) {
                $this->image_file_name = $prefix .$alias_name ."." .$this->image_file_type;
            } else {
                $this->image_file_name = $prefix .$timestamp ."." .$this->image_file_type;
            }
            return $this->image_file_name;
        }

        //funktsioon resize photo tuleb siia: ÕPPEJÕU FUNKTSIOONIST:
        public function resize_photo($w, $h, $keep_orig_proportion = true){
            $image_w = imagesx($this->temp_image);
            $image_h = imagesy($this->temp_image);
            $new_w = $w;
            $new_h = $h;
            $cut_x = 0;
            $cut_y = 0;
            $cut_size_w = $image_w;
            $cut_size_h = $image_h;
            
            if($w == $h){
                if($image_w > $image_h){
                    $cut_size_w = $image_h;
                    $cut_x = round(($image_w - $cut_size_w) / 2);
                } else {
                    $cut_size_h = $image_w;
                    $cut_y = round(($image_h - $cut_size_h) / 2);
                }	
            } elseif($keep_orig_proportion){//kui tuleb originaaproportsioone säilitada
                if($image_w / $w > $image_h / $h){
                    $new_h = round($image_h / ($image_w / $w));
                } else {
                    $new_w = round($image_w / ($image_h / $h));
                }
            } else { //kui on vaja kindlasti etteantud suurust, ehk pisut ka kärpida
                if($image_w / $w < $image_h / $h){
                    $cut_size_h = round($image_w / $w * $h);
                    $cut_y = round(($image_h - $cut_size_h) / 2);
                } else {
                    $cut_size_w = round($image_h / $h * $w);
                    $cut_x = round(($image_w - $cut_size_w) / 2);
                }
            }
            
            //loome uue ajutise pildiobjekti
            $this->new_temp_image = imagecreatetruecolor($new_w, $new_h);
            //kui on läbipaistvusega png pildid, siis on vaja säilitada läbipaistvusega
            imagesavealpha($this->new_temp_image, true);
            $trans_color = imagecolorallocatealpha($this->new_temp_image, 0, 0, 0, 127);
            imagefill($this->new_temp_image, 0, 0, $trans_color);
            imagecopyresampled($this->new_temp_image, $this->temp_image, 0, 0, $cut_x, $cut_y, $new_w, $new_h, $cut_size_w, $cut_size_h);
        }

        public function save_image_to_file($target){
            $notice = null;
            if($this->image_file_type == "jpg"){
                if(imagejpeg($this->new_temp_image, $target, 90)){
                    $notice = 1;
                } else {
                    $notice = 0;
                }
            }
            if($this->image_file_type == "png"){
                if(imagepng($this->new_temp_image, $target, 6)){
                    $notice = 1;
                } else {
                    $notice = 0;
                }
            }
            imagedestroy($this->new_temp_image); //kustutan ajutise pildiobjekti
            return $notice;
        }

        //salvestan originaalpildi
        public function save_orig_photo() {
            $target_file = "../upload_photos_orig/" .$this->image_file_name;
            //if(file_exists($target_file))
                if(move_uploaded_file($this->photo_to_upload["tmp_name"], $target_file)){
                    return null;
                } else {
                    $this->notice .= " Originaalfoto üleslaadimine ebaõnnestus!";
                }
        }

        public function add_watermark($watermark) {
            $watermark_file_type = strtolower(pathinfo($watermark, PATHINFO_EXTENSION));
            $watermark_image = $this->create_image_from_file($watermark, $watermark_file_type); //sellest saan tagasi pikslikogumi)
            $watermark_w = imagesx($watermark_image);
            $watermark_h = imagesy($watermark_image);
            //panen vesimärgi alla paremale nurka:
            $watermark_x = imagesx($this->new_temp_image) - $watermark_w - 10; //10px jätan servast vaba ruumi
            $watermark_y = imagesy($this->new_temp_image) - $watermark_h - 10;
            imagecopy($this->new_temp_image, $watermark_image, $watermark_x, $watermark_y, 0, 0, $watermark_w, $watermark_h);
            imagedestroy($watermark_image);

        }

        public function store_photo_data($image_file_name, $alt, $privacy, $orig_name){
            $notice = null;
            $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
            $stmt = $conn->prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_alttext, vr21_photos_privacy, vr21_photos_origname) VALUES (?, ?, ?, ?, ?)");
            echo $conn->error;
			$conn -> set_charset("utf8");
            $stmt->bind_param("issis", $_SESSION["user_id"], $image_file_name, $alt, $privacy, $orig_name);
            if($stmt->execute()){
              $notice = 1;
            } else {
              $notice = $stmt->error;
            }
            
            $stmt->close();
            $conn->close();
            return $notice;
        }
        
    } 
    //class lõppeb