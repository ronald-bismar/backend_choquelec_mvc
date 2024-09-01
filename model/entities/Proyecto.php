<?php
class Proyecto {
    private $conn;
    private $table_name = "proyecto";

    public $idProyecto;
    public $nombre;
    public $ubicacion;
    public $estaCompleta;
    public $fechaRegistro;
    public $idSupervisor;
    public $idResidenteDeObra;

    // Constructor con la conexión a la base de datos
    function __construct($idProyecto = null, $nombre = '', $ubicacion = '', $estaCompleta = false, $fechaRegistro = '', $idSupervisor = 0, $idResidenteDeObra = 0) {
        $this->idProyecto = $idProyecto;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
        $this->estaCompleta = $estaCompleta;
        $this->fechaRegistro = $fechaRegistro;
        $this->idSupervisor = $idSupervisor;
        $this->idResidenteDeObra = $idResidenteDeObra;
    }

    public function toArray() {
        $array = [
            'nombre' => $this->nombre,
            'ubicacion' => $this->ubicacion,
            'estaCompleta' => $this->estaCompleta,
            'fechaRegistro' => $this->fechaRegistro,
            'idSupervisor' => $this->idSupervisor,
            'idResidenteDeObra' => $this->idResidenteDeObra,
        ];
    
        // Condicionalmente agregar 'idTrabajador'
        if ($this->idProyecto !== null) {
            $array['idProyecto'] = $this->idProyecto;
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
}
