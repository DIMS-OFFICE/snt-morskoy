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

	$s=$db->prepare("SELECT id, user_id, changed, value, DATE(date_time) as date, status FROM 4232_base.lk_changes ORDER BY status ASC, date_time DESC LIMIT 150");

	$s->execute();

	$changes=$s->fetchAll(PDO::FETCH_ASSOC);

	$i=0;
	foreach ($changes as $change) {
		$f[$i]["id"]=$change["id"];
		$f[$i]["changed"]=$change["changed"];
		$f[$i]["value"]=$change["value"];
		$f[$i]["status"]=$change["status"];
		$f[$i]["date"]=$change["date"];
		$f[$i]["user"]=$users[$change["user_id"]][0]["sirname"]." ".$users[$change["user_id"]][0]["name"];

		$i++;
	}

	if ($i==0) {
		$res=Array(
			"result" => "NO_CHANGES",
		);
	} else {
		$res=Array(
			"result" => $result,
			"changes" => $f
		);
	}

	echo json_encode($res);
?>