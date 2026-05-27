<?php
<<<<<<< HEAD
// controlador/PacienteController.php - CORREGIDO
// Basado en el funcionamiento de AdministradorController.php

=======
<<<<<<< HEAD
// controlador/PacienteController.php - CORREGIDO
// Basado en el funcionamiento de AdministradorController.php

=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
class PacienteController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'paciente') {
            if ($this->isAjax()) {
<<<<<<< HEAD
                ApiResponse::unauthorized('No autorizado');
=======
<<<<<<< HEAD
                ApiResponse::unauthorized('No autorizado');
=======
                jsonResponse(['error' => 'No autorizado'], 401);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
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
    
<<<<<<< HEAD
    // ==================== BUSCAR PACIENTE (cargar datos para el perfil) ====================
=======
<<<<<<< HEAD
    // ==================== BUSCAR PACIENTE (cargar datos para el perfil) ====================
=======
    // Buscar paciente (cargar datos)
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function buscar() {
        $id_paciente = $_POST['dato'] ?? $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::buscar - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
=======
<<<<<<< HEAD
            ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
=======
            jsonResponse(['error' => 'No autorizado']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $paciente = new Paciente();
        $fecha_actual = new DateTime();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
<<<<<<< HEAD
            ApiResponse::notFound('Paciente');
=======
<<<<<<< HEAD
            ApiResponse::notFound('Paciente');
=======
            jsonResponse(['error' => 'No se encontró el paciente']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
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
<<<<<<< HEAD
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del paciente cargados correctamente');
    }
    
    // ==================== CAPTURAR DATOS PARA EDICIÓN ====================
=======
<<<<<<< HEAD
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del paciente cargados correctamente');
    }
    
    // ==================== CAPTURAR DATOS PARA EDICIÓN ====================
=======
        jsonResponse($json);
    }
    
    // Capturar datos para edición
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function capturarDatos() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::capturarDatos - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
            jsonResponse(['error' => 'No autorizado']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $paciente = new Paciente();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
<<<<<<< HEAD
            ApiResponse::notFound('Paciente');
=======
<<<<<<< HEAD
            ApiResponse::notFound('Paciente');
=======
            jsonResponse(['error' => 'No se encontró el paciente']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
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
<<<<<<< HEAD
        
        ApiResponse::success($json, 'datos_capturados', 'Datos cargados para edición');
    }
    
    // ==================== EDITAR PACIENTE ====================
   public function editarUsuario() {
    $id_paciente = $_POST['id_paciente'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
=======
<<<<<<< HEAD
        
        ApiResponse::success($json, 'datos_capturados', 'Datos cargados para edición');
    }
    
    // ==================== EDITAR PACIENTE ====================
   public function editarUsuario() {
    $id_paciente = $_POST['id_paciente'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
=======
        jsonResponse($json);
    }
    
    // Editar paciente
    public function editarUsuario() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        error_log("PacienteController::editarUsuario - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
            jsonResponse(['success' => false, 'error' => 'No autorizado']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
<<<<<<< HEAD
        $direccion = $_POST['direccion'] ?? ''; 
=======
<<<<<<< HEAD
        $direccion = $_POST['direccion'] ?? ''; 
=======
        $direccion = $_POST['direccion'] ?? '';
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $paciente = new Paciente();
        $paciente->editar($id_paciente, $telefono, $direccion, $correo, $sexo, $adicional);
        
<<<<<<< HEAD
        ApiResponse::updated([], 'Datos actualizados correctamente');
    }
    
    // ==================== CAMBIAR FOTO DE PERFIL ====================
=======
<<<<<<< HEAD
        ApiResponse::updated([], 'Datos actualizados correctamente');
    }
    
    // ==================== CAMBIAR FOTO DE PERFIL ====================
=======
        jsonResponse(['success' => true, 'message' => 'editado']);
    }
    
    // Cambiar foto de perfil
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function cambiarFoto() {
        $id_paciente = $_SESSION['usuario'];
        
        if (empty($id_paciente)) {
<<<<<<< HEAD
            ApiResponse::error('Sesión no válida', 'auth_error', [], 401);
=======
<<<<<<< HEAD
            ApiResponse::error('Sesión no válida', 'auth_error', [], 401);
=======
            jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
<<<<<<< HEAD
            ApiResponse::error('No se recibió el archivo', 'upload_error', [], 400);
=======
<<<<<<< HEAD
            ApiResponse::error('No se recibió el archivo', 'upload_error', [], 400);
=======
            jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
<<<<<<< HEAD
            ApiResponse::error('Tipo de archivo no permitido. Use JPG, PNG o GIF', 'invalid_type', [], 400);
=======
<<<<<<< HEAD
            ApiResponse::error('Tipo de archivo no permitido. Use JPG, PNG o GIF', 'invalid_type', [], 400);
=======
            jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nombre = uniqid() . '.' . $extension;
<<<<<<< HEAD
        $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
=======
<<<<<<< HEAD
        $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
=======
        $ruta_destino = '../img/' . $nombre;
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
            $paciente = new Paciente();
            $avatar_anterior = $paciente->cambiar_photo($id_paciente, $nombre);
            
<<<<<<< HEAD
            if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
                $ruta_anterior = dirname(__DIR__) . '/img/' . $avatar_anterior;
=======
<<<<<<< HEAD
            if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
                $ruta_anterior = dirname(__DIR__) . '/img/' . $avatar_anterior;
=======
            // Eliminar avatar anterior si no es el default
            if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
                $ruta_anterior = '../img/' . $avatar_anterior;
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                if (file_exists($ruta_anterior)) {
                    @unlink($ruta_anterior);
                }
            }
            
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            ApiResponse::success([
                'ruta' => APP_URL . '/img/' . $nombre,
                'alert' => 'edit'
            ], 'foto_actualizada', 'Foto de perfil actualizada correctamente');
<<<<<<< HEAD
=======
        } else {
            ApiResponse::error('Error al mover el archivo', 'upload_error', [], 500);
        }
    }
    
    // ==================== CAMBIAR CONTRASEÑA ====================
=======
            jsonResponse(['ruta' => APP_URL . '/img/' . $nombre, 'alert' => 'edit']);
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        } else {
            ApiResponse::error('Error al mover el archivo', 'upload_error', [], 500);
        }
    }
    
<<<<<<< HEAD
    // ==================== CAMBIAR CONTRASEÑA ====================
=======
    // Cambiar contraseña
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function cambiarPassword() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
            jsonResponse(['resultado' => 'noupdate']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
<<<<<<< HEAD
            ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'validation_error', [], 400);
=======
<<<<<<< HEAD
            ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'validation_error', [], 400);
=======
            jsonResponse(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $loginPaciente = new LoginPaciente();
        ob_start();
        $loginPaciente->cambiar_contra($id_paciente, $oldpass, $newpass);
<<<<<<< HEAD
        $resultado = trim(ob_get_clean());
=======
<<<<<<< HEAD
        $resultado = trim(ob_get_clean());
        
        if ($resultado === 'update') {
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', 'auth_error', [], 401);
        }
    }
    
    // ==================== MIS ESTADÍSTICAS ====================
=======
        $resultado = ob_get_clean();
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        if ($resultado === 'update') {
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', 'auth_error', [], 401);
        }
    }
    
<<<<<<< HEAD
    // ==================== MIS ESTADÍSTICAS ====================
=======
    // Mis estadísticas
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function misEstadisticas() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
<<<<<<< HEAD
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
=======
            jsonResponse(['error' => 'No autorizado']);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $paciente = new Paciente();
        $total_recetas = $paciente->contarRecetas($id_paciente);
        
<<<<<<< HEAD
        ApiResponse::success([
=======
<<<<<<< HEAD
        ApiResponse::success([
            'total_recetas' => $total_recetas,
            'proximas_citas' => 0
        ], 'estadisticas', 'Estadísticas cargadas correctamente');
    }
    
    // ==================== VISTA: MIS RECETAS ====================
=======
        jsonResponse([
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            'total_recetas' => $total_recetas,
            'proximas_citas' => 0
        ], 'estadisticas', 'Estadísticas cargadas correctamente');
    }
    
<<<<<<< HEAD
    // ==================== VISTA: MIS RECETAS ====================
   public function recetas() {
    AuthHelper::checkRole('paciente', true);
    
    $options = [
        'title' => 'Mis Recetas - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
            ['label' => 'Mis Recetas']
        ],
        'active_page' => 'recetas',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_paciente' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('paciente/pac_recetas', $data, $options);
}
=======
    // Vista: Mis recetas
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
    public function recetas() {
        renderView('paciente/pac_recetas');
    }
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
}
?>