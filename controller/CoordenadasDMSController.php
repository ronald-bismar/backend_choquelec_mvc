<?php
require_once "model/entities/CoordenadasDMS.php";
require_once "model/CoordenadasDMSModel.php";
require_once "model/entities/CoordenadaDMS.php";
require_once "model/CoordenadaDMSModel.php";


class CoordenadasDMSController{

   private CoordenadasDMSModel $coordenadasDMSModel;
   private CoordenadaDMSModel $coordenadaDMSModel;

    public function __construct()
    {
        $this->coordenadasDMSModel = new CoordenadasDMSModel();
        $this->coordenadaDMSModel = new CoordenadaDMSModel();
    }

    public function guardar()
    {
        // Si se envían como JSON
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);

        $latitud = isset($data['latitud']) ? CoordenadaDMS::fromArray($data['latitud']) : new CoordenadaDMS();
        $longitud = isset($data['longitud']) ? CoordenadaDMS::fromArray($data['longitud']) : new CoordenadaDMS();        

        $responseLatitud =$this->coordenadaDMSModel->insertar($latitud->toArray());
        $responseLongitud =$this->coordenadaDMSModel->insertar($longitud->toArray());

        if($responseLatitud && $responseLongitud){
            $idLatitud = $this->coordenadaDMSModel->seleccionar(campos: 'id', condiciones: "grados = '{$latitud->grados}' AND minutos = '{$latitud->minutos}' AND segundos = '{$latitud->segundos}' AND tipo = 'latitud'");
            $idLongitud = $this->coordenadaDMSModel->seleccionar(campos: 'id', condiciones: "grados = '{$longitud->grados}' AND minutos = '{$longitud->minutos}' AND segundos = '{$longitud->segundos}' AND tipo = 'longitud'");

            $coordenadasDMS = new CoordenadasDMS(
                null,
                $idLatitud[0]['id'],
                $idLongitud[0]['id'],
            );
        
            $datosEnviar = $coordenadasDMS->toArray();
            $respuesta = $this->coordenadasDMSModel->insertar($datosEnviar);
    
            return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
        }
    }
    
    public function buscarPor()
{
    $tipo = $_POST['tipo'] ?? 'idCoordenadasDMS';
    $valorBuscado = $_POST['valorBuscado'] ?? '1';
    
    // Buscar coordenadas en base al tipo y valor buscado
    $coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    if ($coordenadasDMSEncontradas) {
        $coordenadas = [];
        
        // Recorremos las coordenadas encontradas
        foreach ($coordenadasDMSEncontradas as $coordenada) {
            $idLatitud = $coordenada['latitud_id'];
            $idLongitud = $coordenada['longitud_id'];

            // Buscar detalles de latitud y longitud en la base de datos
            $coordenadaDMSLatitud = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$idLatitud'");
            $coordenadaDMSLongitud = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$idLongitud'");
            
            // Crear objetos CoordenadaDMS con los datos obtenidos
            $coordenadaDMSLat = new CoordenadaDMS(
                $coordenadaDMSLatitud[0]['id'],
                $coordenadaDMSLatitud[0]['grados'],
                $coordenadaDMSLatitud[0]['minutos'],
                $coordenadaDMSLatitud[0]['segundos'],
                $coordenadaDMSLatitud[0]['hemisferio'],
                $coordenadaDMSLatitud[0]['tipo']
            );
            $coordenadaDMSLon = new CoordenadaDMS(
                $coordenadaDMSLongitud[0]['id'],
                $coordenadaDMSLongitud[0]['grados'],
                $coordenadaDMSLongitud[0]['minutos'],
                $coordenadaDMSLongitud[0]['segundos'],
                $coordenadaDMSLongitud[0]['hemisferio'],
                $coordenadaDMSLongitud[0]['tipo']
            );

            // Agregar los datos al array de coordenadas
            $coordenadas[] = [
                "idCoordenadasDMS" => $coordenada['idCoordenadasDMS'],
                "latitud" => $coordenadaDMSLat->toArray(),
                "longitud" => $coordenadaDMSLon->toArray(),
            ];
        }

        // Devolver los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($coordenadas);
    } else {
        // Si no se encuentran coordenadas, devolver null
        header('Content-Type: application/json');
        echo json_encode(null);
    }
}


    public function obtenerCoordenadasDMS(){
        $idCoordenadasDMS = $_POST['idCoordenadasDMS']?? '1';
// Buscar coordenadas en base al tipo y valor buscado
$coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar(condiciones: "idCoordenadasDMS = '$idCoordenadasDMS'");

if ($coordenadasDMSEncontradas) {
    $coordenadas = [];
    
    // Recorremos las coordenadas encontradas
        $idLatitud = $coordenadasDMSEncontradas[0]['latitud_id'];
        $idLongitud = $coordenadasDMSEncontradas[0]['longitud_id'];

        // Buscar detalles de latitud y longitud en la base de datos
        $coordenadaDMSLatitud = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$idLatitud'");
        $coordenadaDMSLongitud = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$idLongitud'");
        
        // Crear objetos CoordenadaDMS con los datos obtenidos
        $coordenadaDMSLat = new CoordenadaDMS(
            $coordenadaDMSLatitud[0]['id'],
            $coordenadaDMSLatitud[0]['grados'],
            $coordenadaDMSLatitud[0]['minutos'],
            $coordenadaDMSLatitud[0]['segundos'],
            $coordenadaDMSLatitud[0]['hemisferio'],
            $coordenadaDMSLatitud[0]['tipo']
        );
        $coordenadaDMSLon = new CoordenadaDMS(
            $coordenadaDMSLongitud[0]['id'],
            $coordenadaDMSLongitud[0]['grados'],
            $coordenadaDMSLongitud[0]['minutos'],
            $coordenadaDMSLongitud[0]['segundos'],
            $coordenadaDMSLongitud[0]['hemisferio'],
            $coordenadaDMSLongitud[0]['tipo']
        );

        // Agregar los datos al array de coordenadas
        $coordenadas[] = [
            "idCoordenadasDMS" => $idCoordenadasDMS,
            "latitud" => $coordenadaDMSLat->toArray(),
            "longitud" => $coordenadaDMSLon->toArray(),
        ];
    

    // Devolver los resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($coordenadas);
} else {
    // Si no se encuentran coordenadas, devolver null
    header('Content-Type: application/json');
    echo json_encode(null);
}
    }

//     public function obtenerImagenesWithParams($idCoordenadasDMS){
//         $condicion = "idCoordenadasDMS = '$idCoordenadasDMS'";

//         $coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar(condiciones: $condicion);

//         $arrayImagenes = [];
//     if ($coordenadasDMSEncontradas && !empty($coordenadasDMSEncontradas)) {
    
//     foreach ($coordenadasDMSEncontradas as $coordenadasDMS) {
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
            $data['idCoordenadasDMS']?? '1',
            $data['latitud']['id']?? '20',
            $data['longitud']['id']?? '21',
        );

        $coordenadaDMSLat =  isset($data['latitud']) ? CoordenadaDMS::fromArray($data['latitud']) : new CoordenadaDMS();
        $coordenadaDMSLon =  isset($data['longitud']) ? CoordenadaDMS::fromArray($data['longitud']) : new CoordenadaDMS();  
    
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadasDMS->toArray();
        // var_dump($datosEnviar);
    
        // Actualizar en la base de datos
        $responseCordenadaDMSLat = $this->coordenadaDMSModel->actualizar($coordenadaDMSLat->toArray(), condicion: "id = '{$coordenadaDMSLat->id}'");
        $responseCordenadaDMSLon = $this->coordenadaDMSModel->actualizar($coordenadaDMSLon->toArray(), condicion: "id = '{$coordenadaDMSLon->id}'");
        $responseCordenadasDMS = $this->coordenadasDMSModel->actualizar($datosEnviar, condicion: "idCoordenadasDMS = '{$coordenadasDMS->idCoordenadasDMS}'");
    
        return $responseCordenadasDMS ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    public function actualizarWithParams(CoordenadasDMS $coordenadasDMS)
    {
        // Convertir a array para la base de datos
        $datosEnviar = $coordenadasDMS->toArray();
    
        // Actualizar en la base de datos
        $coordenadasDMSEncontradas = $this->coordenadasDMSModel->actualizar($datosEnviar, condicion: "idCoordenadasDMS = '{$coordenadasDMS->idCoordenadasDMS}'");
    
        return $coordenadasDMSEncontradas ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    
    public function eliminar()
    {
        $idCoordenadasDMS = $_POST['idCoordenadasDMS'] ?? '10';
        
        $respuesta = $this->coordenadasDMSModel->eliminar(condiciones: "idCoordenadasDMS = '$idCoordenadasDMS'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $coordenadasDMSEncontradas = $this->coordenadasDMSModel->seleccionar();

     if ($coordenadasDMSEncontradas) {
        $coordenadas = [];
        
        // Recorremos las coordenadas encontradas
        foreach ($coordenadasDMSEncontradas as $coordenada) {
            $idLatitud = $coordenada['latitud_id'];
            $idLongitud = $coordenada['longitud_id'];

            // Buscar detalles de latitud y longitud en la base de datos
            $coordenadaDMSLatitud = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$idLatitud'");
            $coordenadaDMSLongitud = $this->coordenadaDMSModel->seleccionar(condiciones: "id = '$idLongitud'");
            
            // Crear objetos CoordenadaDMS con los datos obtenidos
            $coordenadaDMSLat = new CoordenadaDMS(
                $coordenadaDMSLatitud[0]['id'],
                $coordenadaDMSLatitud[0]['grados'],
                $coordenadaDMSLatitud[0]['minutos'],
                $coordenadaDMSLatitud[0]['segundos'],
                $coordenadaDMSLatitud[0]['hemisferio'],
                $coordenadaDMSLatitud[0]['tipo']
            );
            $coordenadaDMSLon = new CoordenadaDMS(
                $coordenadaDMSLongitud[0]['id'],
                $coordenadaDMSLongitud[0]['grados'],
                $coordenadaDMSLongitud[0]['minutos'],
                $coordenadaDMSLongitud[0]['segundos'],
                $coordenadaDMSLongitud[0]['hemisferio'],
                $coordenadaDMSLongitud[0]['tipo']
            );

            // Agregar los datos al array de coordenadas
            $coordenadas[] = [
                "idCoordenadasDMS" => $coordenada['idCoordenadasDMS'],
                "latitud" => $coordenadaDMSLat->toArray(),
                "longitud" => $coordenadaDMSLon->toArray(),
            ];
        }

        // Devolver los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($coordenadas);
    } else {
        // Si no se encuentran coordenadas, devolver null
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }
} 