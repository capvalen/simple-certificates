<?php
require("conectInfocat.php");
$data = json_decode(file_get_contents('php://input'), true);

$sql=$datab->prepare("SELECT idUsuario, usuNombre, usuPoder FROM usuarios where usuNombre = ? and usuPass = MD5(?) and usuActivo = 1;");
if($sql->execute([
	$data['usuario'], $data['clave']
])){
	$filas = $sql->rowCount();
	//echo $filas;
	if($filas > 0){
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		
		echo json_encode(array('mensaje' => 'ok', 'usuario'=> $row));

	}else
		echo json_encode(array('mensaje' => 'error1'));
}else
	echo json_encode(array('mensaje' => 'error2'));
