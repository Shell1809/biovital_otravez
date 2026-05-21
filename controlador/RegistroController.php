<?php
class RegistroController {
    
    public function showRegistroPaciente() {
        renderView('registro_pac');
    }
    
    public function showRegistroMedico() {
        renderView('med_registro');
    }
    
    public function showRegistroAsistente() {
        renderView('registro_asistente');
    }
    
    public function showRegistroAdministrador() {
        renderView('registro_administrador');
    }
    
    public function crearPaciente() {
        $this->crearUsuario('Paciente');
    }
    
    public function crearMedico() {
        $this->crearUsuario('Medico');
    }
    
    public function crearAsistente() {
        $this->crearUsuario('Asistente');
    }
    
    public function crearAdministrador() {
        $this->crearUsuario('Administrador');
    }
    
    private function crearUsuario($tipo) {
        // Verificar CSRF
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            jsonResponse(['success' => false, 'message' => 'Token CSRF inválido']);
            return;
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        $pass = $_POST['pass'] ?? '';
        
        $tipos = ['Paciente' => 1, 'Medico' => 2, 'Asistente' => 3, 'Administrador' => 4];
        $tipoId = $tipos[$tipo] ?? 1;
        
        $errores = [];
        if (empty($nombre)) $errores[] = 'Nombre requerido';
        if (empty($apellidos)) $errores[] = 'Apellidos requeridos';
        if (empty($cedula)) $errores[] = 'Cédula requerida';
        if (empty($pass)) $errores[] = 'Contraseña requerida';
        if (strlen($pass) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        
        if (!empty($errores)) {
            jsonResponse(['success' => false, 'message' => implode(', ', $errores)]);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        $avatar = 'avatarDES.jpg';
        
        $clase = "\\{$tipo}";
        $usuario = new $clase();
        
        ob_start();
        $usuario->crear($nombre, $apellidos, $fecha_nacimiento, $cedula, $telefono, 
                      $direccion, $correo, $sexo, $adicional, $password_hash, $tipoId, $avatar);
        $resultado = trim(ob_get_clean());
        
        if ($resultado == 'add') {
            jsonResponse(['success' => true, 'message' => "Cuenta de {$tipo} creada exitosamente"]);
        } else if ($resultado == 'existe') {
            jsonResponse(['success' => false, 'message' => 'Ya existe un usuario con esta cédula o correo']);
        } else {
            jsonResponse(['success' => false, 'message' => "Error al crear la cuenta"]);
        }
    }
}
?>