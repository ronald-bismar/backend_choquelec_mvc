<?php
class BD
{
    private $conexion;
    protected $nombreTabla;

    function __construct()
    {
        $this->conexion = new mysqli('bpd8jelghf6igtd6gi28-mysql.services.clever-cloud.com', 'uc5kajgajjlmiubh', '95sQKgUZZK68X0LcjM9d', 'bpd8jelghf6igtd6gi28');

        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    function insertar($valoresEntrada)
    {

        $campos = implode(',', array_keys($valoresEntrada));
        $valores = "'" . implode("','", array_values($valoresEntrada)) . "'";

        $consulta = "INSERT INTO $this->nombreTabla ($campos) VALUES($valores)";

        $respuesta = $this->conexion->query($consulta);

        if (!$respuesta) {
            die("Error al insertar: " . $this->conexion->error);
        }else {
            echo "Registro exitoso";
        }

        return $respuesta;
    }

    function seleccionar($campos = '*', $condiciones = '', $ordenamiento = '', $limite = '')
    {
        $condiciones = $condiciones ? "WHERE $condiciones" : '';
        $ordenamiento = $ordenamiento ? "ORDER BY $ordenamiento" : '';
        $limite = $limite ? "LIMIT $limite" : '';

        $consulta = "SELECT $campos FROM $this->nombreTabla $condiciones $ordenamiento $limite";
        $respuesta = $this->conexion->query($consulta);

        if (!$respuesta) {
            echo("Error al seleccionar: " . $this->conexion->error);
        }else {
            $datos = [];
        while ($fila = $respuesta->fetch_assoc()) {
            $datos[] = $fila;
        }
        }

        
        return $datos;
    }

    // Métodos para modificar y eliminar...
}