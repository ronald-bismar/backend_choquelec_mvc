<?php
require_once "model/entities/CoordenadaDMS.php";
require_once "model/CoordenadaDMSModel.php";

class CoordenadaDMSController{

   private CoordenadaDMSModel $coordenadaDMSModel;

    public function __construct()
    {
        $this->coordenadaDMSModel = new CoordenadaDMSModel();
    }

    public function guardar()
    {
        $coordenadaDMS = new CoordenadaDMS(
           id: $_POST['id'] ?? '',
           grados: $_POST['grados'] ?? '',
           minutos: $_POST['minutos'] ?? '',
           segundos: $_POST['segundos'] ?? '',
           hemisferio: $_POST['hemisferio'] ?? '',
           tipo: $_POST['tipo'] ?? '',
        );

        $datosEnviar = $coordenadaDMS->toArray();
        $respuesta = $this->coordenadaDMSModel->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    // public function buscarPor()
    // {
    //     $tipo = $_POST['tipo'] ?? 'idCoordenadaDMS';
    //     $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
    //     $coordenadaDMS = $this->coordenadaDMSModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    // if ($coordenadaDMS) {
    //     // var_dump($coordenadaDMS);
    //     header('Content-Type: application/json');
    //     echo json_encode($coordenadaDMS);
    // } else {
    //     header('Content-Type: application/json');
    //     echo json_encode(null);
    // }
    // }

    public function obtenerCoordenadaDMS(){
        $idCoordenadaDMS = $_POST['idCoordenadaDMS']?? '1';

        $condicion = "idCoordenadaDMS = '$idCoordenadaDMS'";

        $coordenadaDMS = $this->coordenadaDMSModel->seleccionar(condiciones: $condicion);

    if ($coordenadaDMS && !empty($coordenadaDMS)) {
        header('Content-Type: application/json');
        echo json_encode($coordenadaDMS[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

//     public function obtenerImagenesWithParams($idCoordenadaDMS){
//         $condicion = "idCoordenadaDMS = '$idCoordenadaDMS'";

//         $coordenadaDMS = $this->coordenadaDMSModel->seleccionar(condiciones: $condicion);

//         $arrayImagenes = [];
//     if ($coordenadaDMS && !empty($coordenadaDMS)) {
    
//     foreach ($coordenadaDMS as $coordenadaDMS) {
//         $arrayImagenes[] = $coordenadaDMS->fromArray();
//     }
//     return $arrayImagenes;
//     }
// }

    public function actualizar()
    {
        // Si se envían como JSON
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);
    
        // Si los datos se envían como formulario (application/x-www-form-urlencoded)
        $coordenadaDMS = new CoordenadaDMS(
            $data['id'],
            $data['grados'],
            $data['minutos'],
            $data['segundos'],
            $data['hemisferio'],
            $data['tipo'],
        );
    
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadaDMS->toArray();
        // var_dump($datosEnviar);
    
        // Actualizar en la base de datos
        $coordenadaDMS = $this->coordenadaDMSModel->actualizar($datosEnviar, condicion: "idCoordenadaDMS = '{$coordenadaDMS->id}'");
    
        return $coordenadaDMS ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function actualizarWithParams(CoordenadaDMS $coordenadaDMS)
    {
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadaDMS->toArray();
    
        // Actualizar en la base de datos
        $coordenadaDMS = $this->coordenadaDMSModel->actualizar($datosEnviar, condicion: "idCoordenadaDMS = '{$coordenadaDMS->id}'");
    
        return $coordenadaDMS ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    
    public function eliminar()
    {
        $idCoordenadaDMS = $_POST['idCoordenadaDMS'] ?? '';
        
        $respuesta = $this->coordenadaDMSModel->eliminar(condiciones: "idCoordenadaDMS = '$idCoordenadaDMS'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $coordenadaDMS = $this->coordenadaDMSModel->seleccionar();

    if ($coordenadaDMS) {
        // var_dump($coordenadaDMS);
        header('Content-Type: application/json');
        echo json_encode($coordenadaDMS);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
} 