<?php
class Trabajador {
    public $idTrabajador;
    public $nombre;
    public $apellido;
    public $cedulaDeIdentidad;
    public $contrasenia;
    public $tipoDeTrabajador;
    public $activo;

    public function __construct($nombre = '', $apellido = '', $contrasenia = '', $tipoDeTrabajador = '', $cedulaDeIdentidad = '', $activo = true, $idTrabajador = null) {
        $this->idTrabajador = $idTrabajador;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cedulaDeIdentidad = $cedulaDeIdentidad;
        $this->contrasenia = $contrasenia;
        $this->tipoDeTrabajador = $tipoDeTrabajador;
        $this->activo = $activo;
    }

    public function toArray() {
        $array = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cedulaDeIdentidad' => $this->cedulaDeIdentidad,
            'contrasenia' => $this->contrasenia,
            'tipoDeTrabajador' => $this->getTypeToNumber($this->tipoDeTrabajador),
            'activo' => $this->activo ? 1 : 0,
        ];
    
        // Condicionalmente agregar 'idTrabajador'
        if ($this->idTrabajador !== null) {
            $array['idTrabajador'] = $this->idTrabajador;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new Trabajador(
            $data['nombre'],
            $data['apellido'],
            $data['contrasenia'],
            $data['tipoDeTrabajador'],
            $data['cedulaDeIdentidad'],
            $data['activo'] == 1,
            $data['idTrabajador']
        );
    }
    function getTypeToNumber($tipo)
    {
        $level = '';
        if($tipo == 'ADMINISTRADOR')
        $level = 1;
        else if($tipo == 'SUPERVISOR')
        $level = 2;
        else if($tipo == 'RESIDENTE')
        $level = 3;
        else
        $level = 4;
        return $level;
    }
    function getTypeToString($level): string
    {
        $type = '';
        if($level == 1)
        $level = 'ADMINISTRADOR';
        else if($level == 2)
        $level = 'SUPERVISOR';
        else if($level == 3)
        $level = 'RESIDENTE';
        else
        $level = 'OPERARIO';
        return $type;
    }
}