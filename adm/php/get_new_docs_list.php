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

    $s_users=$db->prepare("SELECT id, name, sirname FROM 4232_base.logins");;
    $s_users->execute();

    $users=$s_users->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);

	$s=$db->prepare("SELECT id, `groups`, user_id, category, file_name_rus, DATE(date_time) as date_time FROM 4232_base.documents WHERE active=:active ORDER BY file_name_rus");
	$s->bindValue(":active", $_POST["active"]);

	$s->execute();

	$files=$s->fetchAll(PDO::FETCH_ASSOC);

	$i=0;
	foreach ($files as $file) {
		$f[$i]["id"]=$file["id"];
		$f[$i]["category"]=$file["category"];
		$f[$i]["groups"]=$file["groups"];
		$f[$i]["file_name_rus"]=$file["file_name_rus"];
		$f[$i]["date_time"]=$file["date_time"];
		$f[$i]["user"]=$users[$file["user_id"]][0]["sirname"]." ".$users[$file["user_id"]][0]["name"];

		$i++;
	}

	if ($i==0) {
		$res=Array(
			"result" => "NO_DOCS",
		);
	} else {
		$res=Array(
			"result" => $result,
			"files" => $f
		);
	}

	echo json_encode($res);
?>