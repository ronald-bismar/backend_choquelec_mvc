<?php
require_once "model/entities/CoordenadasDMS.php";
require_once "model/CoordenadasDMSModel.php";

class CoordenadasDMSController{

   private CoordenadasDMSModel $coordenadasDMSModel;

    public function __construct()
    {
        $this->coordenadasDMSModel = new CoordenadasDMSModel();
    }

    public function guardar()
    {
        $coordenadasDMS = new CoordenadasDMS(
           idLatitudDMS: $_POST['idLatitudDMS'] ?? '',
           idLongitudDMS: $_POST['idLongitudDMS'] ?? '',
        );

        $datosEnviar = $coordenadasDMS->toArray();
        $respuesta = $this->coordenadasDMSModel->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'idCoordenadasDMS';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
        $imagenes = $this->coordenadasDMSModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    if ($imagenes) {
        // var_dump($imagenes);
        header('Content-Type: application/json');
        echo json_encode($imagenes);
    } else {
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerCoordenadasDMS(){
        $idCoordenadasDMS = $_POST['idCoordenadasDMS']?? '1';

        $condicion = "idCoordenadasDMS = '$idCoordenadasDMS'";

        $imagenes = $this->coordenadasDMSModel->seleccionar(condiciones: $condicion);

    if ($imagenes && !empty($imagenes)) {
        header('Content-Type: application/json');
        echo json_encode($imagenes[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

//     public function obtenerImagenesWithParams($idCoordenadasDMS){
//         $condicion = "idCoordenadasDMS = '$idCoordenadasDMS'";

//         $imagenes = $this->coordenadasDMSModel->seleccionar(condiciones: $condicion);

//         $arrayImagenes = [];
//     if ($imagenes && !empty($imagenes)) {
    
//     foreach ($imagenes as $coordenadasDMS) {
//         $arrayImagenes[] = $coordenadasDMS->fromArray();
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
        $coordenadasDMS = new CoordenadasDMS(
            $data['idCoordenadasDMS'],
            $data['urlImagen'],
            $data['tipoImagen'],
        );
    
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadasDMS->toArray();
        echo "Id CoordenadasDMS: ". $data['idCoordenadasDMS'];
        // var_dump($datosEnviar);
    
        // Actualizar en la base de datos
        $imagenes = $this->coordenadasDMSModel->actualizar($datosEnviar, condicion: "idCoordenadasDMS = '{$coordenadasDMS->idCoordenadasDMS}'");
    
        return $imagenes ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function actualizarWithParams(CoordenadasDMS $coordenadasDMS)
    {
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadasDMS->toArray();
    
        // Actualizar en la base de datos
        $imagenes = $this->coordenadasDMSModel->actualizar($datosEnviar, condicion: "idCoordenadasDMS = '{$coordenadasDMS->idCoordenadasDMS}'");
    
        return $imagenes ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    
    public function eliminar()
    {
        $idCoordenadasDMS = $_POST['idCoordenadasDMS'] ?? '';
        
        $respuesta = $this->coordenadasDMSModel->eliminar(condiciones: "idCoordenadasDMS = '$idCoordenadasDMS'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $imagenes = $this->coordenadasDMSModel->seleccionar();

    if ($imagenes) {
        // var_dump($imagenes);
        header('Content-Type: application/json');
        echo json_encode($imagenes);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
} 