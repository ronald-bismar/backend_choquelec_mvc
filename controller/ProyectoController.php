<?php
require_once "model/entities/Proyecto.php";
require_once "model/ProyectoModel.php";
require_once "model/EstructuraModel.php";

class ProyectoController
{
    private ProyectoModel $proyectoModel;
    private EstructuraModel $estructuraModel;

    public function __construct()
    {
        $this->proyectoModel = new ProyectoModel();
        $this->estructuraModel = new EstructuraModel();
    }

    public function guardar()
    {
        $proyecto = new Proyecto(
            idProyecto: $_POST['idProyecto'] ?? null,
            nombre: $_POST['nombre'] ?? '',
            ubicacion:  $_POST['ubicacion'] ?? '',
            estaCompleta: $_POST['estaCompleta'] ?? '',
            fechaRegistro: $_POST['fechaRegistro'] == ''? date("Y-m-d"): $_POST['fechaRegistro'],
            idSupervisor: $_POST['idSupervisor'] == ''? null: $_POST['idSupervisor'],
            idResidenteDeObra: $_POST['idResidenteDeObra']== ''? '0': $_POST['idSupervisor'],
            activo: $_POST['activo'] ?? '1',
        );

        $datosEnviar = $proyecto->toArray();
        $proyectos = $this->proyectoModel->insertar($datosEnviar);

        return $proyectos ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'estaCompleta';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
        $proyectos = $this->proyectoModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    if ($proyectos) {
        // var_dump($proyectos);
        header('Content-Type: application/json');
        echo json_encode($proyectos);
    } else {
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerProyecto(){
        $idProyecto = $_POST['idProyecto'];

        $respuesta = $this->proyectoModel->seleccionar(condiciones: "idProyecto = '$idProyecto'");

    if ($respuesta && !empty($respuesta)) {
        header('Content-Type: application/json');
        echo json_encode($respuesta[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
    public function actualizar()
    {
        $proyecto = new Proyecto(
            $_POST['idProyecto'],
            $_POST['nombre'],
            $_POST['ubicacion'],
            $_POST['estaCompleta'],
            $_POST['fechaRegistro'],
            $_POST['idSupervisor'],
            $_POST['idResidenteDeObra'], 
            $_POST['activo'], 
        );

        $datosEnviar = $proyecto->toArray();
        $respuestaActualizacion = $this->proyectoModel->actualizar($datosEnviar, "idProyecto = '{$proyecto->idProyecto}'");

        return $respuestaActualizacion ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function eliminar()
    {
        $idProyecto = $_POST['idProyecto'] ?? '';

        $valorDeEntrada['activo'] = 0;
        
        $respuesta = $this->proyectoModel->actualizar(valoresEntrada: $valorDeEntrada, condicion: "idProyecto = '$idProyecto'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $proyectos = $this->proyectoModel->seleccionar(condiciones: "activo = '1'");

    if ($proyectos) {
        // var_dump($proyectos);
        header('Content-Type: application/json');
        echo json_encode($proyectos);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
    public function verificarCompletado()
    {
        $idProyecto = $_POST['idProyecto'];

     $estructuras = $this->estructuraModel->seleccionar(condiciones: "idProyecto = '$idProyecto'");

     $estaCompleta = true;

     foreach ($estructuras as $estructura) {
        if($estructura['estaCompleta'] == 0)
        {
            $estaCompleta = false;
            break;
        }
    }

    echo "estaCompletado? : {$estaCompleta}";

    $respuestaActualizacion = $this->proyectoModel->actualizar(['estaCompleta' => $estaCompleta], condicion: "idProyecto = '$idProyecto' ");

    if ($respuestaActualizacion) {
        $proyectos = $this->proyectoModel->seleccionar(condiciones: "activo = '1'");
        header('Content-Type: application/json');
        echo json_encode($proyectos);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerEstructurasParaListar(){
        $idProyecto = $_POST['idProyecto']?? '4';
        $idProyecto = $_POST['idProyecto']?? '4';

        $campos = "idEstructura, 
                    nombre,  
                    estaCompleta";
            $condicion = "idProyecto = '$idProyecto'";
            $estructuras = $this->estructuraModel->seleccionar(campos: $campos, condiciones: $condicion);

    if ($estructuras) {
        header('Content-Type: application/json');
        echo json_encode($estructuras);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerEstructurasCompletas()
    {
        $idProyecto = $_POST['idProyecto']?? '4';

        $campos = "e.idEstructura, 
             e.nombre, 
             e.fechaRegistro, 
             e.estaCompleta, 
             e.idOperadorAsignado,
             e.idProyecto,
             i.idImagen AS idImagenEstructura,
             i.tipoImagen AS tipoImagenEstructura,
             i.fechaCaptura AS fechaCapturaImagenEstructura,
             i.urlImagen AS urlImagenEstructura, 
             ig.idImagen AS idImagenGPS,
             ig.tipoImagen AS tipoImagenGPS,
             ig.fechaCaptura AS fechaCapturaImagenGPS,
             ig.urlImagen AS urlImagenGPS,
             cu.idCoordenadaUTM,
             cu.coordenadaX, 
             cu.coordenadaY, 
             cu.zonaCartografica, 
             lat.id AS idLatitudDMS, 
             lat.grados AS latitudGrados, 
             lat.minutos AS latitudMinutos, 
             lat.segundos AS latitudSegundos, 
             lat.hemisferio AS latitudHemisferio, 
             lon.id AS idLongitudDMS, 
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
                  
            $condicion = "idProyecto = '$idProyecto'";
            $estructuras = $this->estructuraModel->seleccionar(campos: $campos, innerjoin: $innerjoins, condiciones: $condicion);

    if ($estructuras) {
        header('Content-Type: application/json');
        echo json_encode($estructuras);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }


//     $consultaInnerJoin = "SELECT 
//     e.idEstructura, 
//     e.nombre, 
//     e.fechaRegistro, 
//     i.urlImagen AS urlImagenEstructura, 
//     ig.urlImagen AS urlImagenGPS, 
//     cu.coordenadaX, 
//     cu.coordenadaY, 
//     cu.zonaCartografica, 
//     lat.grados AS latitudGrados, 
//     lat.minutos AS latitudMinutos, 
//     lat.segundos AS latitudSegundos, 
//     lat.hemisferio AS latitudHemisferio, 
//     lon.grados AS longitudGrados, 
//     lon.minutos AS longitudMinutos, 
//     lon.segundos AS longitudSegundos, 
//     lon.hemisferio AS longitudHemisferio
// FROM 
//     estructura e
// INNER JOIN 
//     imagen i ON e.imagenEstructura = i.idImagen
// LEFT JOIN 
//     imagen ig ON e.imagenGPS = ig.idImagen
// INNER JOIN 
//     coordenadautm cu ON e.ubicacionUTM = cu.idCoordenadaUTM
// INNER JOIN 
//     coordenadasdms cosdms ON cosdms.idCoordenadasDMS = e.ubicacionDMS
// INNER JOIN 
//     coordenadadms lat ON cosdms.latitud_id = lat.id
// INNER JOIN 
//     coordenadadms lon ON cosdms.longitud_id = lon.id WHERE e.idProyecto = 35";
    }
} 