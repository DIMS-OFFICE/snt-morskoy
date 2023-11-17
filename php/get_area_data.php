<?php
    $dir=realpath(dirname(__FILE__)."/..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    $user_id=$s->fetch(PDO::FETCH_COLUMN);
    
    if ($s->rowCount()==0) {
        $res=Array(
            "result" => "error",
            "desc" => "SESSION_ERROR"
        );

        echo json_encode($res);

        exit();
    }

    $s=$db->prepare("SELECT area_name, area_nom, kadastr, sobstvennost, egrn FROM 4232_base.areas WHERE id=:area_id");
    $s->bindValue(":area_id", $_POST["area_id"]);

    $s->execute();

    if ($s->rowCount()==0) {
        $res=Array(
            "result" => "error",
            "desc" => "AREA_NOT_FOUND"
        );

        echo json_encode($res);

        exit();
    } else {
        $area=$s->fetch(PDO::FETCH_ASSOC);

        $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE area_id=:area_id AND changed='Номер участка' ORDER BY date_time DESC LIMIT 1");
        $s->bindValue(":area_id", $_POST["area_id"]);
        $s->execute();

        if ($s->rowCount()>0) {
            $area_nom_status=$s->fetch(PDO::FETCH_ASSOC);
        } else {
            $area_nom_status["status"]=0;
            $area_nom_status["comment"]="";
        }

        $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE area_id=:area_id AND changed='Название участка' ORDER BY date_time DESC LIMIT 1");
        $s->bindValue(":area_id", $_POST["area_id"]);
        $s->execute();

        if ($s->rowCount()>0) {
            $area_name_status=$s->fetch(PDO::FETCH_ASSOC);
        } else {
            $area_name_status["status"]=0;
            $area_name_status["comment"]="";
        }

        $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE area_id=:area_id AND changed='Кадастровый номер' ORDER BY date_time DESC LIMIT 1");
        $s->bindValue(":area_id", $_POST["area_id"]);
        $s->execute();

        if ($s->rowCount()>0) {
            $kadastr_status=$s->fetch(PDO::FETCH_ASSOC);
        } else {
            $kadastr_status["status"]=0;
            $kadastr_status["comment"]="";
        }

        $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE area_id=:area_id AND changed='Собственность' ORDER BY date_time DESC LIMIT 1");
        $s->bindValue(":area_id", $_POST["area_id"]);
        $s->execute();

        if ($s->rowCount()>0) {
            $sobstvennost_status=$s->fetch(PDO::FETCH_ASSOC);
        } else {
            $sobstvennost_status["status"]=0;
            $sobstvennost_status["comment"]="";
        }

        $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE area_id=:area_id AND changed='ЕГРН' ORDER BY date_time DESC LIMIT 1");
        $s->bindValue(":area_id", $_POST["area_id"]);
        $s->execute();

        if ($s->rowCount()>0) {
            $egrn_status=$s->fetch(PDO::FETCH_ASSOC);
        } else {
            $egrn_status["status"]=0;
            $egrn_status["comment"]="";
        }

        $res=Array(
            "result" => "OK",
            "area_name" => $area["area_name"],
            "area_name_status" => $area_name_status["status"],
            "area_name_comment" => $area_name_status["comment"],
            "area_nom" => $area["area_nom"],
            "area_nom_status" => $area_nom_status["status"],
            "area_nom_comment" => $area_nom_status["comment"],
            "kadastr" => $area["kadastr"],
            "kadastr_status" => $kadastr_status["status"],
            "kadastr_comment" => $kadastr_status["comment"],
            "sobstvennost" => $area["sobstvennost"],
            "sobstvennost_status" => $sobstvennost_status["status"],
            "sobstvennost_comment" => $sobstvennost_status["comment"],
            "egrn" => $area["egrn"],
            "egrn_status" => $egrn_status["status"],
            "egrn_comment" => $egrn_status["comment"],
        );

        echo json_encode($res);

        exit();        
    }
?>