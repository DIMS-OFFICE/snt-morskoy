<?php
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	require("pdo_db_connect.php");

	$s=$db->prepare("SELECT * FROM 4232_base.candidats WHERE `show`=1 AND name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number ORDER BY ".$_POST["sort_order"]." ".$_POST["sort_direction"]);
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$res=$s->fetchAll();

	$s=$db->prepare("SELECT * FROM 4232_base.candidats WHERE `show`=1 AND name='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$against_all=$s->fetch(PDO::FETCH_ASSOC);

	$res[]=Array(
		"name" => "МНЕ ВСЁ РАВНО",
		"pravl_za" => $against_all["pravl_za"]
	);

	$s=$db->prepare("SELECT SUM(pravl_za) as pravl_za, SUM(pravl_vozd) as pravl_vozd, SUM(pravl_protiv) as pravl_protiv, SUM(preds_za) as preds_za, SUM(preds_protiv) as preds_protiv FROM 4232_base.candidats WHERE name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$totals_votes=$s->fetch(PDO::FETCH_ASSOC);


	$s=$db->prepare("SELECT MAX(pravl_za) as pravl_za, MAX(pravl_protiv) as pravl_protiv, MAX(preds_za) as preds_za, MAX(preds_protiv) as preds_protiv FROM 4232_base.candidats WHERE name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$max_values=$s->fetch(PDO::FETCH_ASSOC);

	$s=$db->prepare("SELECT id FROM 4232_base.candidats WHERE pravl_za=:max_pravl_za AND name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":max_pravl_za", $max_values["pravl_za"]);
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$leaders["pravl_za"]=$s->fetchAll(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT id FROM 4232_base.candidats WHERE pravl_protiv=:max_pravl_protiv AND name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":max_pravl_protiv", $max_values["pravl_protiv"]);
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$leaders["pravl_protiv"]=$s->fetchAll(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT id FROM 4232_base.candidats WHERE preds_za=:max_preds_za AND name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":max_preds_za", $max_values["preds_za"]);
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$leaders["preds_za"]=$s->fetchAll(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT id FROM 4232_base.candidats WHERE preds_protiv=:max_preds_protiv AND name!='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":max_preds_protiv", $max_values["preds_protiv"]);
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$leaders["preds_protiv"]=$s->fetchAll(PDO::FETCH_COLUMN);


				
	$s=$db->prepare("SELECT SUM(pravl_za) as against_all, votes_count FROM 4232_base.candidats WHERE name='МНЕ ВСЁ РАВНО' AND tur_number=:tur_number");
	$s->bindValue(":tur_number", $_POST["tur_number"]);
	$s->execute();

	$against_all=$s->fetch(PDO::FETCH_ASSOC);

	$result=Array(
		"results" => $res,
		"totals_votes" => $totals_votes,
		"leaders" => $leaders,
		"against_all" => $against_all["against_all"],
		"votes_count" => $against_all["votes_count"]

	);

	echo json_encode($result);
?>