<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	if ($_POST["action"]=="password_repair") {
		$s=$db->prepare("SELECT id FROM 4232_base.logins WHERE tel_nom=:tel_nom");
		$s->bindValue(":tel_nom", $_POST["tel_nom"]);
		$s->execute();

		if ($s->rowCount()==0) {
			$res=Array(
				"result" => "no_registration"
			);

			echo json_encode($res);

			exit();
		}
	}

	if ($_POST["action"]=="code_generate" || $_POST["action"]=="password_repair") {
		if ($_POST["action"]=="code_generate") {
			if (strlen($_POST["tel_nom"])==11) {
				$_POST["tel_nom"]=substr($_POST["tel_nom"],1,10);
			}

			$s=$db->prepare("SELECT id FROM 4232_base.logins WHERE tel_nom=:tel_nom");
			$s->bindValue(":tel_nom", $_POST["tel_nom"]);

			$s->execute();

			if ($s->rowCount()>0) {
				$result=Array(
					"result" => "alredy_registered"
				);

				echo json_encode($result);

				exit();
			}
		}

		$validate_code=rand(1,9).rand(0,9).rand(0,9).rand(0,9);

		$s=$db->prepare("DELETE FROM 4232_base.validate_codes WHERE tel_nom=:tel_nom");
		$s->bindValue(":tel_nom", $_POST["tel_nom"]);
		$s->execute();

		$s=$db->prepare("INSERT INTO 4232_base.validate_codes (tel_nom, code, date_time) VALUES (:tel_nom, :code, :date_time)");
		$s->bindValue(":tel_nom", $_POST["tel_nom"]);
		$s->bindValue(":code", $validate_code);
		$s->bindValue(":date_time", date("Y-m-d H:i:s", time()));
		$s->execute();

		if ($s->rowCount()>0) {
			$s=$db->prepare("SELECT id FROM 4232_base.validate_codes WHERE tel_nom=:tel_nom");
			$s->bindValue(":tel_nom", $_POST["tel_nom"]);
			$s->execute();

			$msg_id=$s->fetch(PDO::FETCH_COLUMN);

			$result=send_sms($msg_id, "7".$_POST["tel_nom"], "Код snt-morskoy.ru: ".$validate_code);

			if ($result==true) {
				$res=Array(
					"result" => "OK"
				);
			} else {
				$res=Array(
					"result" => "sms_error"
				);
			}
		} else {
			$res=Array(
				"result" => "error"
			);
		}
		
	} else if ($_POST["action"]=="validate") {
		$s=$db->prepare("SELECT id FROM 4232_base.validate_codes WHERE tel_nom=:tel_nom AND code=:code");
		$s->bindValue(":tel_nom", $_POST["tel_nom"]);
		$s->bindValue(":code", $_POST["validate_code"]);

		$s->execute();

		if ($s->rowCount()>0) {
			$s=$db->prepare("DELETE FROM 4232_base.validate_codes WHERE tel_nom=:tel_nom");
			$s->bindValue(":tel_nom", $_POST["tel_nom"]);

			$s->execute();

			$res=Array(
				"result" => "OK",
			);
		} else {
			$res=Array(
				"result" => "error",
			);
		}
	}

	echo json_encode($res);

	function send_sms($msg_id, $tel_nom, $txt) {
	    $data='{"messages": [{"recipient": "'.$tel_nom.'","recipientType": "recipient","id": "string","source": "string","timeout": 3600,"shortenUrl": true,"text": "'.$txt.'"}],"validate": false}';

	    $ch=curl_init();

	    curl_setopt_array($ch, [
	        CURLOPT_URL => "https://lcab.smsprofi.ru/json/v1.0/sms/send/text",
	        CURLOPT_POST => true,
	        CURLOPT_HTTPHEADER => [
	            "X-Token: 25wfgh3xqkql3tof2dmivul9smv6x8uvnawj99i6bvj0vr1u4csicbfy8pmpbrsv",
	            "Content-Type: application/json"
	        ],
	        CURLOPT_POSTFIELDS => $data,
	        CURLOPT_RETURNTRANSFER => true
	    ]);

	    $result = curl_exec($ch);

	    $result=json_decode($result, true);

	    return $result["success"];
	}
?>