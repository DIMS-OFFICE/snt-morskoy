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

        $result=Array(
            "result" => $result,
        );

        echo json_encode($result);

        exit();
    }

    $s=$db->prepare("SELECT id, tel_nom, name, sirname, middle_name, account_type, active FROM 4232_base.logins ORDER BY sirname ASC");
    $s->execute();

    $users=$s->fetchAll(PDO::FETCH_ASSOC);

    $result=Array(
        "result" => $result,
        "users" => $users
    );

    echo json_encode($result);
?>