<?php
    $dir=realpath(dirname(__FILE__) . '/../..');

	require($dir."/php/pdo_db_connect.php");

 	$s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    if ($s->rowCount()>0) {
    	$result="AUTHORIZED";
    } else {
    	$result="NOT_AUTH";

        $res=Array(
            "result" => $result
        );

        echo json_encode($res);

        exit();
    }

    if ($_POST["value"]==10) {
        $s=$db->prepare("UPDATE 4232_base.logins SET account_type=:account_type WHERE id=:user_id");
        $s->bindValue(":account_type", $_POST["account_type"]);
        $s->bindValue(":user_id", $_POST["user_id"]);

        $s->execute();

        if ($s->rowCount()>0) {
            $res=Array(
                "result" => $result,
                "status" => "Сохранено"
            );
        } else {
            $res=Array(
                "result" => $result,
                "status" => "НЕ сохранено"
            );
        }

        echo json_encode($res);
    } else {
        $s=$db->prepare("UPDATE 4232_base.logins SET active=:active WHERE id=:user_id");
        $s->bindValue(":active", $_POST["value"]);
        $s->bindValue(":user_id", $_POST["user_id"]);

        $s->execute();

        if ($s->rowCount()>0) {
            $res=Array(
                "result" => $result,
                "status" => "Сохранено"
            );
        } else {
            $res=Array(
                "result" => $result,
                "status" => "НЕ сохранено"
            );
        }

        echo json_encode($res);
    }
?>