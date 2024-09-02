<?php
require_once "model/entities/Estructura.php";
require_once "model/EstructuraModel.php";

class EstructuraController
{
    private EstructuraModel $estructuraModel;

    public function __construct()
    {
        $this->estructuraModel = new EstructuraModel();
    }

    public function guardar()
    {
        $estructura = new Estructura(
           nombre: $_POST['nombre'] ?? '',
           imagenEstructura: $_POST['imagenEstructura'] ?? '2',
           imagenGPS: $_POST['imagenGPS'] ?? '2',
           ubicacionUTM: $_POST['ubicacionUTM'] ?? '2',
           ubicacionDMS: $_POST['ubicacionDMS'] ?? '3',
           estaCompleta: $_POST['estaCompleta'] ?? '0', 
           fechaRegistro: $_POST['fechaRegistro'] ?? date("Y-m-d H:i:s"), 
          idEstructura:  $_POST['idEstructura'] ?? '1', 
          idProyecto:  $_POST['idProyecto'] ?? '1', 
           idOperadorAsignado: $_POST['idOperadorAsignado']?? '6', 
        );

        $datosEnviar = $estructura->toArray();
        $estructuras = $this->estructuraModel->insertar($datosEnviar);

        return $estructuras ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'estaCompleta';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
        $estructuras = $this->estructuraModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    if ($estructuras) {
        // var_dump($estructuras);
        header('Content-Type: application/json');
        echo json_encode($estructuras);
    } else {
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerEstructura(){
        $idEstructura = $_POST['idEstructura']?? '1';

        $respuesta = $this->estructuraModel->seleccionar(condiciones: "idEstructura = '$idEstructura'");

    if ($respuesta && !empty($respuesta)) {
        header('Content-Type: application/json');
        echo json_encode($respuesta[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
    public function actualizar()
    {
        $estructura = new Estructura(
            $_POST['idEstructura'],
            $_POST['nombre'],
            $_POST['imagenEstructura'],
            $_POST['imagenGPS'],
            $_POST['ubicacionUTM'],
            $_POST['ubicacionDMS'],
            $_POST['estaCompleta'], 
            $_POST['fechaRegistro'], 
            $_POST['idProyecto'], 
            $_POST['idOperadorAsignado'], 
        );

        $datosEnviar = $estructura->toArray();
        $estructuras = $this->estructuraModel->actualizar($datosEnviar,condicion: "idEstructura = '{$estructura->idEstructura}'");

        return $estructuras ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function eliminar()
    {
        $idEstructura = $_POST['idEstructura'] ?? '';
        
        $respuesta = $this->estructuraModel->eliminar(condiciones: "idEstructura = '$idEstructura'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $estructuras = $this->estructuraModel->seleccionar();

    if ($estructuras) {
        // var_dump($estructuras);
        header('Content-Type: application/json');
        echo json_encode($estructuras);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
} 