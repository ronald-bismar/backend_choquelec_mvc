<?php
require_once "model/entities/Trabajador.php";
require_once "model/TrabajadorModel.php";

class TrabajadorController
{
    private TrabajadorModel $trabajadorModelo;

    public function __construct()
    {
        $this->trabajadorModelo = new TrabajadorModel();
    }

    public function guardar()
    {
        $trabajador = new Trabajador(
            $_POST['nombre'] ?? '',
            $_POST['apellido'] ?? '',
            $_POST['contrasenia'] ?? '',
            $_POST['tipoDeTrabajador'] ?? '',
            $_POST['cedulaDeIdentidad'] ?? ''
        );

        $datosEnviar = $trabajador->toArray();
        $respuesta = $this->trabajadorModelo->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    public function autenticar()
    {
        $trabajador = new Trabajador(
            contrasenia: $_POST['contrasenia'] ?? '',
            cedulaDeIdentidad: $_POST['cedulaDeIdentidad'] ?? ''
        );

        $respuesta = $this->trabajadorModelo->seleccionar(
            condiciones: 
            "cedulaDeIdentidad = '$trabajador->cedulaDeIdentidad'
             AND 
            contrasenia = '$trabajador->contrasenia'");

    if ($respuesta && !empty($respuesta)) {
        header('Content-Type: application/json');
        echo json_encode($respuesta[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'idTrabajador';
        $valor = $_POST['valor'] ?? '0';
        
        $trabajadores = $this->trabajadorModelo->seleccionar(condiciones: "$tipo = '$valor'");

    if ($trabajadores) {
        // var_dump($trabajadores);
        header('Content-Type: application/json');
        echo json_encode($trabajadores);
    } else {
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerTrabajador(){
        $idTrabajador = $_POST['idTrabajador'];

        $trabajadores = $this->trabajadorModelo->seleccionar(condiciones: "idTrabajador = '$idTrabajador'");

    if ($trabajadores && !empty($trabajadores)) {
        header('Content-Type: application/json');
        echo json_encode($trabajadores[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
    public function actualizar()
    {
        $trabajador = new Trabajador(
            $_POST['idTrabajador'],
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['cedulaDeIdentidad'],
            $_POST['contrasenia'],
            $_POST['tipoDeTrabajador'],
            $_POST['activo'], 
        );

        $datosEnviar = $trabajador->toArray();
        $trabajadores = $this->trabajadorModelo->actualizar($datosEnviar, "idTrabajador = '{$trabajador->idTrabajador}'");

        return $trabajadores ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function eliminar()
    {
        $idTrabajador = $_POST['idTrabajador'] ?? '0';
        
        $respuesta = $this->trabajadorModelo->eliminar(condiciones: "idTrabajador = '$idTrabajador'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $trabajadores = $this->trabajadorModelo->seleccionar(condiciones: "activo = '1'");

    if ($trabajadores) {
        // var_dump($trabajadores);
        header('Content-Type: application/json');
        echo json_encode($trabajadores);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
}