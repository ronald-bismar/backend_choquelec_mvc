<?php
class CoordenadaDMS {

    public $id;
    public $grados;
    public $minutos;
    public $segundos;
    public $hemisferio;
    public $tipo;

    // Constructor con la conexión a la base de datos
    function __construct($id = null, $grados = '', $minutos  = '' , $segundos = '', $hemisferio  = '', $tipo  = '' ) {
        $this->id = $id;
        $this->grados = $grados;
        $this->minutos  = $minutos;
        $this->segundos  = $segundos;
        $this->hemisferio  = $hemisferio;
        $this->tipo  = $tipo;
    }

    public function toArray() {
        $array = [
            'grados ' => $this->grados ,
            'minutos ' => $this->minutos ,
            'segundos ' => $this->segundos ,
            'hemisferio ' => $this->hemisferio ,
            'tipo' => $this->tipo ,
        ];
    
        // Condicionalmente agregar 'idTrabajador'
        if ($this->id !== null) {
            $array['id'] = $this->id;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new CoordenadaDMS(
            $data['id'],
            $data['grados '],
            $data['minutos '],
            $data['segundos'],
            $data['hemisferio '],
            $data['tipo'],
        );
    }
}