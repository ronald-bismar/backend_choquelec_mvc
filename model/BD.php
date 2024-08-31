<?php
class BD
{
    private $conexion;
    protected $nombreTabla;

    function __construct()
    {
        $this->conexion = new mysqli('localhost', 'root', '', 'bd_choquelec_n');

        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    function insertar($valoresEntrada)
    {

        $campos = implode(',', array_keys($valoresEntrada));
        $valores = "'" . implode("','", array_values($valoresEntrada)) . "'";

        $consulta = "INSERT INTO $this->nombreTabla ($campos) VALUES($valores)";

        ECHO $consulta;
        $respuesta = $this->conexion->query($consulta);

        if (!$respuesta) {
            die("Error al insertar: " . $this->conexion->error);
        }else {
            echo "Registro insertado correctamente";
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
            die("Error al seleccionar: " . $this->conexion->error);
        }

        $datos = [];
        while ($fila = $respuesta->fetch_assoc()) {
            $datos[] = $fila;
        }
        return $datos;
    }

    // Métodos para modificar y eliminar...
}