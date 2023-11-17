<?php
	$dir=realpath(dirname(__FILE__)."/../..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    if ($s->rowCount()>0) {
        $user_id=$s->fetch(PDO::FETCH_COLUMN);
    } else {
        echo "SESSION_ERROR";

        exit();
    }

	$client_ip=$_SERVER['REMOTE_ADDR'];
	$user_agent=$_SERVER['HTTP_USER_AGENT'];

	$s=$db->prepare("INSERT INTO 4232_base.vote_history (user_id, vote_id, selected, client_ip, user_agent, date_time) VALUES (:user_id, :vote_id, :selected, :client_ip, :user_agent, NOW())");
	$s->bindValue(":client_ip", $client_ip);
	$s->bindValue(":user_agent", $user_agent);
	$s->bindValue(":user_id", $user_id);
	$s->bindValue(":vote_id", $_POST["vote_id"]);
	$s->bindValue(":selected", $_POST["selected"]);
	$s->execute();

	if ($s->rowCount()>0) {
		echo "OK";
	} else {
		echo "error";
	}
?>