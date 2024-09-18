<?php

class CoordenadasDMS {

    public $idCoordenadasDMS;
    public $idLatitudDMS;
    public $idLongitudDMS;

    // Constructor con la conexión a la base de datos
    function __construct($idCoordenadasDMS = null, $idLatitudDMS = '', $idLongitudDMS  = '' ) {
        $this->idCoordenadasDMS = $idCoordenadasDMS;
        $this->idLatitudDMS = $idLatitudDMS;
        $this->idLongitudDMS  = $idLongitudDMS ;
    }

    public function toArray() {
        $array = [
            'latitud_id' => $this->idLatitudDMS ,
            'longitud_id' => $this->idLongitudDMS ,
        ];
    
        // Condicionalmente agregar 'idTrabajador'
        if ($this->idCoordenadasDMS !== null) {
            $array['idCoordenadasDMS'] = $this->idCoordenadasDMS;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new CoordenadasDMS(
            $data['idCoordenadasDMS'],
            $data['latitud_id'],
            $data['longitud_id'],
        );
    }
}