<?php
	$dir=realpath(dirname(__FILE__)."/../..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT name, vote_question, vote_maket FROM 4232_base.votes_list WHERE id=:vote_id");
    $s->bindValue(":vote_id", $_POST["vote_id"]);
    $s->execute();

    if ($s->rowCount()==0) {
        echo "Голосование не найдено";
    } else {
        $vote=$s->fetch(PDO::FETCH_ASSOC);

        echo json_encode($vote);
    }
?>