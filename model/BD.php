<?php 
class BD
{
    private $conexion;
    protected $nombreTabla;

    function __construct()
    {
        
        $this->conexion = new mysqli('94.23.161.188', 'nextmacrosystem_ronald_bismar', 'o?!Ao^NnrNwh', 'nextmacrosystem_choquelec_mvc');
        // Usar MYSQLI_CLIENT_PERSISTENT para conexi車n persistente
        // $this->conexion = new mysqli('bpd8jelghf6igtd6gi28-mysql.services.clever-cloud.com', 'uc5kajgajjlmiubh', '95sQKgUZZK68X0LcjM9d', 'bpd8jelghf6igtd6gi28');
        
        // $this->conexion = new mysqli('localhost', 'root', '', 'bpd8jelghf6igtd6gi28');


        if ($this->conexion->connect_error) {
            die("Conexi車n fallida: " . $this->conexion->connect_error);
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
        } else {
            echo "Registro exitoso";
        }

        return $respuesta;
    }

    function seleccionar($campos = '*', $innerjoin = '', $condiciones = '', $ordenamiento = '', $limite = '')
    {
        $condiciones = $condiciones ? "WHERE $condiciones" : '';
        $ordenamiento = $ordenamiento ? "ORDER BY $ordenamiento" : '';
        $limite = $limite ? "LIMIT $limite" : '';

        $consulta = "SELECT $campos FROM $this->nombreTabla $innerjoin $condiciones $ordenamiento $limite";

        // echo "Consulta de seleccionar: ".$consulta;

        $respuesta = $this->conexion->query($consulta);

        $datos = [];
        
        if (!$respuesta) {
            echo("Error al seleccionar: " . $this->conexion->error);
        } else {
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
            if ($valor != '') {
                $pares[] = "$campo='$valor'";
            }
        }
    
        // Unir los pares con comas
        $camposValores = implode(', ', $pares);
        
        $set = "SET $camposValores";
        $where = "WHERE $condicion";
    
        $consulta = $pares != [] ? "UPDATE $this->nombreTabla $set $where" : '';

        echo "Consultas: ".$consulta;
    
        if ($consulta != '') {
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

        echo "Consulta de eliminar: ".$consulta;
        $respuesta = $this->conexion->query($consulta);
        
        if (!$respuesta) {
            die("Error al eliminar: " . $this->conexion->error);
        } else {
            echo 'Eliminacion exitosa';
        }

        return $respuesta;
    }
    function insertarConProcedimientoAlmacenado($valoresEntrada, $nombreProcedimiento)
    {
        $valores = "'" . implode("','", array_values($valoresEntrada)) . "'";
        $consulta = "CALL $nombreProcedimiento($valores)";
        echo "Consulta de insertar con procedimiento almacenado: ".$consulta;
        $respuesta = $this->conexion->query($consulta);

        if (!$respuesta) {
            die("Error al insertar: " . $this->conexion->error);
        } else {
            echo "Registro exitoso";
        }
        return $respuesta;
    }
}
