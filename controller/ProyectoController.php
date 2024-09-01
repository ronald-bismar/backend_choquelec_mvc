<?php
require_once "model/entities/Proyecto.php";
require_once "model/ProyectoModel.php";

class ProyectoController
{
    private ProyectoModel $proyectoModel;

    public function __construct()
    {
        $this->proyectoModel = new ProyectoModel();
    }

    public function guardar()
    {
        $proyecto = new Proyecto(
            $_POST['nombre'] ?? '',
            $_POST['ubicacion'] ?? '',
            $_POST['estaCompleta'] ?? '',
            $_POST['fechaRegistro'] ?? '',
            $_POST['idSupervisor'] ?? '',
            $_POST['idResidenteDeObra'] ?? '', 
            activo: '1'
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
        $proyectos = $this->proyectoModel->actualizar($datosEnviar, "idProyecto = '{$proyecto->idProyecto}'");

        return $proyectos ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function eliminar()
    {
        $idProyecto = $_POST['idProyecto'] ?? '';
        
        $respuesta = $this->proyectoModel->eliminar(condiciones: "idProyecto = '$idProyecto'");

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
} 