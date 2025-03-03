<?php
require("conectInfocat.php");
$data = json_decode(file_get_contents('php://input'), true);

switch ($data['pedir']) {
	case 'listar': listar($datab); break;
	case 'crear': crear($datab, $data); break;
	case 'crearUsuario': crearUsuario($datab, $data); break;
	case 'cambiarClave': cambiarClave($datab, $data); break;
	default:
		# code...
		break;
}

function listar($db){
	$filas = [];
	$sql= $db->prepare("SELECT m.*, u.idUsuario, u.usuNombre
		FROM `medicos` m 
		left join usuarios u on u.usuNombre = m.dni
		where m.activo=1
		order by nombre;");
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas [] = $row;
	echo json_encode($filas);
}

function crear($db, $data){
	$id='';
	$sql = $db->prepare("INSERT INTO `medicos`(`nombre`, `dni`, `esEmpresa`) VALUES (?, ?, ?)");
	$sent = $sql->execute([ $data['sede']['sede'], $data['sede']['dni'], $data['sede']['entidad'] ]);
	if($sent) $id = $db->lastInsertId();

	ob_start();
	$data['id'] = $id;
	$data['clave']=$data['sede']['clave'];
	crearUsuario($db, $data);	
	ob_clean();
	echo json_encode( array('id' => $id));
}

function crearUsuario($db, $data){
	$sql = $db->prepare("INSERT INTO `usuarios`(`usuNombre`, `usuPass`, `usuActivo`, `usuPoder`) VALUES (?, MD5(?), ?, ?)");
	$sent = $sql->execute([ $data['medico']['dni'], $data['clave'], 1, 1 ]);
	if($sent) $id = $db->lastInsertId();
	echo json_encode( array('id' => $id));
}

function cambiarClave($db, $data){
	$sql = $db->prepare("UPDATE usuarios SET usuPass = md5(?) where idUsuario = ?; ");
	$sent = $sql->execute([ $data['clave'], $data['id'] ]);
	
	echo json_encode( array('mensaje' => 'ok'));
}

?>