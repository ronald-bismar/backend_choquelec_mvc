<!-- <?php
require_once "model/entities/Trabajador.php";
require_once "model/TrabajadorModel.php";

class TrabajadorController
{
    private TrabajadorModel $trabajadorModelo;

    public function __construct()
    {
        $this->trabajadorModelo = new TrabajadorModel();
    }

    public function guardar()
    {
        $trabajador = new Trabajador(
            $_POST['nombre'] ?? '',
            $_POST['apellido'] ?? '',
            $_POST['contrasenia'] ?? '',
            $_POST['tipoDeTrabajador'] ?? '',
            $_POST['cedulaDeIdentidad'] ?? ''
        );

        $datosEnviar = $trabajador->toArray();
        $respuesta = $this->trabajadorModelo->insertar($datosEnviar);

        return $respuesta ? "Registro insertado correctamente" : "Error al insertar el registro.";
    }
    public function autenticar()
    {
        $trabajador = new Trabajador(
            contrasenia: $_POST['contrasenia'] ?? '',
            cedulaDeIdentidad: $_POST['cedulaDeIdentidad'] ?? ''
        );

        $respuesta = $this->trabajadorModelo->seleccionar(
            condiciones: 
            "cedulaDeIdentidad = '$trabajador->cedulaDeIdentidad'
             AND 
            contrasenia = '$trabajador->contrasenia'");

    if ($respuesta && !empty($respuesta)) {
        
        $tipoDeTrabajador = $trabajador->getTypeToString(intval($respuesta[0]['tipoDeTrabajador'])); //Obtener el tipo de trabajador en letrasç
        // Añadir el tipo de trabajador al array de respuesta
        $respuesta[0]['tipoDeTrabajador'] = $tipoDeTrabajador;
        header('Content-Type: application/json');
        echo json_encode($respuesta[0]);
    } else {
        // Si no hay resultados, devolver null como JSON
        header('Content-Type: application/json');
        echo json_encode(null);
    }
    }

    // Otros métodos como listar, modificar, eliminar...
} -->