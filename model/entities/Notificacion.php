<?php

class Notificacion {
    public $idNotificacion;
    public $fueLeida;
    public $fueAceptada;
    public $fuePospuesta;
    public $nuevoProyecto;
    public $mensaje;
    public $trabajadorDestinatario;
    public $nombreCreadorDeProyecto;
    // Constructor
    function __construct($idNotificacion = null, $fueLeida = false, $fueAceptada = false, $fuePospuesta = false, $nuevoProyecto = 0, $trabajadorDestinatario = 0, $mensaje = '', $nombreCreadorDeProyecto = '') {
        $this->idNotificacion = $idNotificacion;
        $this->fueLeida = $fueLeida;
        $this->fueAceptada = $fueAceptada;
        $this->fuePospuesta = $fuePospuesta;
        $this->nuevoProyecto = $nuevoProyecto;
        $this->nombreCreadorDeProyecto = $nombreCreadorDeProyecto;
        $this->trabajadorDestinatario = $trabajadorDestinatario;
        $this->mensaje = $mensaje;
    }

    public function toArray() {
        $array = [
            'fueLeida' => $this->fueLeida ? 1 : 0,
            'fueAceptada' => $this->fueAceptada ? 1 : 0,
            'fuePospuesta' => $this->fuePospuesta ? 1 : 0,
            'nombreCreadorDeProyecto' => $this->nombreCreadorDeProyecto,
            'nuevoProyecto' => $this->nuevoProyecto,
            'trabajadorDestinatario' => $this->trabajadorDestinatario,
            'mensaje' => $this->mensaje,
        ];
    
        // Condicionalmente agregar 'idNotificacion'
        if ($this->idNotificacion !== null) {
            $array['idNotificacion'] = $this->idNotificacion;
        }
    
        return $array;
    }
    
    public static function fromArray($data) {
        return new Notificacion(
            isset($data['idNotificacion']) ? intval($data['idNotificacion']) : null,
            $data['fueLeida'] == 1,
            $data['fueAceptada'] == 1,
            $data['fuePospuesta'] == 1,
            intval($data['nuevoProyecto'] ?? 0),
            intval($data['trabajadorDestinatario'] ?? 0),
            $data['mensaje'] ?? '',
            $data['nombreCreadorDeProyecto'] ?? ''
        );
    }

    public function __toString() {
        return sprintf(
            'Notificacion(idNotificacion: %s, fueLeida: %s, fueAceptada: %s, fuePospuesta: %s, trabajadorDestinatario: %d, nuevoProyecto: %d, mensaje: %s)',
            $this->idNotificacion ?? 'null',
            $this->fueLeida ? 'true' : 'false',
            $this->fueAceptada ? 'true' : 'false',
            $this->fuePospuesta ? 'true' : 'false',
            $this->nombreCreadorDeProyecto,
            $this->trabajadorDestinatario,
            $this->nuevoProyecto,
            $this->mensaje
        );
    }
}
