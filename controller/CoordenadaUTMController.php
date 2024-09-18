<?php
require_once "model/entities/CoordenadaUTM.php";
require_once "model/CoordenadaUTMModel.php";

class CoordenadaUTMController{

   private CoordenadaUTMModel $coordenadaUTMModel;

    public function __construct()
    {
        $this->coordenadaUTMModel = new CoordenadaUTMModel();
    }

    public function guardar()
    {
        $coordenadaUTM = new CoordenadaUTM(
           idCoordenadaUTM: $_POST['idCoordenadaUTM']?? null,
           coordenadaX: $_POST['coordenadaX']?? '0.0',
           coordenadaY: $_POST['coordenadaY']?? '0.0',
           zonaCartografica: $_POST['zonaCartografica']?? '19K',
        );

        $datosEnviar = $coordenadaUTM->toArray();
        $respuesta = $this->coordenadaUTMModel->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    // public function buscarPor()
    // {
    //     $tipo = $_POST['tipo'] ?? 'idCoordenadaUTM';
    //     $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
    //     $coordenadaUTM = $this->coordenadaUTMModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    // if ($coordenadaUTM) {
    //     // var_dump($coordenadaUTM);
    //     header('Content-Type: application/json');
    //     echo json_encode($coordenadaUTM);
    // } else {
    //     header('Content-Type: application/json');
    //     echo json_encode(null);
    // }
    // }

    public function obtenerCoordenadaUTM(){
        $idCoordenadaUTM = $_POST['idCoordenadaUTM']?? '1';

        $condicion = "idCoordenadaUTM = '$idCoordenadaUTM'";

        $coordenadaUTM = $this->coordenadaUTMModel->seleccionar(condiciones: $condicion);

    if ($coordenadaUTM && !empty($coordenadaUTM)) {
        header('Content-Type: application/json');
        echo json_encode($coordenadaUTM[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

//     public function obtenerImagenesWithParams($idCoordenadaUTM){
//         $condicion = "idCoordenadaUTM = '$idCoordenadaUTM'";

//         $coordenadaUTM = $this->coordenadaUTMModel->seleccionar(condiciones: $condicion);

//         $arrayImagenes = [];
//     if ($coordenadaUTM && !empty($coordenadaUTM)) {
    
//     foreach ($coordenadaUTM as $coordenadaUTM) {
//         $arrayImagenes[] = $coordenadaUTM->fromArray();
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
        $coordenadaUTM = isset($data) ? CoordenadaDMS::fromArray($data['longitud']) : new CoordenadaUTM(); 

        $coordenadaUTM->idCoordenadaUTM = '1';
    
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadaUTM->toArray();
        // var_dump($datosEnviar);
    
        // Actualizar en la base de datos
        $coordenadaUTM = $this->coordenadaUTMModel->actualizar($datosEnviar, condicion: "idCoordenadaUTM = '{$coordenadaUTM->idCoordenadaUTM}'");
    
        return $coordenadaUTM ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function actualizarWithParams(CoordenadaUTM $coordenadaUTM)
    {
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadaUTM->toArray();
    
        // Actualizar en la base de datos
        $coordenadaUTM = $this->coordenadaUTMModel->actualizar($datosEnviar, condicion: "idCoordenadaUTM = '{$coordenadaUTM->idCoordenadaUTM}'");
    
        return $coordenadaUTM ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    
    public function eliminar()
    {
        $idCoordenadaUTM = $_POST['idCoordenadaUTM'] ?? '5';
        
        $respuesta = $this->coordenadaUTMModel->eliminar(condiciones: "idCoordenadaUTM = '$idCoordenadaUTM'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $coordenadaUTM = $this->coordenadaUTMModel->seleccionar();

    if ($coordenadaUTM) {
        // var_dump($coordenadaUTM);
        header('Content-Type: application/json');
        echo json_encode($coordenadaUTM);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
} 