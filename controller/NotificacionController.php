<?php
require_once "model/entities/Notificacion.php";
require_once "model/NotificacionModel.php";

class NotificacionController
{
    private NotificacionModel $notificacionModel;

    public function __construct()
    {
        $this->notificacionModel = new NotificacionModel();
    }

    private function obtenerDatosJson(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function guardar()
    {
        $data = $this->obtenerDatosJson();
    
        try {
            $notificacionData = [
                $data['nuevoProyecto'],
                $data['trabajadorDestinatario'],
                $data['fueLeida'] ?? false,
                $data['fueAceptada'] ?? false,
                $data['nombreCreadorDeProyecto'] ?? '',
                $data['mensaje'] ?? '',
                $data['fuePospuesta'] ?? false
            ];
    
            if ($this->notificacionModel->insertarConProcedimientoAlmacenado(valoresEntrada: $notificacionData, nombreProcedimiento: "InsertNotificacion")) {
                return "Notificación creada correctamente";
            } else {
                throw new Exception("Error al crear la notificación.");
            }
        } catch (Exception $e) {
            return "Ocurrió un error: " . $e->getMessage();
        }
    }

    public function marcarComoLeida()
    {
        $idNotificacion = $_POST['idNotificacion'] ?? '';
        return $this->notificacionModel->actualizar(['fueLeida' => 1], "idNotificacion = '$idNotificacion'")
            ? "Notificación marcada como leída" 
            : "Error al marcar la notificación como leída.";
    }

    public function marcarComoAceptada()
    {
        $idNotificacion = $_POST['idNotificacion'] ?? '';
        $idTrabajador = $_POST['idTrabajador'] ?? '';
        $idProyecto = $_POST['idProyecto'] ?? '0';
        return $this->notificacionModel->insertarConProcedimientoAlmacenado(valoresEntrada: [$idNotificacion, $idTrabajador, $idProyecto], nombreProcedimiento: "MarcarNotificacionComoAceptadaYAsignarProyecto")
            ? "Notificación marcada como aceptada" 
            : "Error al marcar la notificación como aceptada.";
    }

    public function marcarComoPospuesta()
    {
        $idNotificacion = $_POST['idNotificacion'] ?? '';
        return $this->notificacionModel->actualizar(['fuePospuesta' => 1], "idNotificacion = '$idNotificacion'")
            ? "Notificación marcada como pospuesta" 
            : "Error al marcar la notificación como pospuesta.";
    }

    public function obtenerNotificaciones()
    {
        $idTrabajador = $_POST['idTrabajador'] ?? '';
        $notificaciones = $this->notificacionModel->seleccionar("*", condiciones: "trabajadorDestinatario = '$idTrabajador'");
        header('Content-Type: application/json');
        echo json_encode($notificaciones ?: null);
    }

    public function eliminar()
    {
        $idNotificacion = $_POST['idNotificacion'] ?? '';
        return $this->notificacionModel->eliminar("idNotificacion = '$idNotificacion'")
            ? "Notificación eliminada correctamente" 
            : "Error al eliminar la notificación.";
    }

    public function listar()
    {
        $notificaciones = $this->notificacionModel->seleccionar();
        header('Content-Type: application/json');
        echo json_encode($notificaciones ?: null);
    }

    public function obtenerNotificacionesPendientesDeTrabajador()
    {
        $idTrabajador = $_POST['idTrabajador'] ?? '34';
        $notificaciones = $this->notificacionModel->seleccionar("*", condiciones: "trabajadorDestinatario = '$idTrabajador' AND fueLeida = 0 AND fueAceptada = 0 AND fuePospuesta = 0");
        header('Content-Type: application/json');
        echo json_encode($notificaciones ?: null);
    }
    public function guardarNotificacionesDeOperarios()
    {
        $data = $this->obtenerDatosJson();
    
        try {
            $notificaciones = $data['notificaciones'] ?? [];
            $notificacionesData = [];
    
            foreach ($notificaciones as $notificacion) {
                $notificacionesData[] = [
                    $notificacion['nuevoProyecto'],
                    $notificacion['trabajadorDestinatario'],
                    $notificacion['fueLeida'] ?? false,
                    $notificacion['fueAceptada'] ?? false,
                    $notificacion['fuePospuesta'] ?? false,
                    $notificacion['mensaje'] ?? '',
                    $notificacion['nombreCreadorDeProyecto'] ?? ''
                ];
            }
    
            // Convertir el array de notificaciones a JSON
            $notificacionesJSON = json_encode($notificacionesData);
    
            // Insertar las notificaciones en la base de datos usando un procedimiento almacenado
            if ($this->notificacionModel->insertarConProcedimientoAlmacenado(
                valoresEntrada: [$notificacionesJSON],
                nombreProcedimiento: "InsertarMultiplesNotificaciones"
            )) {
                return json_encode(['mensaje' => 'Notificaciones guardadas correctamente']);
            } else {
                throw new Exception("Error al insertar las notificaciones.");
            }
        } catch (Exception $e) {
            return json_encode(['error' => "Ocurrió un error: " . $e->getMessage()]);
        }
    }
}
