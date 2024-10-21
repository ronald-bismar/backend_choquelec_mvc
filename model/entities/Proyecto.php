<?php
class Proyecto {

    public $idProyecto;
    public $nombre;
    public $ubicacion;
    public $estaCompleta;
    public $fechaRegistro;
    public $idSupervisor;
    public $idResidenteDeObra;
    public $imagenProyecto;
    public $activo;

    // Constructor con la conexi¨®n a la base de datos
    function __construct($idProyecto = null, $nombre = '', $ubicacion = '', $estaCompleta = false, $fechaRegistro = '', $idSupervisor = 0, $idResidenteDeObra = null, $activo = '1', $imagenProyecto = null) {
        $this->idProyecto = $idProyecto;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
        $this->estaCompleta = $estaCompleta;
        $this->fechaRegistro = $fechaRegistro;
        $this->idSupervisor = $idSupervisor;
        $this->idResidenteDeObra = $idResidenteDeObra;
        $this->activo = $activo;
        $this->imagenProyecto = $imagenProyecto;
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
        if ($this->idProyecto !== null) {
            $array['idProyecto'] = $this->idProyecto;
        }
        if ($this->imagenProyecto !== null) {
            $array['imagenProyecto'] = $this->imagenProyecto;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new Proyecto(
            idProyecto:$data['idProyecto']?? null,
            nombre: $data['nombre'],
            ubicacion: $data['ubicacion'],
            estaCompleta: $data['estaCompleta'],
            fechaRegistro: $data['fechaRegistro'] == ''? date("Y-m-d H:i:s"): $data['fechaRegistro'],
            idSupervisor: $data['idSupervisor'],
            idResidenteDeObra: $data['idResidenteDeObra'] == 1,
            activo: $data['activo'],
            imagenProyecto: $data['imagenProyecto']
        );
    }
}