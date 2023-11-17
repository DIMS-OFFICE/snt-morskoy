<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	require("pdo_db_connect.php");

	$client_ip=$_SERVER['REMOTE_ADDR'];

	$_POST["tur_number"]=2;

	$s=$db->prepare("SELECT id FROM 4232_base.vote_history1 WHERE client_ip=:client_ip");
	$s->bindValue(":client_ip", $client_ip);
	$s->execute();

	if ($s->rowCount()>0) {
		echo "already_vote";

		exit();
	}

	$ids=json_decode($_POST["var1"]);

	foreach ($ids as $id) {
		$s=$db->prepare("UPDATE 4232_base.vote1 SET var1=var1+1 WHERE id=:id");
		$s->bindValue(":id", $id);

		$s->execute();
	}

	$ids=json_decode($_POST["var2"]);

	foreach ($ids as $id) {
		$s=$db->prepare("UPDATE 4232_base.vote1 SET var2=var2+1 WHERE id=:id");
		$s->bindValue(":id", $id);

		$s->execute();
	}

	$s=$db->prepare("UPDATE 4232_base.vote1 SET votes_count=votes_count+1");

	$s->execute();

	$client_ip=$_SERVER['REMOTE_ADDR'];
	$user_agent=$_SERVER['HTTP_USER_AGENT'];

	$s=$db->prepare("INSERT INTO 4232_base.vote_history1 (client_ip, user_agent, var1, var2, date_time) VALUES (:client_ip, :user_agent, :var1, :var2, NOW())");
	$s->bindValue(":client_ip", $client_ip);
	$s->bindValue(":user_agent", $user_agent);
	$s->bindValue(":var1", $_POST["var1"]);
	$s->bindValue(":var2", $_POST["var2"]);

	$s->execute();	
?>