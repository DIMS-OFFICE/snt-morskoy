<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	$s=$db->prepare("UPDATE 4232_base.logins SET password=:password WHERE tel_nom=:tel_nom");
	$s->bindValue(":tel_nom", $_POST["tel_nom"]);
	$s->bindValue(":password", $_POST["password"]);

	$s->execute();

	$result=Array(
		"result" => "OK"
	);

	echo json_encode($result);
?>