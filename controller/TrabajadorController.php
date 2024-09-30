<?php

require_once "model/entities/Trabajador.php";
require_once "model/TrabajadorModel.php";

class TrabajadorController
{
    private TrabajadorModel $trabajadorModel;

    public function __construct()
    {
        $this->trabajadorModel = new TrabajadorModel();
    }

    public function guardar()
    {
        $trabajador = $this->createTrabajadorFromPost();
        $result = $this->trabajadorModel->insertar($trabajador->toArray());
        return $result ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }

    public function autenticar()
    {
        $cedula = $_POST['cedulaDeIdentidad'] ?? '';
        $contrasenia = $_POST['contrasenia'] ?? '';
        $condiciones = "cedulaDeIdentidad = '$cedula' AND contrasenia = '$contrasenia'";
        $respuesta = $this->trabajadorModel->seleccionar(condiciones: $condiciones);
        $this->sendJsonResponse($respuesta[0] ?? null);
    }

    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'idTrabajador';
        $valor = $_POST['valor'] ?? '0';
        $trabajadores = $this->trabajadorModel->seleccionar(condiciones: "$tipo = '$valor'");
        $this->sendJsonResponse($trabajadores);
    }

    public function obtenerTrabajador()
    {
        $idTrabajador = $_POST['idTrabajador'];
        $trabajadores = $this->trabajadorModel->seleccionar(condiciones: "idTrabajador = '$idTrabajador'");
        $this->sendJsonResponse($trabajadores[0] ?? null);
    }

    public function actualizar()
    {
        $trabajador = $this->createTrabajadorFromPost();
        $result = $this->trabajadorModel->actualizar($trabajador->toArray(), "idTrabajador = '{$trabajador->idTrabajador}'");
        return $result ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }

    public function eliminar()
    {
        $idTrabajador = $_POST['idTrabajador'] ?? '0';
        $result = $this->trabajadorModel->actualizar(valoresEntrada: ['activo' => 0],condicion: "idTrabajador = '$idTrabajador'");
        return $result ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {
        $trabajadores = $this->trabajadorModel->seleccionar();
        $this->sendJsonResponse($trabajadores);
    }

    private function createTrabajadorFromPost(): Trabajador
    {
        $trabajador = new Trabajador(
            nombre: $_POST['nombre'] ?? '',
            apellido: $_POST['apellido'] ?? '',
            cedulaDeIdentidad: $_POST['cedulaDeIdentidad'] ?? '',
            contrasenia: $_POST['contrasenia'] ?? '',
            tipoDeTrabajador: $_POST['tipoDeTrabajador'] ?? '',
            activo: $_POST['activo'] ?? '1'
        );

        if($_POST['idTrabajador'] != null && $_POST['idTrabajador'] != ''){
            $trabajador->idTrabajador = $_POST['idTrabajador'];
        }
        return $trabajador;
    }

    private function sendJsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}