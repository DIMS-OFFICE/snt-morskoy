<?php
	$dir=realpath(dirname(__FILE__) . '/../..');

	require($dir."/php/pdo_db_connect.php");

	$s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
	$s->bindValue(":hash", $_POST["hash"]);
	$s->execute();

	if ($s->rowCount()==0) {
		echo "NOT_AUTH";

		exit();
	}

	$s=$db->prepare("SELECT * FROM 4232_base.counters ORDER BY period DESC, `date` DESC, `time` DESC");
	$s->execute();

	$counters_list=$s->fetchAll(PDO::FETCH_ASSOC);

	$s=$db->prepare("SELECT id, sirname, name FROM 4232_base.logins");
	$s->execute();

	$users=$s->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);

	$folders=scandir($dir."/users_files");

	$payment_files=Array();

	foreach ($folders as $folder) {
		if ($folder!="." && $folder!="..") {
			$files=scandir($dir."/users_files/".$folder."/payment_docs");

			foreach ($files as $file) {
				if ($file!="." && $file!="..") {
					$payment_files[]=$file;
				}
			}
		}
	}

	$results=array(
		"counters_list" => $counters_list,
		"files" => $payment_files,
		"users" => $users
	);

	echo json_encode($results);