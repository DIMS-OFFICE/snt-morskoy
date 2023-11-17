<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	if (isset($_POST["group"])) {
		$s=$db->prepare("SELECT file_name_rus FROM 4232_base.documents WHERE `groups`=:groups ORDER BY file_name_rus");
		$s->bindValue(":groups", $_POST["group"]);

		$s->execute();

		$files=$s->fetchAll(PDO::FETCH_ASSOC);

		echo json_encode($files);

		exit();
	}

 	$s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    if ($s->rowCount()>0) {
    	$user_id=$s->fetch(PDO::FETCH_COLUMN);
    } else {
    	$user_id=0;
    }

    if ($_POST["category"]=="all") {
		$s=$db->prepare("SELECT COUNT(id) as c, `groups`, file_name_rus, DATE(date_time) as date_time, active FROM 4232_base.documents WHERE user_id=:user_id OR active=1 GROUP BY `groups` ORDER BY active ASC, file_name_rus");
		$s->bindValue(":user_id", $user_id);
	} else {
		$s=$db->prepare("SELECT COUNT(id) as c, `groups`, file_name_rus, DATE(date_time) as date_time, active FROM 4232_base.documents WHERE category=:category AND active=1 GROUP BY `groups` ORDER BY active ASC, file_name_rus");
		$s->bindValue(":category", $_POST["category"]);
	}

	$s->execute();

	$files=$s->fetchAll(PDO::FETCH_ASSOC);

	if ($user_id==0) {
		$result="NOT_AUTH";
	} else {
		$result="AUTHORIZED";
	}

	$files=Array(
		"result" => $result,
		"files" => $files
	);

	echo json_encode($files);
?>