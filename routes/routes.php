<?php 
require_once "controller/TrabajadorController.php";
require_once "controller/ProyectoController.php";
require_once "controller/EstructuraController.php";

$controlador = $_GET['c'] ?? 'Principal';
$metodo = $_GET['m'] ?? 'inicio';

try {
    $objeto = new $controlador();
    $objeto->$metodo();
} catch (Error | Exception $e) {
    echo "Error: " . $e->getMessage();
}