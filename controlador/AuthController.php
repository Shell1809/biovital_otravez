<?php
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
/**
 * AuthController.php
 * Controlador para la autenticación de usuarios
 */

<<<<<<< HEAD
=======
class AuthController {
    
    /**
     * Muestra el login de paciente (redirige a home con parámetro)
     */
    public function showLoginPaciente() {
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
class AuthController {
    
    /**
     * Muestra el login de paciente (redirige a home con parámetro)
     */
    public function showLoginPaciente() {
<<<<<<< HEAD
=======
        // Redirigir a la página principal con el parámetro para abrir el login
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        header('Location: ' . APP_URL . '?openLogin=paciente');
        exit();
    }
    
<<<<<<< HEAD
    /**
     * Muestra el login de médico (redirige a home con parámetro)
     */
=======
<<<<<<< HEAD
    /**
     * Muestra el login de médico (redirige a home con parámetro)
     */
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function showLoginMedico() {
        header('Location: ' . APP_URL . '?openLogin=medico');
        exit();
    }
    
<<<<<<< HEAD
    /**
     * Muestra el login de asistente (redirige a home con parámetro)
     */
=======
<<<<<<< HEAD
    /**
     * Muestra el login de asistente (redirige a home con parámetro)
     */
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function showLoginAsistente() {
        header('Location: ' . APP_URL . '?openLogin=asistente');
        exit();
    }
    
<<<<<<< HEAD
    /**
     * Muestra el login de administrador (redirige a home con parámetro)
     */
=======
<<<<<<< HEAD
    /**
     * Muestra el login de administrador (redirige a home con parámetro)
     */
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function showLoginAdministrador() {
        header('Location: ' . APP_URL . '?openLogin=administrador');
        exit();
    }
    
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    /**
     * Procesa el inicio de sesión
     * POST /login
     */
<<<<<<< HEAD
=======
    public function login() {
        // Obtener datos del formulario
        $user = trim($_POST['user'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $rol = $_POST['rol'] ?? '';
        
        // Validar que los campos no estén vacíos
        if (empty($user) || empty($pass) || empty($rol)) {
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            } else {
                redirect('?error=1');
            }
            return;
        }
        
        // Mapeo de roles a clases y campos
        $loginMap = [
            'paciente' => [
                'class' => 'LoginPaciente', 
                'idField' => 'id_paciente', 
                'tipoField' => 'paciente_tipo', 
                'nameField' => 'nombre_paciente'
            ],
            'medico' => [
                'class' => 'LoginMedico', 
                'idField' => 'id_medico', 
                'tipoField' => 'medico_tipo', 
                'nameField' => 'nombre_medico'
            ],
            'asistente' => [
                'class' => 'LoginAsistente', 
                'idField' => 'id_asistente', 
                'tipoField' => 'asistente_tipo', 
                'nameField' => 'nombre_asistente'
            ],
            'administrador' => [
                'class' => 'LoginAdministrador', 
                'idField' => 'id_administrador', 
                'tipoField' => 'administrador_tipo', 
                'nameField' => 'nombre_administrador'
            ]
        ];
        
        // Validar rol
        if (!isset($loginMap[$rol])) {
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Rol inválido']);
            } else {
                redirect('?error=1');
            }
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function login() {
        // Obtener datos del formulario
        $user = trim($_POST['user'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $rol = $_POST['rol'] ?? '';
        
        // Validar que los campos no estén vacíos
        if (empty($user) || empty($pass) || empty($rol)) {
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            } else {
                redirect('?error=1');
            }
            return;
        }
        
        // Mapeo de roles a clases y campos
        $loginMap = [
            'paciente' => [
                'class' => 'LoginPaciente', 
                'idField' => 'id_paciente', 
                'tipoField' => 'paciente_tipo', 
                'nameField' => 'nombre_paciente'
            ],
            'medico' => [
                'class' => 'LoginMedico', 
                'idField' => 'id_medico', 
                'tipoField' => 'medico_tipo', 
                'nameField' => 'nombre_medico'
            ],
            'asistente' => [
                'class' => 'LoginAsistente', 
                'idField' => 'id_asistente', 
                'tipoField' => 'asistente_tipo', 
                'nameField' => 'nombre_asistente'
            ],
            'administrador' => [
                'class' => 'LoginAdministrador', 
                'idField' => 'id_administrador', 
                'tipoField' => 'administrador_tipo', 
                'nameField' => 'nombre_administrador'
            ]
        ];
        
        // Validar rol
        if (!isset($loginMap[$rol])) {
<<<<<<< HEAD
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Rol inválido']);
            } else {
                redirect('?error=1');
            }
=======
            if ($isAjax) jsonResponse(['success' => false, 'error' => 'Rol inválido']);
            redirect('?error=1');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $map = $loginMap[$rol];
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        // Verificar que la clase existe
        if (!class_exists($map['class'])) {
            error_log("Clase no encontrada: " . $map['class']);
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Error interno del servidor']);
            } else {
                redirect('?error=1');
            }
            return;
        }
        
        // Intentar login
<<<<<<< HEAD
=======
        $login = new $map['class']();
        $login->Loguearse($user, $pass);
        
        // Verificar si el login fue exitoso
        if (!empty($login->objetos)) {
            foreach ($login->objetos as $objeto) {
                // Guardar datos en sesión
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        $login = new $map['class']();
        $login->Loguearse($user, $pass);
        
        // Verificar si el login fue exitoso
        if (!empty($login->objetos)) {
            foreach ($login->objetos as $objeto) {
<<<<<<< HEAD
                // Guardar datos en sesión
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                $_SESSION['usuario'] = $objeto->{$map['idField']};
                $_SESSION['us_tipo'] = $objeto->{$map['tipoField']};
                $_SESSION['nombre_us'] = $objeto->{$map['nameField']};
                $_SESSION['rol'] = $rol;
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                
                // Actualizar último acceso
                if (method_exists($login, 'actualizarUltimoAcceso')) {
                    $login->actualizarUltimoAcceso($objeto->{$map['idField']});
                }
<<<<<<< HEAD
=======
            }
            
            // Construir URL de redirección (sin APP_URL al principio porque redirect ya lo agrega)
            $redirectUrl = 'panel/' . $rol;
            
            if ($this->isAjax()) {
                jsonResponse(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                redirect($redirectUrl);
            }
        } else {
            // Login fallido
            if ($this->isAjax()) {
=======
                $login->actualizarUltimoAcceso($objeto->{$map['idField']});
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            }
            
            // Construir URL de redirección (sin APP_URL al principio porque redirect ya lo agrega)
            $redirectUrl = 'panel/' . $rol;
            
            if ($this->isAjax()) {
                jsonResponse(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                redirect($redirectUrl);
            }
        } else {
<<<<<<< HEAD
            // Login fallido
            if ($this->isAjax()) {
=======
            if ($isAjax) {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                jsonResponse(['success' => false, 'error' => 'Cédula o contraseña incorrecta']);
            } else {
                redirect('?error=1');
            }
        }
    }
    
<<<<<<< HEAD
    /**
     * Cierra la sesión del usuario
     */
=======
<<<<<<< HEAD
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al inicio
        redirect('');
    }
    
    /**
     * Verifica si la petición es AJAX
     * @return bool
     */
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al inicio
        redirect('');
    }
<<<<<<< HEAD
    
    /**
     * Verifica si la petición es AJAX
     * @return bool
     */
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
}
?>