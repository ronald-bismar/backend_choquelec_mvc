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
        $result = $this->trabajadorModel->eliminar(condiciones: "idTrabajador = '$idTrabajador'");
        return $result ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {
        $trabajadores = $this->trabajadorModel->seleccionar(condiciones: "activo = '1'");
        $this->sendJsonResponse($trabajadores);
    }

    private function createTrabajadorFromPost(): Trabajador
    {
        return new Trabajador(
            $_POST['idTrabajador'] ?? null,
            $_POST['nombre'] ?? '',
            $_POST['apellido'] ?? '',
            $_POST['cedulaDeIdentidad'] ?? '',
            $_POST['contrasenia'] ?? '',
            $_POST['tipoDeTrabajador'] ?? '',
            $_POST['activo'] ?? '1'
        );
    }

    private function sendJsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}