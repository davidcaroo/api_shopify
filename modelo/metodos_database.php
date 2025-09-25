<?php


function agregar($consulta, $datos_agregar)
{
	$respuesta = "";

	$sql = $consulta;
	$db = new db();
	$db = $db->conectar_db();
	$stmt = $db->prepare($sql);

	try {
		$stmt->execute($datos_agregar);
		$respuesta = $db->lastInsertId();
	} catch (Exception $e) {
		$respuesta = $e;
	}

	return $respuesta;
}

function actualizar($consulta, $datos_actualizar)
{
	$respuesta = "";

	$sql = $consulta;
	$db = new db();
	$db = $db->conectar_db();
	$stmt = $db->prepare($sql);

	try {
		$stmt->execute($datos_actualizar);
		$respuesta = '';
	} catch (Exception $e) {
		$respuesta = $e;
	}

	return $respuesta;
}

function borrar($consulta, $datos_borrar)
{
	$respuesta = "";

	$sql = $consulta;
	$db = new db();
	$db = $db->conectar_db();
	$stmt = $db->prepare($sql);

	try {
		$stmt->execute($datos_borrar);
		$respuesta = '';
	} catch (Exception $e) {
		$respuesta = $e;
	}

	return $respuesta;
}



function obtener_datos_en_array($consulta)
{
	$respuesta = array();
	try {

		$db = new db();
		$db = $db->conectar_db();

		$resultado = $db->query($consulta);

		if ($resultado->rowCount() > 0) {
			$clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
			$respuesta = $clientes;
		} else {
			$respuesta = array();
		}

		$resultado = null;
		$db = null;
	} catch (PDOException $e) {
		$respuesta = $e->getMessage();
	}

	return $respuesta;
}
