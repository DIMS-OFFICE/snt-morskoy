<?php
    $dir=realpath(dirname(__FILE__)."/..");

    require($dir."/php/pdo_db_connect.php");

    if (isset($_POST['btnSubmit'])) {
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

        $user_id=$s->fetch(PDO::FETCH_COLUMN);

        for ($i=0; $i<$_POST["files_count"]; $i++) {
            $parts = explode(".", $_FILES["uploadImages"]["name"][$i]);

            $ext=$parts[count($parts)-1];

            if ($_POST["action"]=="documents") {

                $folderPath = $dir."/documents/";

                $file=$folderPath . $_FILES["uploadImages"]["name"][$i];

            } else if ($_POST["action"]=="counter_doc") {

                $folderPath = $dir."/users_files/".$user_id."/payment_docs";

                if (file_exists($folderPath)==false) {
                    mkdir($folderPath, 0777, true);
                }

                $file=$folderPath . "/" . $_POST["area_id"]."-".$_POST["params"];

            } else if ($_POST["action"]=="passport1" || $_POST["action"]=="passport2") {

                $folderPath = $dir."/users_files/".$user_id;

                if (file_exists($folderPath)==false) {
                    mkdir($folderPath, 0777, true);
                }

                $file=$folderPath . "/" . $_POST["action"].".".$ext;

            } else if ($_POST["action"]=="sobstvennost" || $_POST["action"]=="egrn") {

                $folderPath = $dir."/users_files/".$user_id."/areas/".$_POST["area_id"];

                if (file_exists($folderPath)==false) {
                    mkdir($folderPath, 0777, true);
                }

                $file=$folderPath . "/" . $_POST["action"].".".$ext;
            }

            if (! is_writable($folderPath) || ! is_dir($folderPath)) {
                $res=Array(
                    "result" => "error",
                    "desc" => "Директория недоступна для записи"
                );

                echo json_encode($res);

                exit();
            }

            if (move_uploaded_file($_FILES["uploadImages"]["tmp_name"][$i], $file)) {
                
            }

            $s_change_sel=$db->prepare("SELECT id FROM 4232_base.lk_changes WHERE user_id=:user_id AND changed=:changed");
            $s_change_del=$db->prepare("DELETE FROM 4232_base.lk_changes WHERE id=:id");
            $s_change=$db->prepare("INSERT INTO 4232_base.lk_changes (user_id, area_id, changed, value, date_time) VALUES (:user_id, :area_id, :changed, :value, NOW())");

            if ($_POST["action"]=="documents") {
                $s=$db->prepare("INSERT INTO 4232_base.documents (user_id, file_name_rus, `groups`, date_time) VALUES (:user_id, :file_name_rus, :groups, :date_time)");
                $s->bindValue(":user_id", $user_id);
                $s->bindValue(":file_name_rus", $_FILES["uploadImages"]["name"][$i]);
                $s->bindValue(":groups", $_FILES["uploadImages"]["name"][$i]);
                $s->bindValue(":date_time", date("Y-m-d H:i:s"));

                $s->execute();

                if ($i==$_POST["files_count"]-1) {//Сообщение на почту только после загрузки последнего файла
                    send_mail("Новый документ");
                }
            } else if ($_POST["action"]=="passport1" || $_POST["action"]=="passport2") {
                $s=$db->prepare("UPDATE 4232_base.logins SET ".$_POST["action"]."=:img WHERE id=:user_id");
                $s->bindValue(":user_id", $user_id);
                $s->bindValue(":img", $user_id."/".$_POST["action"].".".$ext);
                $s->execute();

                $res=Array(
                    "result" => "OK",
                    "file" => $user_id."/".$_POST["action"].".".$ext
                );

                if ($_POST["action"]=="passport1") {
                    $s_change_sel->bindValue(":user_id", $user_id);
                    $s_change_sel->bindValue(":changed", "Скан паспорта 1");        
                    $s_change_sel->execute();

                    if ($s_change_sel->rowCount()>0) {
                        $change_id=$s_change_sel->fetch(PDO::FETCH_COLUMN);

                        $s_change_del->bindValue(":id", $change_id);
                        $s_change_del->execute();
                    }

                    $s_change->bindValue(":user_id", $user_id);
                    $s_change->bindValue(":area_id", 0);
                    $s_change->bindValue(":changed", "Скан паспорта 1");
                    $s_change->bindValue(":value", "<span class='foto_prev' path='users_files/".$user_id."/".$_POST["action"].".".$ext."'>Документ</span>");
                } else {
                    $s_change_sel->bindValue(":user_id", $user_id);
                    $s_change_sel->bindValue(":changed", "Скан паспорта 2");        
                    $s_change_sel->execute();

                    if ($s_change_sel->rowCount()>0) {
                        $change_id=$s_change_sel->fetch(PDO::FETCH_COLUMN);

                        $s_change_del->bindValue(":id", $change_id);
                        $s_change_del->execute();
                    }

                    $s_change->bindValue(":user_id", $user_id);
                    $s_change->bindValue(":area_id", 0);
                    $s_change->bindValue(":changed", "Скан паспорта 2");
                    $s_change->bindValue(":value", "<span class='foto_prev' path='users_files/".$user_id."/".$_POST["action"].".".$ext."'>Документ</span>");
                }

                $s_change->execute();

                send_mail("Изменение личных данных"); 
            } else if ($_POST["action"]=="sobstvennost" || $_POST["action"]=="egrn") {
                $s=$db->prepare("UPDATE 4232_base.areas SET ".$_POST["action"]."=:img WHERE id=:area_id");
                $s->bindValue(":area_id", $_POST["area_id"]);
                $s->bindValue(":img", $user_id."/areas/".$_POST["area_id"]."/".$_POST["action"].".".$ext);
                $s->execute();

                $res=Array(
                    "result" => "OK",
                    "file" => $user_id."/areas/".$_POST["area_id"]."/".$_POST["action"].".".$ext
                );

                if ($_POST["action"]=="sobstvennost") {
                    $changed="Собственность";
                } else {
                    $changed="ЕГРН";
                }

                $s_change_sel->bindValue(":user_id", $user_id);
                $s_change_sel->bindValue(":changed", $changed);        
                $s_change_sel->execute();

                if ($s_change_sel->rowCount()>0) {
                    $change_id=$s_change_sel->fetch(PDO::FETCH_COLUMN);

                    $s_change_del->bindValue(":id", $change_id);
                    $s_change_del->execute();
                }

                $s_change->bindValue(":user_id", $user_id);
                $s_change->bindValue(":area_id", $_POST["area_id"]);
                $s_change->bindValue(":changed", $changed);
                $s_change->bindValue(":value", "<span class='foto_prev' path='users_files/".$user_id."/areas/".$_POST["area_id"]."/".$_POST["action"].".".$ext."'>Документ</span>");

                $s_change->execute();

                send_mail("Изменение данных участка");
            }
        }

        $res=Array(
            "result" => "OK"
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