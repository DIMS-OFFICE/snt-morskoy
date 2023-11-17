<?php
	if (isset($db_name)==false) {
		$db_name="phones100";
	}
	
	try {
	 $db = new PDO("mysql:host=localhost;dbname=".$db_name, "root", "DiMS-21093@", array(PDO::MYSQL_ATTR_LOCAL_INFILE=>1));															

	  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	  $db->exec("set names utf8");
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
?>
