<?php
require_once "model/entities/CoordenadaUTM.php";
require_once "model/CoordenadaUTMModel.php";

class CoordenadaUTMController {
    private CoordenadaUTMModel $coordenadaUTMModel;

    public function __construct() {
        $this->coordenadaUTMModel = new CoordenadaUTMModel();
    }

    public function guardar() {
        $data = $_POST;
        $coordenadaUTM = new CoordenadaUTM(
            idCoordenadaUTM: $data['idCoordenadaUTM'] ?? null,
            coordenadaX: $data['coordenadaX'] ?? '0.0',
            coordenadaY: $data['coordenadaY'] ?? '0.0',
            zonaCartografica: $data['zonaCartografica'] ?? '19K'
        );

        $respuesta = $this->coordenadaUTMModel->insertar($coordenadaUTM->toArray());
        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }

    public function obtenerCoordenadaUTM() {
        $idCoordenadaUTM = $_POST['idCoordenadaUTM'] ?? '1';
        $condicion = "idCoordenadaUTM = '$idCoordenadaUTM'";
        $coordenadaUTM = $this->coordenadaUTMModel->seleccionar(condiciones: $condicion);

        $this->sendJsonResponse($coordenadaUTM[0] ?? null);
    }

    public function actualizar() {
        $data = json_decode(file_get_contents('php://input'), true);
        $coordenadaUTM = CoordenadaUTM::fromArray($data ?? []);
        $coordenadaUTM->idCoordenadaUTM = $data['idCoordenadaUTM'] ?? '1';

        $respuesta = $this->coordenadaUTMModel->actualizar(
            $coordenadaUTM->toArray(),
            condicion: "idCoordenadaUTM = '{$coordenadaUTM->idCoordenadaUTM}'"
        );

        return $respuesta ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }

    public function eliminar() {
        $idCoordenadaUTM = $_POST['idCoordenadaUTM'] ?? '5';
        $respuesta = $this->coordenadaUTMModel->eliminar(condiciones: "idCoordenadaUTM = '$idCoordenadaUTM'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar() {
        $coordenadaUTM = $this->coordenadaUTMModel->seleccionar();
        $this->sendJsonResponse($coordenadaUTM);
    }

    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}