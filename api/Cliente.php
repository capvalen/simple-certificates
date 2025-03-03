<?php
//error_reporting(E_ALL); ini_set("display_errors", 1);	
require("conectInfocat.php");
$data = json_decode(file_get_contents('php://input'), true);

switch ($data['pedir']) {
	case 'crear': crear($datab, $data); break;
	case 'buscarDNI': buscarDNI($datab, $data); break;
	case 'listar': listar($datab, $data); break;
	case 'listarActivos': listarActivos($datab); break;
	case 'listarID': listarID($datab, $data); break;
	case 'registrarMuestra': registrarMuestra($datab, $data); break;
	case 'eliminar': eliminar($datab, $data); break;
	default:
		# code...
		break;
}

function buscarDNI($db, $data){
	$filas = [];
	$sql= $db->prepare("SELECT * FROM `clientes` where dni= ? limit 1; ");
	$sql->execute([$data['dni']]);
	$row= $sql->fetch(PDO::FETCH_ASSOC);
	$filas = $row;
	echo json_encode($filas);
}
function listarID($db, $data){
	$filas = [];
	$sql= $db->prepare("SELECT * FROM `clientes` where id= ?; ");
	$sql->execute([$data['id']]);
	$row= $sql->fetch(PDO::FETCH_ASSOC);
	$filas = $row;
	echo json_encode($filas);
}

function listar($db, $data){
	$filas = [];
	$sql= $db->prepare("SELECT * FROM `clientes` where dni= ? and activo=1 limit 1");
	$sql->execute([$data['cliente']['dni']]);
	$row= $sql->fetch(PDO::FETCH_ASSOC);
		$filas = $row;
	echo json_encode($filas);
}

function listarActivos($db){
	$filas = [];
	$sql= $db->prepare("SELECT * FROM `clientes` where activo=1 order by id desc limit 50");
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas []= $row;
	foreach($filas as &$alumno){
		$sqlCursos = $db->prepare("SELECT count(*) as conteo from muestras where activo = 1 and idCliente = ?;");
		$sqlCursos->execute([$alumno['id']]);
		$rowCursos = $sqlCursos->fetch(PDO::FETCH_ASSOC);
		$alumno['conteo'] = $rowCursos['conteo'];
	}
	echo json_encode($filas);
}

function crear($db, $data){
	ob_start();
	listar($db, $data);
	$respuesta = ob_get_clean();
	$aRespuesta = json_decode($respuesta, true);

	$cliente = $data['cliente'];
	if( $aRespuesta == 0 ){
		//Crear al cliente
		$sql = $db->prepare("INSERT INTO `clientes`(`dni`, `nombre`) VALUES (?, ?)");
		$sent = $sql->execute([ $cliente['dni'], $cliente['nombre'] ]);
		if($sent) $idCliente = $db->lastInsertId();
		else $idCliente = 1;
	}else{
		//el cliente ya esta creado
		$idCliente = $aRespuesta['id'];
		$sql = $db->prepare("UPDATE `clientes` set
		`dni` = ?, `nombre` = ?
		where id = ?");
		$sent = $sql->execute([ $cliente['dni'], $cliente['nombre'], $idCliente ]);
	}

	echo json_encode( array('idCliente' => $idCliente));
}

function registrarMuestra($db, $data){
	ob_start();
	crear($db, $data);
	$respuesta = ob_get_clean();
	$aRespuesta = json_decode($respuesta, true);
	$idCliente = $aRespuesta['idCliente'];

	$idMuestra=-1;
	$muestra = $data['muestra'];

	$sql = $db->prepare("INSERT INTO `muestras`(`idCliente`, `idCurso`, `fecha`, `codigo`) VALUES (?,?,?,?)");
	$sent = $sql->execute([
		$idCliente, $muestra['id'], $muestra['fecha'],$muestra['codigo']
	]);

	if($sent) $idMuestra = $db->lastInsertId();

	echo json_encode( array('idMuestra' => $idMuestra));
}

function eliminar($db, $data){
	$sql = $db->prepare("UPDATE `clientes` set activo=0 where id = ? ");
	$sent = $sql->execute([ $data['id'] ]);
	if($sent) echo 'ok';
	else echo 'error';
}

?>