<?php
	$dir=realpath(dirname(__FILE__) . '/../..');

	require($dir."/php/pdo_db_connect.php");

 	$s=$db->prepare("SELECT id FROM 4232_base.documents WHERE file_name_rus=:old_doc_name");
 	$s->bindValue(":old_doc_name", $_POST["old_doc_name"]);
 	$s->execute();

 	$doc_id=$s->fetch(PDO::FETCH_COLUMN);

 	if (file_exists($dir."/documents/".$_POST["old_doc_name"])==false) {
 		echo "Переименовываемый файл не существует";
 		exit();
 	}

 	$ext=explode(".", $_POST["old_doc_name"]);
 	$ext=$ext[count($ext)-1];

 	if (rename($dir."/documents/".$_POST["old_doc_name"], $dir."/documents/".$_POST["new_name"].".".$ext)) {
 		$s=$db->prepare("UPDATE 4232_base.documents SET file_name_rus=:new_file_name WHERE id=:doc_id");
 		$s->bindValue(":doc_id", $doc_id);
 		$s->bindValue(":new_file_name", $_POST["new_name"].".".$ext);
 		$s->execute();

 		echo "OK";
 	} else {
 		echo "Ошибка перименования файла";
 	}
?>