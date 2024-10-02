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
        $idLatitud = $this->coordenadaDMSModel->seleccionar(campos: "id",condiciones: "grados = '{$ubicacionDMSLat->grados}' AND minutos = '{$ubicacionDMSLat->minutos}' AND segundos = '{$ubicacionDMSLat->segundos}'");
        $idLongitud = $this->coordenadaDMSModel->seleccionar(campos: "id",condiciones: "grados = '{$ubicacionDMSLon->grados}' AND minutos = '{$ubicacionDMSLon->minutos}' AND segundos = '{$ubicacionDMSLon->segundos}'");

        $ubicacionesDMS->idLatitudDMS = $idLatitud[0]['id'];
        $ubicacionesDMS->idLongitudDMS = $idLongitud[0]['id'];

        

        return $this->coordenadasDMSModel->insertar($ubicacionesDMS->toArray());
    }

    private function insertarYObtenerIdImagen(array $imagenData): int
    {
        $imagen = Imagen::fromArray($imagenData);
        $this->imagenModel->insertar($imagen->toArray());
        $resultado = $this->imagenModel->seleccionar(campos: "idImagen",condiciones: "urlImagen = '{$imagen->urlImagen}'");
        return $resultado[0]['idImagen'];
    }

    private function insertarYObtenerIdCoordenadaUTM(array $utmData): int
    {
        $utm = CoordenadaUTM::fromArray($utmData);
        $this->coordenadaUTMModel->insertar($utm->toArray());
        $resultado = $this->coordenadaUTMModel->seleccionar(campos:"idCoordenadaUTM", condiciones: "coordenadaX = '{$utm->coordenadaX}' AND coordenadaY = '{$utm->coordenadaY}' AND zonaCartografica = '{$utm->zonaCartografica}'");
        return $resultado[0]['idCoordenadaUTM'];
    }

    private function insertarYObtenerIdCoordenadasDMS(array $dmsData): int
    {
        $latitud = CoordenadaDMS::fromArray($dmsData['latitud']);
        $longitud = CoordenadaDMS::fromArray($dmsData['longitud']);
        
        $this->coordenadaDMSModel->insertar($latitud->toArray());
        $this->coordenadaDMSModel->insertar($longitud->toArray());
        
        $idLatitud = $this->coordenadaDMSModel->seleccionar(campos:"id", condiciones: "grados = '{$latitud->grados}' AND minutos = '{$latitud->minutos}' AND segundos = '{$latitud->segundos}'");
        $idLongitud = $this->coordenadaDMSModel->seleccionar(campos:"id",condiciones: "grados = '{$longitud->grados}' AND minutos = '{$longitud->minutos}' AND segundos = '{$longitud->segundos}'");
        
        $coordenadasDMS = new CoordenadasDMS(null, $idLatitud[0]['id'], $idLongitud[0]['id']);
        $this->coordenadasDMSModel->insertar($coordenadasDMS->toArray());
        
        $idLatitud = $idLatitud[0]['id'];
        $idLongitud = $idLongitud[0]['id'];

        $resultado = $this->coordenadasDMSModel->seleccionar(campos:"idCoordenadasDMS", condiciones: "latitud_id = '$idLatitud' AND longitud_id = '$idLongitud'");
        return $resultado[0]['idCoordenadasDMS'];
    }

    public function guardar()
    {
        $data = $this->obtenerDatosJson();

        try {
            // Insertar y obtener IDs de imágenes
            $idImagenEstructura = $this->insertarYObtenerIdImagen($data['imagenEstructura']);
            $idImagenGPS = $this->insertarYObtenerIdImagen($data['imagenGPS']);

            // Insertar y obtener ID de coordenada UTM
            $idCoordenadaUTM = $this->insertarYObtenerIdCoordenadaUTM($data['ubicacionUTM']);

            // Insertar y obtener ID de coordenadas DMS
            $idCoordenadasDMS = $this->insertarYObtenerIdCoordenadasDMS($data['ubicacionDMS']);

            // Crear y guardar la estructura
            $estructura = new Estructura(
                null,
                $data['nombre'],
                $idImagenEstructura,
                $idImagenGPS,
                $idCoordenadaUTM,
                $idCoordenadasDMS,
                $data['estaCompleta'],
                $data['fechaRegistro'] == ''? date("Y-m-d H:i:s"): $data['fechaRegistro'],
                $data['idProyecto'],
                $data['idOperadorAsignado']
            );


            if ($this->estructuraModel->insertar($estructura->toArray())) {
                return "Registro insertado correctamente";
            } else {
                throw new Exception("Error al insertar el registro de la estructura.");
            }
        } catch (Exception $e) {
            // Aquí podrías implementar un rollback de las inserciones previas si es necesario
            return "Ocurrió un error: " . $e->getMessage();
        }
    }
    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'estaCompleta';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';

        $estructuras = $this->estructuraModel->seleccionar("*", condiciones:"$tipo = '$valorBuscado'");
        header('Content-Type: application/json');
        echo json_encode($estructuras ?: null);
    }
    public function buscarUltimaEstructura() // Afinar este metodo
    {
        $tipo = $_POST['tipo'] ?? 'estaCompleta';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';

        $estructuras = $this->estructuraModel->seleccionar("*", condiciones:"$tipo = '$valorBuscado'");
        header('Content-Type: application/json');
        echo json_encode($estructuras[0] ?? null);
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

    public function obtenerUltimaEstructuraDeTrabajador()
{
    // Obtener el ID del trabajador desde el POST, si no existe se usa un valor predeterminado (1)
    $idTrabajador = $_POST['idOperadorAsignado'] ?? '1';

    // Seleccionar los campos necesarios
    $campos = "e.idEstructura, e.nombre, e.fechaRegistro, e.estaCompleta, e.idOperadorAsignado, e.idProyecto, 
              i.urlImagen AS urlImagenEstructura, ig.urlImagen AS urlImagenGPS, cu.coordenadaX, cu.coordenadaY, 
              cu.zonaCartografica, lat.grados AS latitudGrados, lat.minutos AS latitudMinutos, lat.segundos AS latitudSegundos, 
              lat.hemisferio AS latitudHemisferio, lon.grados AS longitudGrados, lon.minutos AS longitudMinutos, lon.segundos AS longitudSegundos, lon.hemisferio AS longitudHemisferio";

    // Definir los JOIN necesarios
    $joins = "e INNER JOIN imagen i ON e.imagenEstructura = i.idImagen
              LEFT JOIN imagen ig ON e.imagenGPS = ig.idImagen
              INNER JOIN coordenadautm cu ON e.ubicacionUTM = cu.idCoordenadaUTM
              INNER JOIN coordenadasdms cosdms ON cosdms.idCoordenadasDMS = e.ubicacionDMS
              INNER JOIN coordenadadms lat ON cosdms.latitud_id = lat.id
              INNER JOIN coordenadadms lon ON cosdms.longitud_id = lon.id";

    // Condición para seleccionar las estructuras del trabajador
    $condiciones = "e.idOperadorAsignado = '$idTrabajador'";

    // Ordenar por la fecha de registro de forma descendente y limitar a la primera fila
    $orden = "e.fechaRegistro DESC";
    $limite = "1";

    // Realizar la consulta
    $estructuras = $this->estructuraModel->seleccionar($campos, $joins, $condiciones, ordenamiento: $orden, limite: $limite);

    // Devolver la estructura en formato JSON
    header('Content-Type: application/json');
    echo json_encode($estructuras[0] ?? null);
}    
}
