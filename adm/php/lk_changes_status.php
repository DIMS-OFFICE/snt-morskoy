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
    }

    if ($_POST["action"]=="accept") {
        $status=1;
        $_POST["comment"]="";
    } else {
        $status=2;
    }

    $s=$db->prepare("UPDATE 4232_base.lk_changes SET status=:status, comment=:comment WHERE id=:change_id");
    $s->bindValue(":status", $status);
    $s->bindValue(":comment", $_POST["comment"]);
    $s->bindValue(":change_id", $_POST["change_id"]);

    $s->execute();

    if ($s->rowCount()>0) {
        $res=Array(
            "result" => $result,
            "status" => "OK"
        );
    } else {
        $res=Array(
            "result" => $result,
            "status" => "ERROR"
        );
    }

    echo json_encode($res);
?>