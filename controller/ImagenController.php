<?php
require_once "model/entities/Imagen.php";
require_once "model/ImagenModel.php";

class ImagenController
{
    private ImagenModel $imagenModel;

    public function __construct()
    {
        $this->imagenModel = new ImagenModel();
    }

    public function guardar(): string
    {
        $imagen = new Imagen(
            urlImagen: $_POST['urlImagen'] ?? '',
            tipoImagen: $_POST['tipoImagen'] ?? 'estructura',
            fechaCaptura: $_POST['fechaCaptura'] ?? '',
            activo: $_POST['activo'] ?? '1'
        );

        return $this->imagenModel->insertar($imagen->toArray()) 
            ? "Registro insertado correctamente" 
            : "Error al insertar el registro.";
    }

    public function buscarPor(): void
    {
        $tipo = $_POST['tipo'] ?? 'activo';
        $valorBuscado = $_POST['valorBuscado'] ?? '1';
        
        $imagenes = $this->imagenModel->seleccionar("$tipo = '$valorBuscado'");
        $this->enviarJson($imagenes);
    }

    public function obtenerImagenes(): void
    {
        $idImagen = $_POST['idImagen'] ?? '1';
        $imagenes = $this->imagenModel->seleccionar("idImagen = '$idImagen'");
        $this->enviarJson($imagenes[0] ?? null);
    }

    public function actualizar(): string
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $imagen = new Imagen(
            $data['idImagen'] ?? '',
            $data['urlImagen'] ?? '',
            $data['tipoImagen'] ?? '',
            $data['fechaCaptura'] ?? '',
            $data['activo'] ?? '1'
        );

        return $this->imagenModel->actualizar($imagen->toArray(), "idImagen = '{$imagen->idImagen}'")
            ? "Registro actualizado correctamente"
            : "Error al actualizar el registro.";
    }

    public function eliminar(): string
    {
        $idImagen = $_POST['idImagen'] ?? '';
        return $this->imagenModel->eliminar("idImagen = '$idImagen'") 
            ? "Registro eliminado correctamente" 
            : "Error al eliminar el registro.";
    }

    public function listar(): void
    {
        $imagenes = $this->imagenModel->seleccionar();
        $this->enviarJson($imagenes);
    }

    private function enviarJson($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data ?: null);
    }
}
