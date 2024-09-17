<?php
require_once "model/entities/Imagen.php";
require_once "model/ImagenModel.php";

class ImagenController{

   private ImagenModel $imagenModel;

    public function __construct()
    {
        $this->imagenModel = new ImagenModel();
    }

    public function guardar()
    {
        $imagen = new Imagen(
           urlImagen: $_POST['urlImagen'] ?? '',
           tipoImagen: $_POST['tipoImagen'] ?? '',
           fechaCaptura : $_POST['fechaCaptura '] ?? '',
           activo : $_POST['activo '] ?? '1',
          
        );

        $datosEnviar = $imagen->toArray();
        $respuesta = $this->imagenModel->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    
    public function buscarPor()
    {
        $tipo = $_POST['tipo'] ?? 'activo';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
        $imagenes = $this->imagenModel->seleccionar(condiciones: "$tipo = '$valorBuscado'");

    if ($imagenes) {
        // var_dump($imagenes);
        header('Content-Type: application/json');
        echo json_encode($imagenes);
    } else {
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    public function obtenerImagenes(){
        $idImagen = $_POST['idImagen']?? '1';

        $condicion = "idImagen = '$idImagen'";

        $imagenes = $this->imagenModel->seleccionar(condiciones: $condicion);

    if ($imagenes && !empty($imagenes)) {
        header('Content-Type: application/json');
        echo json_encode($imagenes[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

//     public function obtenerImagenesWithParams($idImagen){
//         $condicion = "idImagen = '$idImagen'";

//         $imagenes = $this->imagenModel->seleccionar(condiciones: $condicion);

//         $arrayImagenes = [];
//     if ($imagenes && !empty($imagenes)) {
    
//     foreach ($imagenes as $imagen) {
//         $arrayImagenes[] = $imagen->fromArray();
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
        $imagen = new Imagen(
            $data['idImagen'],
            $data['urlImagen'],
            $data['tipoImagen'],
            $data['fechaCaptura'],
            $data['activo'],
        );
    
        // Convertir a array para la base de datos
        $datosEnviar = $imagen->toArray();
        echo "Id Imagen: ". $data['idImagen'];
        // var_dump($datosEnviar);
    
        // Actualizar en la base de datos
        $imagenes = $this->imagenModel->actualizar($datosEnviar, condicion: "idImagen = '{$imagen->idImagen}'");
    
        return $imagenes ? "Registro actualizado correctamente" : "Error al actualizar el registro.";
    }
    
    
    public function eliminar()
    {
        $idImagen = $_POST['idImagen'] ?? '';
        
        $respuesta = $this->imagenModel->eliminar(condiciones: "idImagen = '$idImagen'");

        return $respuesta ? "Registro eliminado correctamente" : "Error al eliminar el registro.";
    }

    public function listar()
    {

     $imagenes = $this->imagenModel->seleccionar();

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