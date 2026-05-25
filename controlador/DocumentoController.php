<?php
class DocumentoController {
    private $documento;
    private $tiposPermitidos = ['recipes', 'indicaciones', 'justificativos', 'constancia-estudio', 'constancia-trabajo', 'diagnostico', 'laboratorio'];

    public function __construct() {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            redirect('login');
        }

        if (!in_array($_SESSION['rol'], ['paciente', 'asistente'])) {
            redirect('login');
        }

        require_once MODEL_PATH . '/DocumentoMedico.php';
        $this->documento = new DocumentoMedico();
    }

    public function index() {
        $documentos = $this->documento->listarPorRol($_SESSION['rol'], $_SESSION['usuario']);
        renderView('documentos/index', ['documentos' => $documentos]);
    }

    public function ver() {
        $tipo = $_GET['tipo'] ?? '';
        $id = (int)($_GET['id'] ?? 0);

        if (!in_array($tipo, $this->tiposPermitidos) || $id <= 0) {
            http_response_code(404);
            echo 'Documento no encontrado';
            return;
        }

        $documento = $this->documento->obtenerPorId($id, $_SESSION['rol'], $_SESSION['usuario']);

        if (!$documento) {
            http_response_code(404);
            echo 'Documento no encontrado';
            return;
        }

        renderView('documentos/print', ['documento' => $documento, 'tipo' => $tipo]);
    }
}
?>
