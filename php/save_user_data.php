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

    $changed=false;

    $s_change_sel=$db->prepare("SELECT id FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed=:changed");
    $s_change_del=$db->prepare("DELETE FROM 4232_base.lk_changes WHERE id=:id");
    $s_change=$db->prepare("INSERT INTO 4232_base.lk_changes (user_id, changed, value, date_time) VALUES (:user_id, :changed, :value, NOW())");

    if ($_POST["user_id"]==false) {//Если запрос из личного кабинета
        $s=$db->prepare("UPDATE 4232_base.logins SET passport_seria=:passport_seria, passport_number=:passport_number WHERE id=:id");
    } else {
        $s=$db->prepare("UPDATE 4232_base.logins SET sirname=:sirname, name=:name, middle_name=:middle_name, birth_date=:birth_date, passport_seria=:passport_seria, passport_number=:passport_number WHERE id=:id");
        $s->bindValue(":passport_seria", $_POST["passport_seria"]);
        $s->bindValue(":passport_number", $_POST["passport_number"]);
        $s->bindValue(":sirname", $_POST["sirname"]);
        $s->bindValue(":name", $_POST["name"]);
        $s->bindValue(":middle_name", $_POST["middle_name"]);
        $s->bindValue(":birth_date", $_POST["birth_date"]);                
    }

    $s->bindValue(":id", $user_id);

    $s->execute();

    if ($s->rowCount()>0) {
        $changed=true;

        if ($_POST["user_id"]==false) {//Если запрос из личного кабинета
            $s_change_sel->bindValue(":user_id", $user_id);
            $s_change_sel->bindValue(":changed", "Паспорт");        
            $s_change_sel->execute();

            if ($s_change_sel->rowCount()>0) {
                $change_id=$s_change_sel->fetch(PDO::FETCH_COLUMN);

                $s_change_del->bindValue(":id", $change_id);
                $s_change_del->execute();
            }

            $s_change->bindValue(":user_id", $user_id);
            $s_change->bindValue(":changed", "Паспорт");
            $s_change->bindValue(":value", $_POST["passport_seria"]." ".$_POST["passport_number"]);

            $s_change->execute();
        }
    }

    $s=$db->prepare("UPDATE 4232_base.logins SET reg_address=:reg_address WHERE id=:id");
    $s->bindValue(":reg_address", $_POST["reg_address"]);
    $s->bindValue(":id", $user_id);

    $s->execute();

    if ($s->rowCount()>0) {
        $changed=true;

        if ($_POST["user_id"]==false) {//Если запрос из личного кабинета
            $s_change_sel->bindValue(":user_id", $user_id);
            $s_change_sel->bindValue(":changed", "Регистрация");        
            $s_change_sel->execute();

            if ($s_change_sel->rowCount()>0) {
                $change_id=$s_change_sel->fetch(PDO::FETCH_COLUMN);

                $s_change_del->bindValue(":id", $change_id);
                $s_change_del->execute();
            }

            $s_change->bindValue(":user_id", $user_id);
            $s_change->bindValue(":changed", "Регистрация");
            $s_change->bindValue(":value", $_POST["reg_address"]);

            $s_change->execute();
        }
    }

    $s=$db->prepare("UPDATE 4232_base.logins SET email=:email WHERE id=:id");
    $s->bindValue(":email", $_POST["email"]);
    $s->bindValue(":id", $user_id);

    $s->execute();

    if ($s->rowCount()>0) {
        $changed=true;

        if ($_POST["user_id"]==false) {//Если запрос из личного кабинета
            $s_change_sel->bindValue(":user_id", $user_id);
            $s_change_sel->bindValue(":changed", "e-mail");        
            $s_change_sel->execute();

            if ($s_change_sel->rowCount()>0) {
                $change_id=$s_change_sel->fetch(PDO::FETCH_COLUMN);

                $s_change_del->bindValue(":id", $change_id);
                $s_change_del->execute();
            }
            
            $s_change->bindValue(":user_id", $user_id);
            $s_change->bindValue(":changed", "e-mail");
            $s_change->bindValue(":value", $_POST["email"]);

            $s_change->execute();
        }
    }

    if ($changed=true) {
        if ($_POST["user_id"]==false) {//Если запрос из личного кабинета
            send_mail("Обновление личных данных");
        }

        $res=Array(
            "result" => "OK",
        );
    } else {
        $res=Array(
            "result" => "error",
        );
    }

    echo json_encode($res);

    function send_mail($subject) {
        $to = 'prog@dims.mobi';     
        $message = 'https://snt-morskoy.ru/adm/';
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        $headers.= 'From: SNT-Morskoy'.'\r\n';
        $headers.= 'Reply-To: robot@snt-morskoy.ru';
        //$headers.='Content-type: text/plain; charset=UTF-8'.'\r\n';
        //$headers.='Content-transfer-encoding: quoted-printable';
        
        mail ($to, $subject, $message, $headers);
    }
?>