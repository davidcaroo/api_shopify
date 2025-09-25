<?php
// Cargar las variables de entorno
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Función helper para obtener variables de entorno
function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}
