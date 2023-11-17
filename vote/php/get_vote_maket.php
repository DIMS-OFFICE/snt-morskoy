<?php
	$dir=realpath(dirname(__FILE__)."/../..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT user_id FROM 4232_base.users_sessions WHERE hash=:hash");
    $s->bindValue(":hash", $_POST["hash"]);

    $s->execute();

    if ($s->rowCount()>0) {
        $user_id=$s->fetch(PDO::FETCH_COLUMN);
    } else {
        $result="SESSION_ERROR";

        $result=Array(
            "result" => $result,
        );

        echo json_encode($result);

        exit();
    }

    $s=$db->prepare("SELECT id FROM 4232_base.vote_history WHERE vote_id=:vote_id AND user_id=:user_id");
    $s->bindValue(":vote_id", $_POST["vote_id"]);
    $s->bindValue(":user_id", $user_id);
    $s->execute();

    if ($s->rowCount()==0) {
        $already_voted=0;
    } else {
        $already_voted=1;
    }

    $s=$db->prepare("SELECT name, vote_maket, active FROM 4232_base.votes_list WHERE id=:vote_id");
    $s->bindValue(":vote_id", $_POST["vote_id"]);
    $s->execute();

    if ($s->rowCount()==0) {
        $result=Array(
            "result" => "VOTE_NOT_FOUND",
        );

        echo json_encode($result);
    } else {
        $vote=$s->fetch(PDO::FETCH_ASSOC);

        $result=Array(
            "vote" => $vote,
            "already_voted" => $already_voted
        );

        echo json_encode($result);
    }
?>