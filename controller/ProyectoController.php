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
        $proyecto = $this->createProyectoFromPost();
        $result = $this->proyectoModel->insertar($proyecto->toArray());
        return $result ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'estaCompleta';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
        $proyectos = $this->proyectoModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");
        $this->sendJsonResponse($proyectos);
    }

    public function obtenerProyecto()
    {
        $idProyecto = $_POST['idProyecto'];
        $respuesta = $this->proyectoModel->seleccionar(condiciones: "idProyecto = '$idProyecto'");
        $this->sendJsonResponse($respuesta[0] ?? null);
    }

    public function actualizar()
    {
        $proyecto = $this->createProyectoFromPost();
        $result = $this->proyectoModel->actualizar($proyecto->toArray(), "idProyecto = '{$proyecto->idProyecto}'");
        return $result ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }

    public function eliminar()
    {
        $idProyecto = $_POST['idProyecto'] ?? '';
        $result = $this->proyectoModel->actualizar(['activo' => 0], "idProyecto = '$idProyecto'");
        return $result ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {
        $proyectos = $this->proyectoModel->seleccionar(condiciones: "activo = '1'");
        $this->sendJsonResponse($proyectos);
    }

    public function verificarCompletado()
    {
        $idProyecto = $_POST['idProyecto'];
        $estructuras = $this->estructuraModel->seleccionar(condiciones: "idProyecto = '$idProyecto'");
        $estaCompleta = !in_array(0, array_column($estructuras, 'estaCompleta'));

        $result = $this->proyectoModel->actualizar(['estaCompleta' => $estaCompleta], "idProyecto = '$idProyecto'");
        if ($result) {
            $proyectos = $this->proyectoModel->seleccionar(condiciones: "activo = '1'");
            $this->sendJsonResponse($proyectos);
        } else {
            $this->sendJsonResponse(null);
        }
    }

    public function obtenerEstructurasParaListar()
    {
        $idProyecto = $_POST['idProyecto'] ?? '4';
        $campos = "idEstructura, nombre, estaCompleta";
        $condicion = "idProyecto = '$idProyecto'";
        $estructuras = $this->estructuraModel->seleccionar(campos: $campos, condiciones: $condicion);
        $this->sendJsonResponse($estructuras);
    }

    public function obtenerEstructurasCompletas()
    {
        $idProyecto = $_POST['idProyecto'] ?? '4';
        $campos = $this->getEstructurasCompletasCampos();
        $innerjoins = $this->getEstructurasCompletasJoins();
        $condicion = "idProyecto = '$idProyecto'";
        $estructuras = $this->estructuraModel->seleccionar(campos: $campos, innerjoin: $innerjoins, condiciones: $condicion);
        $this->sendJsonResponse($estructuras);
    }

    private function createProyectoFromPost(): Proyecto
    {
        return new Proyecto(
            idProyecto: !empty($_POST['idProyecto']) ? $_POST['idProyecto'] : null,
            nombre: $_POST['nombre'] ?? '',
            ubicacion: $_POST['ubicacion'] ?? '',
            estaCompleta: $_POST['estaCompleta'] ?? '',
            fechaRegistro: $_POST['fechaRegistro'] ?: date("Y-m-d"),
            idSupervisor: $_POST['idSupervisor'] ?: null,
            idResidenteDeObra: $_POST['idResidenteDeObra'] ?: '0',
            activo: $_POST['activo'] ?? '1'
        );
    }

    private function sendJsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    private function getEstructurasCompletasCampos(): string
    {
        return "e.idEstructura, e.nombre, e.fechaRegistro, e.estaCompleta, e.idOperadorAsignado, e.idProyecto,
                i.idImagen AS idImagenEstructura, i.tipoImagen AS tipoImagenEstructura, 
                i.fechaCaptura AS fechaCapturaImagenEstructura, i.urlImagen AS urlImagenEstructura, 
                ig.idImagen AS idImagenGPS, ig.tipoImagen AS tipoImagenGPS,
                ig.fechaCaptura AS fechaCapturaImagenGPS, ig.urlImagen AS urlImagenGPS,
                cu.idCoordenadaUTM, cu.coordenadaX, cu.coordenadaY, cu.zonaCartografica,
                cosdms.idCoordenadasDMS AS idCoordenadasDMS,
                lat.id AS idLatitudDMS, lat.grados AS latitudGrados, lat.minutos AS latitudMinutos, 
                lat.segundos AS latitudSegundos, lat.hemisferio AS latitudHemisferio, 
                lon.id AS idLongitudDMS, lon.grados AS longitudGrados, lon.minutos AS longitudMinutos, 
                lon.segundos AS longitudSegundos, lon.hemisferio AS longitudHemisferio";
    }

    private function getEstructurasCompletasJoins(): string
    {
        return "e
                INNER JOIN imagen i ON e.imagenEstructura = i.idImagen
                LEFT JOIN imagen ig ON e.imagenGPS = ig.idImagen
                INNER JOIN coordenadautm cu ON e.ubicacionUTM = cu.idCoordenadaUTM
                INNER JOIN coordenadasdms cosdms ON cosdms.idCoordenadasDMS = e.ubicacionDMS
                INNER JOIN coordenadadms lat ON cosdms.latitud_id = lat.id
                INNER JOIN coordenadadms lon ON cosdms.longitud_id = lon.id";
    }
    public function reporteProyectosAsignados()
    {
        $consulta = "SELECT t.nombre, t.apellido, tt.descripcion AS tipoDeTrabajador, p.nombre AS nombreProyecto, p.estaCompleta, p.fechaRegistro 
        FROM trabajador t
        JOIN proyecto p ON t.idTrabajador = p.idSupervisor OR t.idTrabajador = p.idResidenteDeObra
        JOIN tipo_trabajador tt ON t.tipoDeTrabajador = tt.idTipoTrabajador
        WHERE t.activo = 1;
        ";
        
        $data = $this->proyectoModel->ejecutarConsultaPersonalizada($consulta);
        $this->sendJsonResponse($data);
    }
}