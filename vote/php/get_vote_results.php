<?php
	$dir=realpath(dirname(__FILE__)."/../..");

    require($dir."/php/pdo_db_connect.php");

    $s=$db->prepare("SELECT selected, COUNT(selected) as c FROM 4232_base.vote_history WHERE vote_id=:vote_id GROUP BY selected");
    $s->bindValue(":vote_id", $_POST["vote_id"]);
    $s->execute();

    for ($i=0; $i<30; $i++) {
    	$results[$i]=0;
    }

    if ($s->rowCount()>0) {
    	$selects=$s->fetchAll(PDO::FETCH_GROUP);

    	foreach ($selects as $select => $count) {
    		$results[$select]=intval($count[0]["c"]);
    	}
    }

    $results["total_votes"]=array_sum($results);

    echo json_encode($results);
?>