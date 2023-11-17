<?php
	$dir=realpath(dirname(__FILE__)."/..");

	require($dir."/php/pdo_db_connect.php");

	$s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
	$s->bindValue(":hash", $_POST["hash"]);
	$s->execute();

	if ($s->rowCount()==0) {
		echo "NOT_AUTH";

		exit();
	}

	$user_id=$s->fetch(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT * FROM 4232_base.counters WHERE user_id=:user_id ORDER BY period DESC, `date` DESC, `time` DESC");
	$s->bindValue(":user_id", $user_id);
	$s->execute();

	$counters_list=$s->fetchAll(PDO::FETCH_ASSOC);

	$files=scandir($dir."/users_files/".$user_id."/payment_docs");

	$results=array(
		"counters_list" => $counters_list,
		"files" => $files
	);

	echo json_encode($results);