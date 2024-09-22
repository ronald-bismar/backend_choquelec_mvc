<?php
require_once "model/entities/Estructura.php";
require_once "model/EstructuraModel.php";
require_once "model/entities/Imagen.php";
require_once "model/ImagenModel.php";
require_once "controller/ImagenController.php";
require_once "model/entities/CoordenadaUTM.php";
require_once "model/CoordenadaUTMModel.php";
require_once "controller/CoordenadaUTMController.php";
require_once "model/entities/CoordenadasDMS.php";
require_once "model/CoordenadasDMSModel.php";
require_once "controller/CoordenadasDMSController.php";
require_once "model/entities/CoordenadaDMS.php";
require_once "model/CoordenadaDMSModel.php";
require_once "controller/CoordenadaDMSController.php";

class EstructuraController
{
    private EstructuraModel $estructuraModel;
    private ImagenModel $imagenModel;
    private CoordenadaUTMModel $coordenadaUTMModel;
    private CoordenadasDMSModel $coordenadasDMSModel;
    private CoordenadaDMSModel $coordenadaDMSModel;

    public function __construct()
    {
        $this->estructuraModel = new EstructuraModel();
        $this->imagenModel = new ImagenModel();
        $this->coordenadaUTMModel = new CoordenadaUTMModel();
        $this->coordenadasDMSModel = new CoordenadasDMSModel();
        $this->coordenadaDMSModel = new CoordenadaDMSModel();
    }

    private function obtenerDatosJson(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function insertarUbicaciones(array $data): bool
    {
        $ubicacionesDMS = CoordenadasDMS::fromArray($data['ubicacionDMS']);
        $ubicacionDMSLat = CoordenadaDMS::fromArray($data['ubicacionDMS']['latitud']);
        $ubicacionDMSLon = CoordenadaDMS::fromArray($data['ubicacionDMS']['longitud']);

        $this->imagenModel->insertar(Imagen::fromArray($data['imagenEstructura'])->toArray());
        $this->imagenModel->insertar(Imagen::fromArray($data['imagenGPS'])->toArray());
        $this->coordenadaUTMModel->insertar(CoordenadaUTM::fromArray($data['ubicacionUTM'])->toArray());

        $this->coordenadaDMSModel->insertar($ubicacionDMSLat->toArray());
        $this->coordenadaDMSModel->insertar($ubicacionDMSLon->toArray());

        $idLatitud = $this->coordenadaDMSModel->seleccionar("id", "grados = '{$ubicacionDMSLat->grados}' AND minutos = '{$ubicacionDMSLat->minutos}' AND segundos = '{$ubicacionDMSLat->segundos}'");
        $idLongitud = $this->coordenadaDMSModel->seleccionar("id", "grados = '{$ubicacionDMSLon->grados}' AND minutos = '{$ubicacionDMSLon->minutos}' AND segundos = '{$ubicacionDMSLon->segundos}'");

        $ubicacionesDMS->idLatitudDMS = $idLatitud;
        $ubicacionesDMS->idLongitudDMS = $idLongitud;

        return $this->coordenadasDMSModel->insertar($ubicacionesDMS->toArray());
    }

    public function guardar()
    {
        $data = $this->obtenerDatosJson();

        if ($this->insertarUbicaciones($data)) {
            $estructura = new Estructura(
                $data['idEstructura'],
                $data['nombre'],
                $data['imagenEstructura']['idImagen'],
                $data['imagenGPS']['idImagen'],
                $data['ubicacionUTM']['idCoordenadaUTM'],
                $data['ubicacionDMS']['idCoordenadasDMS'],
                $data['estaCompleta'], 
                $data['fechaRegistro'], 
                $data['idProyecto'], 
                $data['idOperadorAsignado']
            );

            return $this->estructuraModel->actualizar($estructura->toArray(), "idEstructura = '{$estructura->idEstructura}'") 
                ? "Registro actualizado correctamente" 
                : "Error al actualizar el registro.";
        }

        return "Ocurrió un error.";
    }

    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'estaCompleta';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';

        $estructuras = $this->estructuraModel->seleccionar("$tipo = '$valorBuscado'");
        header('Content-Type: application/json');
        echo json_encode($estructuras ?: null);
    }

    public function obtenerEstructura()
    {
        $idEstructura = $_POST['idEstructura'] ?? '1';

        $campos = "e.idEstructura, e.nombre, e.fechaRegistro, e.estaCompleta, e.idOperadorAsignado, e.idProyecto, 
                  i.urlImagen AS urlImagenEstructura, ig.urlImagen AS urlImagenGPS, cu.coordenadaX, cu.coordenadaY, 
                  cu.zonaCartografica, lat.grados AS latitudGrados, lat.minutos AS latitudMinutos, lat.segundos AS latitudSegundos, 
                  lat.hemisferio AS latitudHemisferio, lon.grados AS longitudGrados, lon.minutos AS longitudMinutos, lon.segundos AS longitudSegundos, lon.hemisferio AS longitudHemisferio";

        $joins = "e INNER JOIN imagen i ON e.imagenEstructura = i.idImagen
                  LEFT JOIN imagen ig ON e.imagenGPS = ig.idImagen
                  INNER JOIN coordenadautm cu ON e.ubicacionUTM = cu.idCoordenadaUTM
                  INNER JOIN coordenadasdms cosdms ON cosdms.idCoordenadasDMS = e.ubicacionDMS
                  INNER JOIN coordenadadms lat ON cosdms.latitud_id = lat.id
                  INNER JOIN coordenadadms lon ON cosdms.longitud_id = lon.id";

        $estructuras = $this->estructuraModel->seleccionar($campos, $joins, "idEstructura = '$idEstructura'");
        header('Content-Type: application/json');
        echo json_encode($estructuras[0] ?? null);
    }

    public function actualizar()
    {
        $data = $this->obtenerDatosJson();

        $estructura = new Estructura(
            $data['idEstructura'],
            $data['nombre'],
            $data['imagenEstructura']['idImagen'],
            $data['imagenGPS']['idImagen'],
            $data['ubicacionUTM']['idCoordenadaUTM'],
            $data['ubicacionDMS']['idCoordenadasDMS'],
            $data['estaCompleta'], 
            $data['fechaRegistro'], 
            $data['idProyecto'], 
            $data['idOperadorAsignado']
        );

        $this->imagenModel->actualizar(Imagen::fromArray($data['imagenEstructura'])->toArray(), "idImagen = '{$estructura->imagenEstructura}'");
        $idImagen = $data['imagenGPS']['idImagen'];
        $this->imagenModel->actualizar(Imagen::fromArray($data['imagenGPS'])->toArray(), "idImagen = '{$estructura->imagenGPS}'");
        $this->coordenadaUTMModel->actualizar(CoordenadaUTM::fromArray($data['ubicacionUTM'])->toArray(), "idCoordenadaUTM = '{$estructura->ubicacionUTM}'");

        $idLatitud = $data['ubicacionDMS']['latitud']['id'];
        $this->coordenadaDMSModel->actualizar(CoordenadaDMS::fromArray($data['ubicacionDMS']['latitud']),"id = '$idLatitud'");

        $idLongitud = $data['ubicacionDMS']['longitud']['id'];
        $this->coordenadaDMSModel->actualizar(CoordenadaDMS::fromArray($data['ubicacionDMS']['longitud']),"id = '$idLongitud'");

        return $this->estructuraModel->actualizar($estructura->toArray(), "idEstructura = '{$estructura->idEstructura}'")
            ? "Registro actualizado correctamente" 
            : "Error al actualizar el registro.";
    }

    public function eliminar()
    {
        $idEstructura = $_POST['idEstructura'] ?? '';
        return $this->estructuraModel->eliminar("idEstructura = '$idEstructura'")
            ? "Registro eliminado correctamente" 
            : "Error al eliminar el registro.";
    }

    public function listar()
    {
        $estructuras = $this->estructuraModel->seleccionar();
        header('Content-Type: application/json');
        echo json_encode($estructuras ?: null);
    }
}
