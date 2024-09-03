<?php
class Proyecto {

    public $idProyecto;
    public $nombre;
    public $ubicacion;
    public $estaCompleta;
    public $fechaRegistro;
    public $idSupervisor;
    public $idResidenteDeObra;
    public $activo;

    // Constructor con la conexión a la base de datos
    function __construct($idProyecto = null, $nombre = '', $ubicacion = '', $estaCompleta = false, $fechaRegistro = '', $idSupervisor = 0, $idResidenteDeObra = null, $activo = '1') {
        $this->idProyecto = $idProyecto;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
        $this->estaCompleta = $estaCompleta;
        $this->fechaRegistro = $fechaRegistro;
        $this->idSupervisor = $idSupervisor;
        $this->idResidenteDeObra = $idResidenteDeObra;
        $this->activo = $activo;
    }

    public function toArray() {
        $array = [
            'nombre' => $this->nombre,
            'ubicacion' => $this->ubicacion,
            'estaCompleta' => $this->estaCompleta,
            'fechaRegistro' => $this->fechaRegistro,
            'idSupervisor' => $this->idSupervisor,
            'idResidenteDeObra' => $this->idResidenteDeObra,
            'activo' => $this->activo,
        ];
    
       
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new Proyecto(
            $data['nombre'],
            $data['ubicacion'],
            $data['estaCompleta'],
            $data['fechaRegistro'],
            $data['idSupervisor'],
            $data['idResidenteDeObra'] == 1,
            $data['idTrabajador'],
            $data['activo']
        );
    }
}