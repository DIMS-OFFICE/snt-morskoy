<?php
	$dir=realpath(dirname(__FILE__) . '/../..');

	require($dir."/php/pdo_db_connect.php");

 	$s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    if ($s->rowCount()==0) {
    	$result=Array(
            "result" => "NOT_AUTH"
        );

        echo json_encode($result);

        exit();
    }

    $ids=explode(",", $_POST["ids"]);

    if ($_POST["action"]=="change_groups") {
        $s=$db->prepare("UPDATE 4232_base.documents SET `groups`=:groups WHERE id=:id");

        $success=0;
        foreach ($ids as $id) {
            $s->bindValue(":id", $id);
            $s->bindValue(":groups", $_POST["param"]);
            $s->execute();

            if ($s->rowCount()>0) {
                $success++;
            }
        }
    } else if ($_POST["action"]=="change_category") {
        $s=$db->prepare("UPDATE 4232_base.documents SET `category`=:category WHERE id=:id");

        $success=0;
        foreach ($ids as $id) {
            $s->bindValue(":id", $id);
            $s->bindValue(":category", $_POST["param"]);
            $s->execute();

            if ($s->rowCount()>0) {
                $success++;
            }
        }
    } else if ($_POST["action"]=="publish") {
        $s=$db->prepare("UPDATE 4232_base.documents SET `active`=1 WHERE id=:id");

        $success=0;
        foreach ($ids as $id) {
            $s->bindValue(":id", $id);
            $s->execute();

            if ($s->rowCount()>0) {
                $success++;
            }
        }
    } else if ($_POST["action"]=="remove") {
        $s_sel=$db->prepare("SELECT file_name_rus FROM 4232_base.documents WHERE id=:id");

        $s_del=$db->prepare("DELETE FROM 4232_base.documents WHERE id=:id");

        $success=0;
        foreach ($ids as $id) {
            $s_sel->bindValue(":id", $id);
            $s_sel->execute();

            $file=$s_sel->fetch(PDO::FETCH_COLUMN);

            unlink($dir."/documents/".$file);

            $s_del->bindValue(":id", $id);
            $s_del->execute();

            if ($s_del->rowCount()>0) {
                $success++;
            }
        }
    }

    if ($success>0) {
        $res=Array(
            "result" => "OK"
        );
    } else {
        $res=Array(
            "result" => "ERROR"
        );        
    }

    echo json_encode($res);
?>