<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	$s=$db->prepare("DELETE FROM 4232_base.users_sessions WHERE hash=:hash");
	$s->bindValue(":hash", $_POST["hash"]);

	$s->execute();

	if ($s->rowCount()>0) {
		$result=Array(
			"result" => "OK"
		);
	} else {
		$result=Array(
			"result" => "error"
		);
	}

	echo json_encode($result);
?>