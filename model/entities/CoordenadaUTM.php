<?php

class CoordenadaUTM {

    public $idCoordenadaUTM;
    public $coordenadaX;
    public $coordenadaY;
    public $zonaCartografica;

    function __construct($idCoordenadaUTM = null, $coordenadaX = '0.0', $coordenadaY  = '0.0', $zonaCartografica = '19K' ) {
        $this->idCoordenadaUTM = $idCoordenadaUTM;
        $this->coordenadaX = $coordenadaX;
        $this->coordenadaY  = $coordenadaY ;
        $this->zonaCartografica  = $zonaCartografica;
    }

    public function toArray() {
        $array = [
            'coordenadaX' => $this->coordenadaX ,
            'coordenadaY' => $this->coordenadaY ,
            'zonaCartografica' => $this->zonaCartografica ,
        ];
    
        if ($this->idCoordenadaUTM !== null) {
            $array['idCoordenadaUTM'] = $this->idCoordenadaUTM;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new CoordenadaUTM(
            $data['idCoordenadaUTM'],
            $data['coordenadaX'],
            $data['coordenadaY'],
            $data['zonaCartografica'],
        );
    }
}