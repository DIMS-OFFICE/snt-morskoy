<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	if (isset($_POST["tel_nom"]) && isset($_POST["password"])) {
		if (strlen($_POST["tel_nom"])==11) {
			$_POST["tel_nom"]=substr($_POST["tel_nom"],1,10);
		}

		$s=$db->prepare("SELECT id, name, sirname FROM 4232_base.logins WHERE tel_nom=:tel_nom AND password=:password");
		$s->bindValue(":tel_nom", $_POST["tel_nom"]);
		$s->bindValue(":password", $_POST["password"]);
		$s->execute();

		if ($s->rowCount()>0) {
			$user=$s->fetch(PDO::FETCH_ASSOC);

			$hash=base64_encode(time());

			$s=$db->prepare("DELETE FROM 4232_base.users_sessions WHERE user_id=:user_id");
			$s->bindValue(":user_id", $user["id"]);

			$s->execute();

			$s=$db->prepare("INSERT INTO 4232_base.users_sessions (user_id, name, sirname, hash, date_time, user_agent) VALUE (:user_id, :name, :sirname, :hash, :date_time, :user_agent)");
			$s->bindValue(":user_id", $user["id"]);
			$s->bindValue(":name", $user["name"]);
			$s->bindValue(":sirname", $user["sirname"]);
			$s->bindValue(":hash", $hash);
			$s->bindValue(":date_time", date("Y-m-d H:i:s", time()));
			$s->bindValue(":user_agent", $_SERVER["HTTP_USER_AGENT"]);

			$s->execute();

			$result=Array(
				"result" => "OK",
				"hash" => $hash
			);
		} else {
			$result=Array(
				"result" => "Неверный логин или пароль. Возможно Вы не зарегистрированы"
			);
		}

		echo json_encode($result);
	} else {
		$s=$db->prepare("SELECT user_id, name, sirname FROM 4232_base.users_sessions WHERE hash=:hash");
		$s->bindValue(":hash", $_POST["hash"]);

		$s->execute();

		if ($s->rowCount()>0) {
			$user=$s->fetch(PDO::FETCH_ASSOC);

			$s=$db->prepare("SELECT changed, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND status=0 AND area_id=0");
			$s->bindValue(":user_id", $user["user_id"]);
			$s->execute();

			if ($s->rowCount()>0) {//Если есть нерассмотренные админом позиции, значит регистрация ещё на рассмотрении
				$account_status="Ваша регистрация на рассмотрении";
			} else {
				$s=$db->prepare("SELECT changed, comment FROM 4232_base.lk_changes WHERE user_id=:user_id AND status=2 AND area_id=0");
				$s->bindValue(":user_id", $user["user_id"]);
				$s->execute();

				if ($s->rowCount()==0) {
					$s=$db->prepare("DELETE FROM 4232_base.lk_changes WHERE user_id=:user_id AND area_id=0");
					$s->bindValue(":user_id", $user["user_id"]);
					$s->execute();

					if ($s->rowCount()>0) {
						$account_status="Ваша регистрация подтверждена";
					} else {
						$account_status="";
					}
				} else {//Если есть ошибки в регистрации
					$errors=$s->fetchAll(PDO::FETCH_ASSOC);

					$account_status=Array();
					foreach ($errors as $error) {
						$account_status[]=$error["changed"].": ".$error["comment"];
					}

					$account_status="Ошибки в личных данных:<BR>".implode("<BR>", $account_status);
				}
			}

			if ($account_status=="") {
				$s=$db->prepare("SELECT id FROM 4232_base.logins WHERE (passport_seria='' OR passport_number='' OR reg_address='' OR email='' OR passport1='' OR passport2='') AND id=:user_id");
				$s->bindValue(":user_id", $user["user_id"]);
				$s->execute();

				if ($s->rowCount()>0) {
					$account_status="Не все личные данные заполнены";
				}
			}
			
			$s=$db->prepare("SELECT account_type, active FROM 4232_base.logins WHERE id=:user_id");
			$s->bindValue(":user_id", $user["user_id"]);
			$s->execute();

			$account=$s->fetch(PDO::FETCH_ASSOC);

			if ($account["active"]==0) {
				$result=Array(
					"result" => "Ваш аккаунт заблокирован.<BR>Обратитесь к администратору",
				);

				echo json_encode($result);

				exit();
			}

			$result=Array(
				"result" => "OK",
				"user_name" => $user["name"],
				"user_sirname" => $user["sirname"],
				"account_status" => $account_status,
				"account_type" => $account["account_type"],
				"hash" => $_POST["hash"]
			);
		} else {
			$result=Array(
				"result" => ""
			);
		}

		echo json_encode($result);
	}
?>