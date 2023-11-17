<?php
	$dir=realpath(dirname(__FILE__) . '/../..');

	require($dir."/php/pdo_db_connect.php");

 	$s=$db->prepare("SELECT COUNT(id) FROM 4232_base.documents WHERE active=0");
 	$s->execute();

 	$new_documents=$s->fetch(PDO::FETCH_COLUMN);

 	$s=$db->prepare("SELECT COUNT(id) FROM 4232_base.lk_changes WHERE status=0");
 	$s->execute();

 	$new_lk_changes=$s->fetch(PDO::FETCH_COLUMN);

 	$res=Array(
 		"new_documents" => $new_documents,
 		"new_lk_changes" => $new_lk_changes
 	);

 	echo json_encode($res);
 ?>