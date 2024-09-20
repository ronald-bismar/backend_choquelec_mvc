<?php
require_once "model/entities/CoordenadasDMS.php";
require_once "model/CoordenadasDMSModel.php";
require_once "model/entities/CoordenadaDMS.php";
require_once "model/CoordenadaDMSModel.php";

class CoordenadasDMSController {
    private CoordenadasDMSModel $coordenadasDMSModel;
    private CoordenadaDMSModel $coordenadaDMSModel;

    public function __construct() {
        $this->coordenadasDMSModel = new CoordenadasDMSModel();
        $this->coordenadaDMSModel = new CoordenadaDMSModel();
    }

    public function guardar() {
        $data = json_decode(file_get_contents('php://input'), true);

        $latitud = CoordenadaDMS::fromArray($data['latitud'] ?? []);
        $longitud = CoordenadaDMS::fromArray($data['longitud'] ?? []);

        $responseLatitud = $this->coordenadaDMSModel->insertar($latitud->toArray());
        $responseLongitud = $this->coordenadaDMSModel->insertar($longitud->toArray());

        if ($responseLatitud && $responseLongitud) {
            $idLatitud = $this->coordenadaDMSModel->seleccionar(campos: 'id', condiciones: $this->buildCondition($latitud, 'latitud'));
            $idLongitud = $this->coordenadaDMSModel->seleccionar(campos: 'id', condiciones: $this->buildCondition($longitud, 'longitud'));

            $coordenadasDMS = new CoordenadasDMS(null, $idLatitud[0]['id'], $idLongitud[0]['id']);
            $respuesta = $this->coordenadasDMSModel->insertar($coordenadasDMS->toArray());

            return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
        }
        return "Error al insertar las coordenadas.";
    }

    public function buscarPor() {
        $tipo = $_POST['tipo'] ?? 'idCoordenadasDMS';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';

        $coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

        $coordenadas = $this->procesarCoordenadas($coordenadasDMSEncontradas);

        $this->sendJsonResponse($coordenadas);
    }

    public function obtenerCoordenadasDMS() {
        $idCoordenadasDMS = $_POST['idCoordenadasDMS'] ?? '1';
        $coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar(condiciones: "idCoordenadasDMS = '$idCoordenadasDMS'");

        $coordenadas = $this->procesarCoordenadas($coordenadasDMSEncontradas);

        $this->sendJsonResponse($coordenadas);
    }

    public function actualizar() {
        $data = json_decode(file_get_contents('php://input'), true);

        $coordenadasDMS = new CoordenadasDMS(
            $data['idCoordenadasDMS'] ?? '1',
            $data['latitud']['id'] ?? '20',
            $data['longitud']['id'] ?? '21'
        );

        $coordenadaDMSLat = CoordenadaDMS::fromArray($data['latitud'] ?? []);
        $coordenadaDMSLon = CoordenadaDMS::fromArray($data['longitud'] ?? []);

        $responseCordenadaDMSLat = $this->coordenadaDMSModel->actualizar($coordenadaDMSLat->toArray(), condicion: "id = '{$coordenadaDMSLat->id}'");
        $responseCordenadaDMSLon = $this->coordenadaDMSModel->actualizar($coordenadaDMSLon->toArray(), condicion: "id = '{$coordenadaDMSLon->id}'");

        if($responseCordenadaDMSLat && $responseCordenadaDMSLon){
            $responseCordenadasDMS = $this->coordenadasDMSModel->actualizar($coordenadasDMS->toArray(), condicion: "idCoordenadasDMS = '{$coordenadasDMS->idCoordenadasDMS}'");

        return $responseCordenadasDMS ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
        }else {
            return "Error al actualizar las coordenadas.";
        }     
    }

    public function eliminar() {
        $idCoordenadasDMS = $_POST['idCoordenadasDMS'] ?? '10';
        $respuesta = $this->coordenadasDMSModel->eliminar(condiciones: "idCoordenadasDMS = '$idCoordenadasDMS'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar() {
        $coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar();
        $coordenadas = $this->procesarCoordenadas($coordenadasDMSEncontradas);
        $this->sendJsonResponse($coordenadas);
    }

    private function procesarCoordenadas($coordenadasDMSEncontradas) {
        if (!$coordenadasDMSEncontradas) return null;

        $coordenadas = [];
        foreach ($coordenadasDMSEncontradas as $coordenada) {
            $latitud = $this->obtenerCoordenadaDMS($coordenada['latitud_id']);
            $longitud = $this->obtenerCoordenadaDMS($coordenada['longitud_id']);

            $coordenadas[] = [
                "idCoordenadasDMS" => $coordenada['idCoordenadasDMS'],
                "latitud" => $latitud->toArray(),
                "longitud" => $longitud->toArray(),
            ];
        }
        return $coordenadas;
    }

    private function obtenerCoordenadaDMS($id) {
        $coordenadaDMS = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$id'")[0];
        return new CoordenadaDMS(
            $coordenadaDMS['id'],
            $coordenadaDMS['grados'],
            $coordenadaDMS['minutos'],
            $coordenadaDMS['segundos'],
            $coordenadaDMS['hemisferio'],
            $coordenadaDMS['tipo']
        );
    }

    private function buildCondition($coordenada, $tipo) {
        return "grados = '{$coordenada->grados}' AND minutos = '{$coordenada->minutos}' AND segundos = '{$coordenada->segundos}' AND tipo = '$tipo'";
    }

    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}