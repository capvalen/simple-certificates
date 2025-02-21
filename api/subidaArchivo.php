<?php
require("conectInfocat.php");

$uploadDir ="../subidas/";
$data = json_decode(file_get_contents('php://input'), true);

if (isset($_FILES['pdf'])){

	$fileInfo = pathinfo($_FILES['pdf']['name']);
	$extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
	$uniqueFilename = uniqid() . '.' . $extension;
	$uploadFile = $uploadDir . $uniqueFilename;

	if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadFile)) {
		//echo "El archivo ha sido subido correctamente con el nombre: " . $uniqueFilename;

		// ------- Guardar el registro en la DB
		$sql = $datab->prepare("UPDATE muestras set adjunto = ?, estado = 2 where id = ?; ");
		$sql->execute([ $uniqueFilename, $_POST['id'] ]);
		echo json_encode( array('subida' => $uniqueFilename, 'estado' => 'ok'));
		
	} else {
		$error = error_get_last();
		echo json_encode( array('estado' => "error ". $error['message']));
	}

}else{
	echo json_encode( array('estado' => "no se encontró el archivo "));
}