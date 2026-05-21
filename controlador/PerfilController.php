<?php
class PerfilController {
    
    private $rol;
    private $id;
    
    public function __construct() {
        $this->rol = $_SESSION['rol'];
        $this->id = $_SESSION['usuario'];
    }
    
    public function index() {
        $vistas = [
            'paciente' => 'paciente/pac_editar_datos',
            'medico' => 'medico/med_editar_datos',
            'asistente' => 'asistente/asi_editar_datos',
            'administrador' => 'administrador/adm_editar_datos'
        ];
        
        if (isset($vistas[$this->rol])) {
            renderView($vistas[$this->rol]);
        } else {
            redirect('login');
        }
    }
    
    public function getDatos() {
        $controllerName = ucfirst($this->rol) . 'Controller';
        $controllerFile = CONTROLLER_PATH . DS . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();
            
            if (method_exists($controller, 'buscar')) {
                $controller->buscar();
            } else {
                jsonResponse(['error' => 'Método no encontrado'], 500);
            }
        } else {
            jsonResponse(['error' => 'Controlador no encontrado'], 500);
        }
    }
    
    public function editar() {
        $controllerName = ucfirst($this->rol) . 'Controller';
        $controllerFile = CONTROLLER_PATH . DS . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();
            
            if (method_exists($controller, 'editarUsuario')) {
                $controller->editarUsuario();
            } else {
                jsonResponse(['success' => false, 'error' => 'Método no encontrado'], 500);
            }
        } else {
            jsonResponse(['success' => false, 'error' => 'Controlador no encontrado'], 500);
        }
    }
    
    public function cambiarFoto() {
        $controllerName = ucfirst($this->rol) . 'Controller';
        $controllerFile = CONTROLLER_PATH . DS . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();
            
            if (method_exists($controller, 'cambiarFoto')) {
                $controller->cambiarFoto();
            } else {
                jsonResponse(['alert' => 'noedit', 'error' => 'Método no encontrado'], 500);
            }
        } else {
            jsonResponse(['alert' => 'noedit', 'error' => 'Controlador no encontrado'], 500);
        }
    }
    
    public function cambiarPassword() {
        $id = $_POST['id'] ?? $this->id;
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        $loginClass = "Login" . ucfirst($this->rol);
        $login = new $loginClass();
        
        ob_start();
        $login->cambiar_contra($id, $oldpass, $newpass);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
}
?>