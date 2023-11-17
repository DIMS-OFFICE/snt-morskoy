<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	$s=$db->prepare("INSERT INTO 4232_base.logins (name, sirname, middle_name, birth_date, tel_nom, password, account_type, reg_date_time) VALUES (:name, :sirname, :middle_name, :birth_date, :tel_nom, :password, 'user', :reg_date_time)");
	$s->bindValue(":name", $_POST["name"]);
	$s->bindValue(":sirname", $_POST["sirname"]);
	$s->bindValue(":middle_name", $_POST["middle_name"]);
	$s->bindValue(":birth_date", $_POST["birth_date"]);
	$s->bindValue(":tel_nom", $_POST["tel_nom"]);
	$s->bindValue(":password", $_POST["password"]);
	$s->bindValue(":reg_date_time", date("Y-m-d H:i:s", time()));

	$s->execute();

	if ($s->rowCount()>0) {

		$s=$db->prepare("SELECT id FROM 4232_base.logins WHERE tel_nom=:tel_nom");
		$s->bindValue(":tel_nom", $_POST["tel_nom"]);

		$s->execute();

		$user_id=$s->fetch(PDO::FETCH_COLUMN);

		$hash=base64_encode(time());

		$s=$db->prepare("INSERT INTO 4232_base.users_sessions (user_id, name, sirname, hash, date_time) VALUE (:user_id, :name, :sirname, :hash, :date_time)");
		$s->bindValue(":user_id", $user_id);
		$s->bindValue(":name", $_POST["name"]);
		$s->bindValue(":sirname", $_POST["sirname"]);
		$s->bindValue(":hash", $hash);
		$s->bindValue(":date_time", date("Y-m-d H:i:s", time()));

		$s->execute();

		$result=Array(
			"result" => "OK",
			"hash" => $hash
		);
	} else {
		$result=Array(
			"result" => "Какая-то ошибка"
		);
	}

	echo json_encode($result);
?>