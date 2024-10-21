<?php 
require_once "controller/TrabajadorController.php";
require_once "controller/ProyectoController.php";
require_once "controller/EstructuraController.php";
require_once "controller/ImagenController.php";
require_once "controller/CoordenadaDMSController.php";
require_once "controller/CoordenadasDMSController.php"; //Latitud y longitud
require_once "controller/CoordenadaUTMController.php";
require_once "controller/NotificacionController.php";

$controlador = $_GET['c'] ?? 'Principal';
$metodo = $_GET['m'] ?? 'inicio';

try {
    $objeto = new $controlador();
    $objeto->$metodo();
} catch (Error | Exception $e) {
    echo "Error: " . $e->getMessage();
}