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

    public function guardar()
    {
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true); 

        $imagenEstructura = Imagen::fromArray($data['imagenEstructura']);
        $imagenGPS = Imagen::fromArray($data['imagenGPS']);
        $ubicacionUTM = CoordenadaUTM::fromArray($data['ubicacionUTM']);
        $ubicacionesDMS = CoordenadasDMS::fromArray($data['ubicacionDMS']);
        $ubicacionDMSLat = CoordenadaDMS::fromArray($data['ubicacionDMS']['latitud']);
        $ubicacionDMSLon = CoordenadaDMS::fromArray($data['ubicacionDMS']['longitud']);

        $responseImagenEstructura = $this->imagenModel->insertar($imagenEstructura->toArray());
        $responseImagenGPS = $this->imagenModel->insertar($imagenGPS->toArray());
        $responseUbicacionUTM = $this->coordenadaUTMModel->insertar($ubicacionUTM->toArray());

        $responseUbicacionDMSLat = $this->coordenadaDMSModel->insertar($ubicacionDMSLat->toArray());
        $responseUbicacionDMSLon = $this->coordenadaDMSModel->insertar($ubicacionDMSLon->toArray());



        if($responseImagenEstructura && $responseImagenGPS && $responseUbicacionUTM && $responseUbicacionDMSLat && $responseUbicacionDMSLon){
            
            $idLatitud = $this->coordenadaDMSModel->seleccionar("id", condiciones:"grados = '{$ubicacionDMSLat->grados}' AND minutos = '{$ubicacionDMSLat->minutos}' AND segundos = '{$ubicacionDMSLat->segundos}'");
            $idLongitud = $this->coordenadaDMSModel->seleccionar("id", condiciones:"grados = '{$ubicacionDMSLon->grados}' AND minutos = '{$ubicacionDMSLon->minutos}' AND segundos = '{$ubicacionDMSLon->segundos}'");

            $ubicacionesDMS->idLatitudDMS = $idLatitud;
            $ubicacionesDMS->idLongitudDMS = $idLongitud;

        $responseUbicacionesDMS = $this->coordenadasDMSModel->insertar($ubicacionesDMS->toArray());


            $idImagenEstructura = $this->imagenModel->seleccionar("idImagen", condiciones:"urlImagen = '{$imagenEstructura->idImagen}'");
            $idImagenGPS = $this->imagenModel->seleccionar("idImagen", condiciones:"urlImagen = '{$imagenGPS->idImagen}'");
            $idUbicacionUTM = $this->coordenadaUTMModel->seleccionar("idCoordenadaUTM", condiciones:"coordenadaX = '{$ubicacionUTM->coordenadaX}' AND coordenadaY = '{$ubicacionUTM->coordenadaY}'");
            $idUbicacionDMS= $this->coordenadaDMSModel->seleccionar("idCoordenadasDMS", condiciones:"latitud_id = '{$ubicacionesDMS->idLatitudDMS}' AND longitud_id = '{$ubicacionesDMS->idLongitudDMS}'");
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
    
            // Convertir a array para la base de datos
            $datosEnviar = $estructura->toArray();
            // var_dump($datosEnviar);
        
            // Actualizar en la base de datos
            $estructuras = $this->estructuraModel->actualizar($datosEnviar, condicion: "idEstructura = '{$estructura->idEstructura}'");
        
            return $estructuras ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
        }else {
            echo "Ocurrio un error";
        }
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

        $campos = "e.idEstructura, 
             e.nombre, 
             e.fechaRegistro, 
             e.estaCompleta, 
             e.idOperadorAsignado,
             e.idProyecto, 
             i.urlImagen AS urlImagenEstructura, 
             ig.urlImagen AS urlImagenGPS, 
             cu.coordenadaX, 
             cu.coordenadaY, 
             cu.zonaCartografica, 
             lat.grados AS latitudGrados, 
             lat.minutos AS latitudMinutos, 
             lat.segundos AS latitudSegundos, 
             lat.hemisferio AS latitudHemisferio, 
             lon.grados AS longitudGrados, 
             lon.minutos AS longitudMinutos, 
             lon.segundos AS longitudSegundos, 
             lon.hemisferio AS longitudHemisferio";
        
             $innerjoins = "e
              INNER JOIN 
                  imagen i ON e.imagenEstructura = i.idImagen
              LEFT JOIN 
                  imagen ig ON e.imagenGPS = ig.idImagen
              INNER JOIN 
                  coordenadautm cu ON e.ubicacionUTM = cu.idCoordenadaUTM
              INNER JOIN 
                  coordenadasdms cosdms ON cosdms.idCoordenadasDMS = e.ubicacionDMS
              INNER JOIN 
                  coordenadadms lat ON cosdms.latitud_id = lat.id
              INNER JOIN 
                  coordenadadms lon ON cosdms.longitud_id = lon.id";

        $condicion = "idEstructura = '$idEstructura'";

        $estructuras = $this->estructuraModel->seleccionar(campos: $campos, innerjoin: $innerjoins, condiciones: $condicion);

    if ($estructuras && !empty($estructuras)) {
        header('Content-Type: application/json');
        echo json_encode($estructuras[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function actualizar()
    {
        // Si se envían como JSON
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);
    
        // Si los datos se envían como formulario (application/x-www-form-urlencoded)
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

        $imagenEstructura = Imagen::fromArray($data['imagenEstructura']);
        $imagenGPS = Imagen::fromArray($data['imagenGPS']);
        $ubicacionUTM = CoordenadaUTM::fromArray($data['ubicacionUTM']);
        $ubicacionesDMS = CoordenadasDMS::fromArray($data['ubicacionDMS']);
        $ubicacionDMSLat = CoordenadaDMS::fromArray($data['ubicacionDMS']['latitud']);
        $ubicacionDMSLon = CoordenadaDMS::fromArray($data['ubicacionDMS']['longitud']);

        $responseImagenEstructura = $this->imagenModel->actualizar($imagenEstructura->toArray(), condicion:"idImagen = {$imagenEstructura->idImagen}");
        $responseImagenGPS = $this->imagenModel->actualizar($imagenGPS->toArray(), condicion:"idImagen = {$imagenGPS->idImagen}");
        $responseUbicacionUTM = $this->coordenadaUTMModel->actualizar($ubicacionUTM->toArray(), condicion:"idCoordenadaUTM = {$ubicacionUTM->idCoordenadaUTM}");
        // $responseUbicacionesDMS = $this->coordenadasDMSModel->actualizar($ubicacionesDMS->toArray(), condicion:"idCoordenadasDMS = {$ubicacionesDMS->idCoordenadasDMS}");
        $responseUbicacionesDMSLat = $this->coordenadaDMSModel->actualizar($ubicacionDMSLat->toArray(), condicion:"id = {$ubicacionDMSLat->id}");
        $responseUbicacionesDMSLon = $this->coordenadaDMSModel->actualizar($ubicacionDMSLon->toArray(), condicion:"id = {$ubicacionDMSLon->id}");

        // Convertir a array para la base de datos
        $datosEnviar = $estructura->toArray();
        // var_dump($datosEnviar);
    
        // Actualizar en la base de datos
        $estructuras = $this->estructuraModel->actualizar($datosEnviar, condicion: "idEstructura = '{$estructura->idEstructura}'");
    
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