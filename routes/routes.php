<?php 
require_once "controller/TrabajadorController.php";

$controlador = $_GET['c'] ?? 'Principal';
$metodo = $_GET['m'] ?? 'inicio';

try {
    $objeto = new $controlador();
    $objeto->$metodo();
} catch (Error | Exception $e) {
    echo "Error: " . $e->getMessage();
}