<?php
require("conectInfocat.php");
$data = json_decode(file_get_contents('php://input'), true);

switch ($data['pedir']) {
	case 'listar': listar($datab); break;
	case 'crear': crear($datab, $data); break;
	case 'eliminar': eliminar($datab, $data); break;
	default:
		# code...
		break;
}

function listar($db){
	$filas = [];
	$sql= $db->prepare("SELECT * FROM `cursos` where activo=1 order by nombre asc");
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas [] = $row;
	echo json_encode($filas);
}

function crear($db, $data){
	$id='';
	$sql = $db->prepare("INSERT INTO `cursos`(`nombre`, `fecha`) VALUES (?,?)");
	$sent = $sql->execute([ $data['curso']['nombre'], $data['curso']['fecha'] ]);
	if($sent) $id = $db->lastInsertId();
	echo json_encode( array('id' => $id));

}

function eliminar($db, $data){
	$sql = $db->prepare("UPDATE `cursos` set activo=0 where id = ? ");
	$sent = $sql->execute([ $data['id'] ]);
	if($sent) echo 'ok';
	else echo 'error';
}

?>