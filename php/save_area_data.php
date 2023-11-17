<?php
	$dir=realpath(dirname(__FILE__)."/..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    if ($s->rowCount()>0) {
    	$user_id=$s->fetch(PDO::FETCH_COLUMN);

    	$s=$db->prepare("SELECT id FROM 4232_base.areas WHERE id=:area_id");
    	$s->bindValue(":area_id", $_POST["area_id"]);
	
		$s->execute();

		$s_change=$db->prepare("INSERT INTO 4232_base.lk_changes (user_id, area_id, changed, value, date_time) VALUES (:user_id, :area_id, :changed, :value, NOW())");

		$changed=false;
		
	    if ($s->rowCount()>0) {    	
		    $s=$db->prepare("UPDATE 4232_base.areas SET area_nom=:area_nom, update_time=NOW() WHERE id=:area_id");
		    $s->bindValue(":area_id", $_POST["area_id"]);
		    $s->bindValue(":area_nom", $_POST["area_nom"]);

		    $s->execute();

		    if ($s->rowCount()>0) {
		    	$s_change->bindValue(":user_id", $user_id);
				$s_change->bindValue(":changed", "Номер участка");
				$s_change->bindValue(":value", $_POST["area_nom"]);
				$s_change->bindValue(":area_id", $_POST["area_id"]);

				$s_change->execute();

		    	$changed=true;
		    }

		    $s=$db->prepare("UPDATE 4232_base.areas SET area_name=:area_name, update_time=NOW() WHERE id=:area_id");
		    $s->bindValue(":area_id", $_POST["area_id"]);
		    $s->bindValue(":area_name", $_POST["area_name"]);

		    $s->execute();

		    if ($s->rowCount()>0) {
		    	$s_change->bindValue(":user_id", $user_id);
				$s_change->bindValue(":changed", "Название участка");
				$s_change->bindValue(":value", $_POST["area_name"]);
				$s_change->bindValue(":area_id", $_POST["area_id"]);

				$s_change->execute();

		    	$changed=true;
		    }

		    $s=$db->prepare("UPDATE 4232_base.areas SET kadastr=:kadastr, update_time=NOW() WHERE id=:area_id");
		    $s->bindValue(":area_id", $_POST["area_id"]);
		    $s->bindValue(":kadastr", $_POST["kadastr"]);

		    $s->execute();

		    if ($s->rowCount()>0) {
		    	$s_change->bindValue(":user_id", $user_id);
				$s_change->bindValue(":changed", "Кадастровый номер");
				$s_change->bindValue(":value", $_POST["kadastr"]);
				$s_change->bindValue(":area_id", $_POST["area_id"]);

				$s_change->execute();

		    	$changed=true;
		    }

		    if ($changed==true) {	    		    
		    	$res=Array(
		    		send_mail("Изменение данных участка");

		    		"result" => "OK",
		    		"action" => "UPDATE"
		    	);
		    } else {
		    	$res=Array(
		    		"result" => "error",
		    		"desc" => "НЕ сохранено"
		    	);		    	
		    }
		} else {
			$s=$db->prepare("INSERT INTO 4232_base.areas (user_id, area_nom, area_name, kadastr, update_time) VALUES (:user_id, :area_nom, :area_name, :kadastr, NOW())"); 
			$s->bindValue(":user_id", $user_id);
			$s->bindValue(":area_nom", $_POST["area_nom"]);
		    $s->bindValue(":area_name", $_POST["area_name"]);
		    $s->bindValue(":kadastr", $_POST["kadastr"]);

			$s->execute();

		    if ($s->rowCount()>0) {
		    	send_mail("Изменение данных участка");
		    	
		    	$res=Array(
		    		"result" => "OK",
		    		"action" => "INSERT"
		    	);
		    }		    
		}
	} else {
	    if ($s->rowCount()>0) {
	    	$res=Array(
	    		"result" => "error",
	    		"desc" => "not_auth"
	    	);
	    }
	}

	echo json_encode($res);

    function send_mail($subject) {
        $to = 'prog@dims.mobi';     
        $message = 'https://snt-morskoy.ru/adm/';
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        $headers.= 'From: SNT-Morskoy'.'\r\n';
        $headers.= 'Reply-To: robot@snt-morskoy.ru';
        //$headers.='Content-type: text/plain; charset=UTF-8'.'\r\n';
        //$headers.='Content-transfer-encoding: quoted-printable';
        
        mail ($to, $subject, $message, $headers);
    }	
 ?>