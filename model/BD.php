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

        $datos = [];
        
        if (!$respuesta) {
            echo("Error al seleccionar: " . $this->conexion->error);
        }else {
        while ($fila = $respuesta->fetch_assoc()) {
            $datos[] = $fila;
        }
        }

        
        return $datos;
    }

    function actualizar($valoresEntrada, $condicion)
    {
        $pares = [];
        // Construir pares campo=valor
        foreach ($valoresEntrada as $campo => $valor) {
            if($valor != ''){$pares[] = "$campo='$valor'";}
        }
    
        // Unir los pares con comas
        $camposValores = implode(', ', $pares);
        
        $set = "SET $camposValores";
        $where = "WHERE $condicion";
    
        $consulta = $pares != [] ? "UPDATE $this->nombreTabla $set $where" : '';
    
        echo $consulta;
        if($consulta != ''){
            $respuesta = $this->conexion->query($consulta);
        }
        
        if (!$respuesta) {
            die("Error al actualizar: " . $this->conexion->error);
        } else {
            echo "Actualizacion exitosa";
        }
    
        return $respuesta;
    }
    

    function eliminar($condiciones = '')
    {
        $condiciones = $condiciones ? "WHERE $condiciones" : '';

        $consulta = "DELETE FROM $this->nombreTabla $condiciones";
        $respuesta = $this->conexion->query($consulta);
        
        if (!$respuesta) {
            die("Error al eliminar: " . $this->conexion->error);
        }else {
            echo 'Eliminacion exitosa';
        }

        
        return $respuesta;
    }
}