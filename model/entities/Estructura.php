<?php
class Estructura {

    public $idEstructura;
    public $nombre;
    public $imagenEstructura;
    public $imagenGPS;
    public $ubicacionUTM;
    public $ubicacionDMS;
    public $estaCompleta;
    public $fechaRegistro;
    public $idProyecto;
    public $idOperadorAsignado;

    // Constructor con la conexión a la base de datos
    function __construct($idEstructura = null, $nombre = '', $imagenEstructura  = '', $imagenGPS  = '', $ubicacionUTM  = '', $ubicacionDMS = '', $estaCompleta = 0, $fechaRegistro = '', $idProyecto = '', $idOperadorAsignado = '' ) {
        $this->idEstructura = $idEstructura;
        $this->nombre = $nombre;
        $this->imagenEstructura  = $imagenEstructura ;
        $this->imagenGPS  = $imagenGPS ;
        $this->ubicacionUTM  = $ubicacionUTM;
        $this->ubicacionDMS = $ubicacionDMS;
        $this->estaCompleta = $estaCompleta;
        $this->fechaRegistro  = $fechaRegistro;
        $this->idProyecto  = $idProyecto;
        $this->idOperadorAsignado  = $idOperadorAsignado;
    }

    public function toArray() {
        $array = [
            'nombre' => $this->nombre,
            'imagenEstructura ' => $this->imagenEstructura ,
            'imagenGPS ' => $this->imagenGPS ,
            'ubicacionUTM ' => $this->ubicacionUTM ,
            'ubicacionDMS' => $this->ubicacionDMS,
            'estaCompleta' => $this->estaCompleta,
            'fechaRegistro ' => $this->fechaRegistro ,
            'idProyecto ' => $this->idProyecto ,
            'idOperadorAsignado ' => $this->idOperadorAsignado ,
        ];
    
        // Condicionalmente agregar 'idTrabajador'
        if ($this->idEstructura !== null) {
            $array['idEstructura'] = $this->idEstructura;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new Estructura(
            $data['nombre'],
            $data['imagenEstructura '],
            $data['imagenGPS '],
            $data['ubicacionUTM '],
            $data['ubicacionDMS'],
            $data['estaCompleta'] == 1,
            $data['idTrabajador'],
            $data['fechaRegistro'],
            $data['idProyecto'],
            $data['idOperadorAsignado']
        );
    }
}