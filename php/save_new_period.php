<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	$s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
	$s->bindValue(":hash", $_POST["hash"]);
	$s->execute();

	if ($s->rowCount()==0) {
		echo "NOT_AUTH";

		exit();
	}

	$user_id=$s->fetch(PDO::FETCH_COLUMN);

	$s=$db->prepare("SELECT value, counter_type FROM 4232_base.counters WHERE area_nom=:area_nom AND period<:period ORDER BY period DESC LIMIT 1");
	$s->bindValue(":area_nom", $_POST["area_nom"]);
	$s->bindValue(":period", $_POST["period"]);
	$s->execute();

	if ($s->rowCount()>0) {
		$record=$s->fetch(PDO::FETCH_ASSOC);

		if ($_POST["counter_type"]==$record["counter_type"]) {//Если тип счётсика не изменился с прошлого месяца
			if ($_POST["counter_type"]=="Двухтарифный") {
				$parts=explode("/", $record["value"]);

				$day_last_period=$parts[0];
				$night_last_period=$parts[1];

				$parts=explode("/", $_POST["value"]);

				$day=$parts[0];
				$night=$parts[1];

				if ($day<$day_last_period || $night<$night_last_period) {
					echo "WRONG_VALUE";

					exit();
				}
			} else {
				if ($_POST["value"]<$record["value"]) {
					echo "WRONG_VALUE";

					exit();
				}
			}
		}
	}

	if ($_POST["counter_id"]==0) {
		$s=$db->prepare("INSERT INTO 4232_base.counters (user_id, area_nom, period, counter_type, value, for_pay, paid, `date`, `time`) VALUES (:user_id, :area_nom, :period, :counter_type, :value, :for_pay, :paid, DATE(NOW()), TIME(NOW()))");
		$s->bindValue(":user_id", $user_id);
		$s->bindValue(":area_nom", $_POST["area_nom"]);
		$s->bindValue(":period", $_POST["period"]);
		$s->bindValue(":counter_type", $_POST["counter_type"]);
		$s->bindValue(":value", $_POST["value"]);
		$s->bindValue(":for_pay", $_POST["for_pay"]);
		$s->bindValue(":paid", $_POST["paid"]);
		$s->execute();
	} else {
		$s=$db->prepare("UPDATE 4232_base.counters SET area_nom=:area_nom, period=:period, counter_type=:counter_type, value=:value, for_pay=:for_pay, paid=:paid, `date`=DATE(NOW()), `time`=TIME(NOW()) WHERE id=:counter_id");
		$s->bindValue(":counter_id", $_POST["counter_id"]);
		$s->bindValue(":area_nom", $_POST["area_nom"]);
		$s->bindValue(":period", $_POST["period"]);
		$s->bindValue(":counter_type", $_POST["counter_type"]);
		$s->bindValue(":value", $_POST["value"]);
		$s->bindValue(":for_pay", $_POST["for_pay"]);
		$s->bindValue(":paid", $_POST["paid"]);
		$s->execute();
	}

	if ($s->rowCount()>0) {
		echo "OK";
	} else {
		echo "ERROR";
	}
?>