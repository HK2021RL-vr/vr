<?php
    require_once "usersession.php";
    $id = $_REQUEST["photoid"]; //tuleb modal.js failist AJAX
    $raiting = $_REQUEST["raiting"];

    require_once "../../../conf.php";

    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

    $stmt = $conn->prepare("INSERT INTO vr_21photoraitings (vr_21_photoratings_photoid, vr21_photoratings_userid, vr21_photoratings_rating) VALUES(?,?,?)");
    echo $conn->error;
    $stmt->bind_param("iii", $id, $_SESSION["user_id"], $raiting);
    $stmt->execute();
    $stmt->close();

    //loeme keskmise hinde

    $stmt = $conn->prepare("SELECT AVG(vr21_photoraitings_raitin) as avgValue FROM vr_21photoraitings WHERE vr_21_photoraitings_photoid = ?p" );
    $stmt->bind_param("i", $id);
    $stmt->bind_result ($score);
    $stmt->exicute();
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    echo round ($score, 2);
