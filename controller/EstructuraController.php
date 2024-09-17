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
    
        // Convertir a array para la base de datos
        $datosEnviar = $estructura->toArray();
        var_dump($datosEnviar);
    
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