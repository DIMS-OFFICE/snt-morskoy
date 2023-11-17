<?php
	$dir=realpath(dirname(__FILE__)."/../..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT id FROM 4232_base.votes_list WHERE id=:vote_id");
    $s->bindValue(":vote_id", $_POST["vote_id"]);
    $s->execute();

    if ($s->rowCount()==0) {
    	$s=$db->prepare("INSERT INTO 4232_base.votes_list (name, vote_question, vote_maket, `date`, active) VALUES (:name, :vote_question, :vote_maket, DATE(NOW()), 0)");
    	$s->bindValue(":name", $_POST["vote_caption"]);
    	$s->bindValue(":vote_question", $_POST["vote_question"]);
    	$s->bindValue(":vote_maket", $_POST["vote_maket"]);
    	$s->execute();

    	if ($s->rowCount()>0) {
    		$s=$db->prepare("SELECT MAX(id) as vote_id FROM 4232_base.votes_list");
    		$s->execute();

    		$vote_id=$s->fetch(PDO::FETCH_COLUMN);

    		$result=Array(
    			"result" => "OK",
    			"vote_id" => $vote_id
    		);

    		echo json_encode($result);
    	} else {
    		$result=Array(
    			"result" => "Ошибка создания голосования"
    		);

    		echo json_encode($result);
    	}
    } else {
    	$s=$db->prepare("UPDATE 4232_base.votes_list SET name=:name, vote_question=:vote_question, vote_maket=:vote_maket, active=:active WHERE id=:vote_id");
    	$s->bindValue(":name", $_POST["vote_caption"]);
    	$s->bindValue(":vote_question", $_POST["vote_question"]);
    	$s->bindValue(":vote_maket", $_POST["vote_maket"]);
    	$s->bindValue(":active", $_POST["action"]);
    	$s->bindValue(":vote_id", $_POST["vote_id"]);
    	$s->execute();

    	if ($s->rowCount()>0) {
    		$result=Array(
    			"result" => "OK",
    			"vote_id" => $_POST["vote_id"]
    		);

    		echo json_encode($result);
    	} else {
    		$result=Array(
    			"result" => "Ошибка изменения голосования"
    		);

    		echo json_encode($result);
    	}
    }
?>