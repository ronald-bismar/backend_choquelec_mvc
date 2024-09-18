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
           id: $_POST['id']?? null,
           grados: $_POST['grados'] ?? '0',
           minutos: $_POST['minutos'] ?? '0',
           segundos: $_POST['segundos'] ?? '0',
           hemisferio: $_POST['hemisferio'] ?? 'N',
           tipo: $_POST['tipo'] ?? 'latitud',
        );

        $datosEnviar = $coordenadaDMS->toArray();
        $respuesta = $this->coordenadaDMSModel->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    public function obtener(){
        $id = $_POST['id']?? '1';

        $condicion = "id = '$id'";

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
    public function actualizar()
    {
        // Si se envían como JSON
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);
    
        $coordenadaDMS = CoordenadaDMS::fromArray($data);
    
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadaDMS->toArray();
    
        $coordenadaDMS = $this->coordenadaDMSModel->actualizar($datosEnviar, condicion: "id = '{$coordenadaDMS->id}'");
    
        return $coordenadaDMS ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function eliminar()//Por el momento no se puede eliminar porque esta referenciado en la tabla coordenadasdms
    {
        $id = $_POST['id'] ?? '2';
        
        $respuesta = $this->coordenadaDMSModel->eliminar(condiciones: "id = '$id'");

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
    
    // public function buscarPor()
    // {
    //     $tipo = $_POST['tipo'] ?? 'id';
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

//     public function obtenerImagenesWithParams($id){
//         $condicion = "id = '$id'";

//         $coordenadaDMS = $this->coordenadaDMSModel->seleccionar(condiciones: $condicion);

//         $arrayImagenes = [];
//     if ($coordenadaDMS && !empty($coordenadaDMS)) {
    
//     foreach ($coordenadaDMS as $coordenadaDMS) {
//         $arrayImagenes[] = $coordenadaDMS->fromArray();
//     }
//     return $arrayImagenes;
//     }
// }

    // public function actualizarWithParams(CoordenadaDMS $coordenadaDMS)
    // {
    //     // Convertir a array para la base de datos
    //     $datosEnviar = $coordenadaDMS->toArray();
    
    //     // Actualizar en la base de datos
    //     $coordenadaDMS = $this->coordenadaDMSModel->actualizar($datosEnviar, condicion: "id = '{$coordenadaDMS->id}'");
    
    //     return $coordenadaDMS ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    // }
} 