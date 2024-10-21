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

    public function guardar()
    {
        $data = $this->obtenerDatosJson();
    
        try {
            // Prepara los datos en un array
            $estructuraData = [
                $data['nombre'],
                $data['imagenEstructura']['urlImagen'],
                $data['imagenGPS']['urlImagen'],
                $data['ubicacionUTM']['coordenadaX'],
                $data['ubicacionUTM']['coordenadaY'],
                $data['ubicacionUTM']['zonaCartografica'],
                $data['ubicacionDMS']['latitud']['grados'],
                $data['ubicacionDMS']['latitud']['minutos'],
                $data['ubicacionDMS']['latitud']['segundos'],
                $data['ubicacionDMS']['latitud']['hemisferio'],
                $data['ubicacionDMS']['longitud']['grados'],
                $data['ubicacionDMS']['longitud']['minutos'],
                $data['ubicacionDMS']['longitud']['segundos'],
                $data['ubicacionDMS']['longitud']['hemisferio'],
                $data['estaCompleta'],
                $data['idProyecto'],
                $data['idOperadorAsignado']?? null
            ];
                
    // $ejemploData = ['Motor de bombeo de agua','http://res.cloudinary.com/dt5rqpa84
    // /image/upload/v1728571183/public/cxjwtgfixwq3gzzurm5t.jpg','http://res.cloudinary.com/d
    // t5rqpa84/image/upload/v1728571182/public/omcfm1hoccqduvyqmr6j.jpg','2345454','8675676',
    // '19L','11','29','6.4277','S','52','18','6.3318','W','1','1','49'];
    
            // Inserta la estructura en la base de datos
            if ($this->estructuraModel->insertarConProcedimientoAlmacenado(valoresEntrada: $estructuraData, nombreProcedimiento: "InsertEstructura")) {
                return "Registro insertado correctamente";
            } else {
                throw new Exception("Error al insertar el registro de la estructura.");
            }
        } catch (Exception $e) {
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
    
    public function buscarUltimaEstructuraDeTrabajador() 
    // Afinar este metodo
    {
        $tipo = $_POST['tipo'] ?? 'idOperadorAsignado';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
         // Ordenar por la fecha de registro de forma descendente y limitar a la primera fila
         $orden = "fechaRegistro DESC";
         $limite = "1";

        $estructuras = $this->estructuraModel->seleccionar("*", condiciones:"$tipo = '$valorBuscado'", ordenamiento: $orden, limite: $limite);

       
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

    try {
        // Prepara los datos en un array
        $estructuraData = [
            $data['idEstructura'],
            $data['nombre'],
            $data['imagenEstructura']['urlImagen'],
            $data['imagenGPS']['urlImagen'],
            $data['ubicacionUTM']['coordenadaX'],
            $data['ubicacionUTM']['coordenadaY'],
            $data['ubicacionUTM']['zonaCartografica'],
            $data['ubicacionDMS']['latitud']['grados'],
            $data['ubicacionDMS']['latitud']['minutos'],
            $data['ubicacionDMS']['latitud']['segundos'],
            $data['ubicacionDMS']['latitud']['hemisferio'],
            $data['ubicacionDMS']['longitud']['grados'],
            $data['ubicacionDMS']['longitud']['minutos'],
            $data['ubicacionDMS']['longitud']['segundos'],
            $data['ubicacionDMS']['longitud']['hemisferio'],
            $data['estaCompleta'],
            $data['idProyecto'],
            $data['idOperadorAsignado']
        ];

        // Actualiza la estructura en la base de datos usando el procedimiento almacenado
        if ($this->estructuraModel->insertarConProcedimientoAlmacenado(valoresEntrada: $estructuraData, nombreProcedimiento: "UpdateEstructura")) {
            return "Registro actualizado correctamente";
        } else {
            throw new Exception("Error al actualizar el registro de la estructura.");
        }
    } catch (Exception $e) {
        return "Ocurri�� un error: " . $e->getMessage();
    }
}


    public function eliminar()
    {
        $idEstructura = $_POST['idEstructura'] ?? '';
        return $this->estructuraModel->eliminar("idEstructura = '$idEstructura'")
            ? "Registro eliminado correctamente" 
            : "Error al eliminar el registro.";
    }

    public function getImageForProject(){
        $idProyecto = $_POST['idProyecto'] ?? '1';

        $campos = "i.urlImagen AS urlImagenEstructura";
        $joins = "e INNER JOIN imagen i ON e.imagenEstructura = i.idImagen";
        $condiciones = "e.idProyecto = '$idProyecto'";
        $orden = "e.fechaRegistro DESC";
        $limite = "1";

        $estructura = $this->estructuraModel->seleccionar($campos, $joins, $condiciones, $orden, $limite);

        header('Content-Type: application/json');
        echo json_encode($estructura[0]['urlImagenEstructura'] ?? null);
    }

    public function listar()
    {
        $estructuras = $this->estructuraModel->seleccionar();
        header('Content-Type: application/json');
        echo json_encode($estructuras ?: null);
    }   
}
