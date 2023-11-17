<?php
	$dir=realpath(dirname(__FILE__));

	require($dir."/pdo_db_connect.php");

	$s=$db->prepare("SELECT id, name, `date`, active FROM 4232_base.votes_list ORDER BY active ASC, `date` DESC");
	$s->execute();

	$votes=$s->fetchAll(PDO::FETCH_ASSOC);

	echo json_encode($votes);
?>