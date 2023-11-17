<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	require("pdo_db_connect.php");

	$s=$db->prepare("SELECT * FROM 4232_base.vote1 ORDER BY ".$_POST["sort_order"]." ".$_POST["sort_direction"]);
	$s->execute();

	$res=$s->fetchAll(PDO::FETCH_ASSOC);

	$s=$db->prepare("SELECT SUM(var1) as var1, SUM(var2) as var2 FROM 4232_base.vote1");
	$s->execute();

	$totals_votes=$s->fetch(PDO::FETCH_ASSOC);

	$s=$db->prepare("SELECT MAX(var1) as var1, MAX(var2) as var2 FROM 4232_base.vote1");
	$s->execute();

	$max_values=$s->fetch(PDO::FETCH_ASSOC);

	$s=$db->prepare("SELECT id FROM 4232_base.vote1 WHERE var1=:var1");
	$s->bindValue(":var1", $max_values["var1"]);
	$s->execute();

	$leaders["var1"]=$s->fetchAll(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT id FROM 4232_base.vote1 WHERE var2=:var2");
	$s->bindValue(":var2", $max_values["var2"]);
	$s->execute();

	$leaders["var2"]=$s->fetchAll(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT votes_count FROM 4232_base.vote1 LIMIT 1");
	$s->bindValue(":var2", $max_values["var2"]);
	$s->execute();

	$votes_count=$s->fetch(PDO::FETCH_COLUMN);

	$result=Array(
		"results" => $res,
		"totals_votes" => $totals_votes,
		"leaders" => $leaders,
		"votes_count" => $votes_count
	);

	echo json_encode($result);
?>