<?php

// Cargar configuración y variables de entorno
require_once 'config.php';

require_once('DB.php');   //Consultas con PDO
require "modelo/metodos_database.php";
require "modelo/metodos_generales.php";
require_once "APIS/MetodosAPI.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

$MetodosAPI = new MetodosAPI();
$MetodosAPI->API();
