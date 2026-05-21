<?php
// controlador/AdministradorController.php
class AdministradorController {
    
    public function __construct() {
        // Verificar autenticación y rol
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
            } else {
                redirect('login/administrador');
            }
            exit();
        }
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    // Buscar administrador (cargar datos)
    public function buscar() {
        $id_administrador = $_POST['dato'] ?? $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AdministradorController::buscar - ID: $id_administrador, Sesión: $id_sesion");
        
        if($id_administrador != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $administrador = new Administrador();
        $fecha_actual = new DateTime();
        $administrador->obtener_datos($id_administrador);
        
        if(empty($administrador->objetos)) {
            jsonResponse(['error' => 'No se encontró el administrador']);
            return;
        }
        
        $json = array();
        foreach ($administrador->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_administrador;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_administrador) && $objeto->avatar_administrador != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_administrador 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
                'nombre' => $objeto->nombre_administrador ?? '',
                'apellidos' => $objeto->apellido_administrador ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_administrador ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Administrador',
                'telefono' => $objeto->telefono_administrador ?? '',
                'direccion' => $objeto->direccion_administrador ?? '',
                'correo' => $objeto->correo_administrador ?? '',
                'sexo' => $objeto->sexo_administrador ?? '',
                'adicional' => $objeto->adicional_administrador ?? '',
                'avatar' => $avatar_path
            );
        }
        jsonResponse($json);
    }
    
    // Capturar datos para edición
    public function capturarDatos() {
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AdministradorController::capturarDatos - ID: $id_administrador, Sesión: $id_sesion");
        
        if($id_administrador != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $administrador = new Administrador();
        $administrador->obtener_datos($id_administrador);
        
        if(empty($administrador->objetos)) {
            jsonResponse(['error' => 'No se encontró el administrador']);
            return;
        }
        
        $json = array();
        foreach ($administrador->objetos as $objeto) {
            $json = array(
                'telefono' => $objeto->telefono_administrador ?? '',
                'direccion' => $objeto->direccion_administrador ?? '',
                'correo' => $objeto->correo_administrador ?? '',
                'sexo' => $objeto->sexo_administrador ?? '',
                'adicional' => $objeto->adicional_administrador ?? ''
            );
        }
        jsonResponse($json);
    }
    
    // Editar administrador
    public function editarUsuario() {
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AdministradorController::editarUsuario - ID: $id_administrador, Sesión: $id_sesion");
        
        if($id_administrador != $id_sesion) {
            jsonResponse(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $administrador = new Administrador();
        $administrador->editar($id_administrador, $telefono, $direccion, $correo, $sexo, $adicional);
        
        jsonResponse(['success' => true, 'message' => 'editado']);
    }
    
    // API: Cambiar foto
public function cambiarFoto() {
    $id_administrador = $_SESSION['usuario'];
    
    if (empty($id_administrador)) {
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
        jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido. Use JPG, PNG o GIF']);
        return;
    }
    
    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $nombre = uniqid() . '.' . $extension;
    $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
        $administrador = new Administrador();
        $avatar_anterior = $administrador->cambiar_photo($id_administrador, $nombre);
        
        // Eliminar avatar anterior si no es el default
        if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
            $ruta_anterior = dirname(__DIR__) . '/img/' . $avatar_anterior;
            if (file_exists($ruta_anterior)) {
                @unlink($ruta_anterior);
            }
        }
        
        // Devolver la ruta completa con APP_URL
        jsonResponse([
            'alert' => 'edit', 
            'ruta' => APP_URL . '/img/' . $nombre
        ]);
    } else {
        jsonResponse(['alert' => 'noedit', 'error' => 'Error al mover el archivo']);
    }
    $_SESSION['avatar'] = APP_URL . '/img/' . $nombre;
}

    
    // Cambiar contraseña
    public function cambiarPassword() {
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_administrador != $id_sesion) {
            jsonResponse(['resultado' => 'noupdate']);
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
            jsonResponse(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }
        
        $loginAdministrador = new LoginAdministrador();
        ob_start();
        $loginAdministrador->cambiar_contra($id_administrador, $oldpass, $newpass);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // Listar usuarios para el panel de administración
    public function listarUsuarios() {
        // Implementar según necesidad
        jsonResponse([]);
    }
}
?>
