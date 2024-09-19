<?php
require_once "model/entities/CoordenadaDMS.php";
require_once "model/CoordenadaDMSModel.php";

class CoordenadaDMSController {
    private CoordenadaDMSModel $model;

    public function __construct() {
        $this->model = new CoordenadaDMSModel();
    }

    public function guardar() {
        $coordenada = new CoordenadaDMS(
            id: $_POST['id'] ?? null,
            grados: $_POST['grados'] ?? '0',
            minutos: $_POST['minutos'] ?? '0',
            segundos: $_POST['segundos'] ?? '0',
            hemisferio: $_POST['hemisferio'] ?? 'N',
            tipo: $_POST['tipo'] ?? 'latitud'
        );

        return $this->model->insertar($coordenada->toArray())
            ? "Registro insertado correctamente"
            : "Error al insertar el registro.";
    }

    public function obtener() {
        $id = $_POST['id'] ?? '1';
        $coordenada = $this->model->seleccionar("id = '$id'");

        header('Content-Type: application/json');
        echo json_encode($coordenada[0] ?? null);
    }

    public function actualizar() {
        $data = json_decode(file_get_contents('php://input'), true);
        $coordenada = CoordenadaDMS::fromArray($data);

        return $this->model->actualizar($coordenada->toArray(), "id = '{$coordenada->id}'")
            ? "Registro actualizado correctamente"
            : "Error al actualizar el registro.";
    }

    public function eliminar() {
        $id = $_POST['id'] ?? '2';
        return $this->model->eliminar("id = '$id'")
            ? "Registro eliminado correctamente"
            : "Error al eliminar el registro.";
    }

    public function listar() {
        header('Content-Type: application/json');
        echo json_encode($this->model->seleccionar() ?: null);
    }
}