<?php
class PacienteController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'paciente') {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
            } else {
                redirect('login/paciente');
            }
            exit();
        }
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    // Buscar paciente (cargar datos)
    public function buscar() {
        $id_paciente = $_POST['dato'] ?? $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::buscar - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $paciente = new Paciente();
        $fecha_actual = new DateTime();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
            jsonResponse(['error' => 'No se encontró el paciente']);
            return;
        }
        
        $json = array();
        foreach ($paciente->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_pac;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_paciente) && $objeto->avatar_paciente != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_paciente 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
                'nombre' => $objeto->nombre_paciente ?? '',
                'apellidos' => $objeto->apellido_paciente ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_paciente ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Paciente',
                'telefono' => $objeto->telefono_paciente ?? '',
                'direccion' => $objeto->direccion_paciente ?? '',
                'correo' => $objeto->correo_paciente ?? '',
                'sexo' => $objeto->sexo_paciente ?? '',
                'adicional' => $objeto->adicional_paciente ?? '',
                'avatar' => $avatar_path
            );
        }
        jsonResponse($json);
    }
    
    // Capturar datos para edición
    public function capturarDatos() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::capturarDatos - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $paciente = new Paciente();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
            jsonResponse(['error' => 'No se encontró el paciente']);
            return;
        }
        
        $json = array();
        foreach ($paciente->objetos as $objeto) {
            $json = array(
                'telefono' => $objeto->telefono_paciente ?? '',
                'direccion' => $objeto->direccion_paciente ?? '',
                'correo' => $objeto->correo_paciente ?? '',
                'sexo' => $objeto->sexo_paciente ?? '',
                'adicional' => $objeto->adicional_paciente ?? ''
            );
        }
        jsonResponse($json);
    }
    
    // Editar paciente
    public function editarUsuario() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::editarUsuario - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            jsonResponse(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $paciente = new Paciente();
        $paciente->editar($id_paciente, $telefono, $direccion, $correo, $sexo, $adicional);
        
        jsonResponse(['success' => true, 'message' => 'editado']);
    }
    
    // Cambiar foto de perfil
    public function cambiarFoto() {
        $id_paciente = $_SESSION['usuario'];
        
        if (empty($id_paciente)) {
            jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            return;
        }
        
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
            return;
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido']);
            return;
        }
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nombre = uniqid() . '.' . $extension;
        $ruta_destino = '../img/' . $nombre;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
            $paciente = new Paciente();
            $avatar_anterior = $paciente->cambiar_photo($id_paciente, $nombre);
            
            // Eliminar avatar anterior si no es el default
            if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
                $ruta_anterior = '../img/' . $avatar_anterior;
                if (file_exists($ruta_anterior)) {
                    @unlink($ruta_anterior);
                }
            }
            
            jsonResponse(['ruta' => APP_URL . '/img/' . $nombre, 'alert' => 'edit']);
        } else {
            jsonResponse(['alert' => 'noedit', 'error' => 'Error al mover el archivo']);
        }
    }
    
    // Cambiar contraseña
    public function cambiarPassword() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
            jsonResponse(['resultado' => 'noupdate']);
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
            jsonResponse(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }
        
        $loginPaciente = new LoginPaciente();
        ob_start();
        $loginPaciente->cambiar_contra($id_paciente, $oldpass, $newpass);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // Mis estadísticas
    public function misEstadisticas() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $paciente = new Paciente();
        $total_recetas = $paciente->contarRecetas($id_paciente);
        
        jsonResponse([
            'total_recetas' => $total_recetas,
            'proximas_citas' => 0
        ]);
    }
    
    // Vista: Mis recetas
    public function recetas() {
        renderView('paciente/pac_recetas');
    }
}
?>