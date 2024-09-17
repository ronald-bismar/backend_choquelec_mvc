<?php
class Imagen {

    public $idImagen;
    public $urlImagen;
    public $tipoImagen;
    public $fechaCaptura;
    public $activo;

    // Constructor con la conexión a la base de datos
    function __construct($idImagen = null, $urlImagen = '', $tipoImagen  = '', $fechaCaptura  = '',$activo  = '' ) {
        $this->idImagen = $idImagen;
        $this->urlImagen = $urlImagen;
        $this->tipoImagen  = $tipoImagen ;
        $this->fechaCaptura  = $fechaCaptura ;
        $this->activo = $activo;
    }

    public function toArray() {
        $array = [
            'urlImagen ' => $this->urlImagen ,
            'tipoImagen ' => $this->tipoImagen ,
            'fechaCaptura ' => $this->fechaCaptura ,
            'activo' => $this->activo,
        ];
    
        // Condicionalmente agregar 'idTrabajador'
        if ($this->idImagen !== null) {
            $array['idImagen'] = $this->idImagen;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new Imagen(
            $data['idImagen'],
            $data['urlImagen '],
            $data['tipoImagen '],
            $data['fechaCaptura '],
            $data['activo'],
        );
    }
}