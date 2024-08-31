<?php
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

    // Otros métodos como listar, modificar, eliminar...
}