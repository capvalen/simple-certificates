<?php
error_reporting(E_ALL); ini_set("display_errors", 1);
require("conectInfocat.php");
$data = json_decode(file_get_contents('php://input'), true);

switch ($data['pedir']) {
	case 'listar': listar($datab, $data); break;
	case 'buscarPorFiltros': buscarPorFiltros($datab,$data); break;
	case 'registrosDNI': registrosDNI($datab,$data); break;
	case 'registrosID': registrosID($datab,$data); break;
	case 'eliminar': eliminar($datab, $data); break;
	default:
		# code...
		break;
}

function listar($db, $data){
	$filas = [];
	if($data['nivel']=='3'){
		$sql= $db->prepare("SELECT m.*, c.dni, c.nombre, cu.nombre as curso, cu.fecha as fechaCurso FROM `muestras` m
			inner join clientes c on c.id = m.idCliente
			inner join cursos cu on cu.id = m.idCurso		
			inner join usuarios u on u.usuNombre = me.dni
			where m.activo = 1 order by m.id desc;
		where m.activo = 1 and u.idUsuario = {$data['idUsuario']} order by m.id desc;");
	}else{
		$sql= $db->prepare("SELECT m.*, c.dni, c.nombre, cu.nombre as curso, cu.fecha as fechaCurso FROM `muestras` m
			inner join clientes c on c.id = m.idCliente
			inner join cursos cu on cu.id = m.idCurso	
		where m.activo = 1 order by m.id desc;");
	}
	
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas [] = $row;
	echo json_encode($filas);
}

function buscarPorFiltros($db, $data){
	$filas = [];
	$filtro = '';
	if($data['dni']) $filtro = " c.dni = '{$data['dni']}' and ";
	if($data['estado']) $filtro .= " m.estado = {$data['estado']} and ";
	if($data['certificado']) $filtro .= " cu.nombre like '%{$data['certificado']}%' and ";
	//echo $filtro; die();
	$sql= $db->prepare("SELECT m.*, c.dni, c.nombre, cu.nombre as curso, cu.fecha as fechaCurso FROM `muestras` m
		inner join clientes c on c.id = m.idCliente
    inner join cursos cu on cu.id = m.idCurso
	where {$filtro} m.activo = 1 order by m.id desc");
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas [] = $row;
	echo json_encode($filas);
}

function registrosDNI($db, $data){
	$cliente =[];
	$sqlCliente = $db->prepare("SELECT * from clientes where dni= ? limit 1; ") ;
	$sqlCliente->execute([$data['dni']]);
	$cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC); 

	$filas = [];
	$filtro = '';
	if($data['dni']) $filtro = " c.dni = '{$data['dni']}' and ";
	else die();
	//echo $filtro; die();
	$sql= $db->prepare("SELECT m.*, c.nombre, cu.nombre as curso, cu.fecha as fechaCurso FROM `muestras` m
		inner join clientes c on c.id = m.idCliente
    inner join cursos cu on cu.id = m.idCurso
	where {$filtro} m.activo = 1 order by cu.fecha desc");
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas [] = $row;
	echo json_encode(array('cliente'=> $cliente, 'muestras' => $filas	));
}

function registrosID($db, $data){
	$cliente =[];
	$sqlCliente = $db->prepare("SELECT * from clientes where id= ?; ") ;
	$sqlCliente->execute([$data['id']]);
	$cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC); 
	
	$filas = [];
	$filtro = '';
	if($data['id']) $filtro = " m.idCliente = '{$data['id']}' and ";
	else die();
	//echo $filtro; die();
	$sql= $db->prepare("SELECT m.*, c.nombre, c.dni, c.edad, s.sede, me.nombre as nombreMedico FROM `muestras` m
	inner join clientes c on c.id = m.idCliente
	inner join sedes s on s.id = m.idSede
	inner join medicos me on me.id = m.idMedico
	where {$filtro} m.activo = 1");
	$sql->execute();
	while($row= $sql->fetch(PDO::FETCH_ASSOC))
		$filas [] = $row;
	echo json_encode(array('cliente'=> $cliente, 'muestras' => $filas	));
}

function eliminar($db, $data){
	$sql = $db->prepare("UPDATE `muestras` set activo=0 where id = ? ");
	$sent = $sql->execute([ $data['id'] ]);
	if($sent) echo 'ok';
	else echo 'error';

}

?>