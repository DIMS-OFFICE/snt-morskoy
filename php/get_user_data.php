<?php
	$dir=realpath(dirname(__FILE__)."/..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

   if ($s->rowCount()==0) {
        $res=Array(
            "result" => "error",
            "desc" => "SESSION_ERROR"
        );

        echo json_encode($res);

        exit();
    }

    if ($_POST["user_id"]==false) {//Если запрос из личного кабинета
        $user_id=$s->fetch(PDO::FETCH_COLUMN);
    } else {//Если запрос из админки
        $user_id=$_POST["user_id"];
    }

    $s=$db->prepare("SELECT id, area_name FROM 4232_base.areas WHERE user_id=:user_id");
    $s->bindValue(":user_id", $user_id);

    $s->execute();

    if ($s->rowCount()>0) {
        $areas=$s->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $areas=Array();
    }

    $s=$db->prepare("SELECT MAX(id) FROM 4232_base.areas");
    $s->bindValue(":user_id", $user_id);

    $s->execute();

    $max_area_id=(int)$s->fetch(PDO::FETCH_COLUMN);

    $s=$db->prepare("SELECT name, sirname, middle_name, birth_date, passport_seria, passport_number, reg_address, email, passport1, passport2 FROM 4232_base.logins WHERE id=:user_id");

    $s->bindValue(":user_id", $user_id);

    $s->execute();

    $user_data=$s->fetch(PDO::FETCH_ASSOC);

    $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed='Паспорт' ORDER BY date_time DESC LIMIT 1");
    $s->bindValue(":user_id", $user_id);
    $s->execute();

    if ($s->rowCount()>0) {
        $passport_status=$s->fetch(PDO::FETCH_ASSOC);
    } else {
        if ($user_data["passport_number"]=="" || $user_data["passport_seria"]=="") {
            $passport_status["status"]=0;
        } else {
            $passport_status["status"]=1;
        }
        $passport_status["comment"]="";
    }

    $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed='Регистрация' ORDER BY date_time DESC LIMIT 1");
    $s->bindValue(":user_id", $user_id);
    $s->execute();

    if ($s->rowCount()>0) {
        $reg_address_status=$s->fetch(PDO::FETCH_ASSOC);
    } else {
        if ($user_data["reg_address"]=="") {
            $reg_address_status["status"]=0;
        } else {
            $reg_address_status["status"]=1;
        }
        $reg_address_status["comment"]="";
    }

    $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed='e-mail' ORDER BY date_time DESC LIMIT 1");
    $s->bindValue(":user_id", $user_id);
    $s->execute();

    if ($s->rowCount()>0) {
        $email_status=$s->fetch(PDO::FETCH_ASSOC);
    } else {
        if ($user_data["email"]=="") {
            $email_status["status"]=0;
        } else {
            $email_status["status"]=1;
        }
        $email_status["comment"]="";
    }

    $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed='Скан паспорта 1' ORDER BY date_time DESC LIMIT 1");
    $s->bindValue(":user_id", $user_id);
    $s->execute();

    if ($s->rowCount()>0) {
        $passport1_img_status=$s->fetch(PDO::FETCH_ASSOC);
    } else {
        if ($user_data["passport1"]=="") {
            $passport1_img_status["status"]=0;
        } else {
            $passport1_img_status["status"]=1;
        }
        $passport1_img_status["comment"]="";
    }

    $s=$db->prepare("SELECT status, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed='Скан паспорта 2' ORDER BY date_time DESC LIMIT 1");
    $s->bindValue(":user_id", $user_id);
    $s->execute();

    if ($s->rowCount()>0) {
        $passport2_img_status=$s->fetch(PDO::FETCH_ASSOC);
    } else {
        if ($user_data["passport2"]=="") {
            $passport2_img_status["status"]=0;
        } else {
            $passport2_img_status["status"]=1;
        }
        $passport2_img_status["comment"]="";
    }

    $res=Array(
        "result" => "OK",
    	"user_data" => $user_data,
    	"areas" => $areas,
        "max_area_id" => $max_area_id,
        "passport_status" => $passport_status["status"],
        "passport_comment" => $passport_status["comment"],
        "reg_address_status" => $reg_address_status["status"],
        "reg_address_comment" => $reg_address_status["comment"],
        "email_status" => $email_status["status"],
        "email_comment" => $email_status["comment"],
        "passport1_img_status" => $passport1_img_status["status"],
        "passport1_img_comment" => $passport1_img_status["comment"],
        "passport2_img_status" => $passport2_img_status["status"],
        "passport2_img_comment" => $passport2_img_status["comment"]        
    );

    echo json_encode($res);
?>