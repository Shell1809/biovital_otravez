<?php
// controlador/PanelController.php
class PanelController {
    
    public function paciente() {
        if ($_SESSION['rol'] !== 'paciente') {
            redirect('login');
        }
        renderView('paciente/pac_catalogo');
    }
    
    public function medico() {
        if ($_SESSION['rol'] !== 'medico') {
            redirect('login');
        }
        renderView('medico/med_catalogo');
    }
    
    public function asistente() {
        if ($_SESSION['rol'] !== 'asistente') {
            redirect('login');
        }
        renderView('asistente/asi_catalogo');
    }
    
    public function administrador() {
        if ($_SESSION['rol'] !== 'administrador') {
            redirect('login');
        }
        renderView('administrador/adm_catalogo');
    }
}
?>