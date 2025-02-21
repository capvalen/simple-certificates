<?php
	$server="localhost";
	$username="root";
	$password="*123456*";
	$db='citolab';
	$conection= mysqli_connect($server,$username,$password)or die("No se ha podido establecer la conexion");
	$sdb= mysqli_select_db($conection,$db)or die("La base de datos no existe");
	$conection->set_charset("utf8");

	$conf= new mysqli($server, $username, $password, $db);
	$conf->set_charset("utf8");

	//Con Objetos:
	try {
		$datab = new PDO (
			'mysql:host=localhost;
			dbname='.$db,
			$username,
			$password,
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
	} catch (Exception $e) {
		echo "Problema con la conexion: ".$e->getMessage();
	}

	
?>