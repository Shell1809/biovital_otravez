<?php
// Desactivar errores en pantalla para producción
error_reporting(0);
ini_set('display_errors', 0);

include_once '../modelo/Administrador.php';
session_start();
// ==================== FUNCIÓN DE VERIFICACIÓN CSRF ====================
function verificarCSRF() {
    // Solo verificar peticiones POST (no GET)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }
    
    // Obtener token de diferentes fuentes posibles
    $token = '';
    if (isset($_POST['csrf_token'])) {
        $token = $_POST['csrf_token'];
    } elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    
    if (empty($token)) {
        error_log("CSRF: Token no proporcionado");
        return false;
    }
    
    // Verificar sesión
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        error_log("CSRF: Token de sesión no encontrado");
        return false;
    }
    
    // Verificar expiración
    if (time() > $_SESSION['csrf_token_expiry']) {
        error_log("CSRF: Token expirado");
        return false;
    }
    
    // Verificar coincidencia
    $esValido = hash_equals($_SESSION['csrf_token'], $token);
    if (!$esValido) {
        error_log("CSRF: Token inválido - Sesión: " . substr($_SESSION['csrf_token'], 0, 10) . 
                  " vs Recibido: " . substr($token, 0, 10));
    }
    
    return $esValido;
}

// Detectar si es AJAX
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if($is_ajax) {
    header('Content-Type: application/json');
}

$administrador = new Administrador();

// Verificar que el usuario está autenticado
if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    if($is_ajax) {
        echo json_encode(['error' => 'Sesión no válida o no autorizada']);
    } else {
        header('Location: ../login_administrador.php');
    }
    exit();
}

$id_sesion = $_SESSION['usuario']; // ID del administrador logueado

if(isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    
    // Buscar administrador
    if ($funcion == 'buscar_administrador') {
        $dato = $_POST['dato'];
        // ✅ Solo puede buscar su propio perfil
        if($dato != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para ver este perfil']);
            exit();
        }
        
        $json = array();
        $fecha_actual = new DateTime();
        $administrador->obtener_datos($dato);     
        if(empty($administrador->objetos)) {
            echo json_encode(['error' => 'No se encontró el administrador']);
            exit();
        }    
        foreach ($administrador->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_administrador;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            $edad_years = $edad->y;
            $json = array(
                'nombre' => $objeto->nombre_administrador,
                'apellidos' => $objeto->apellido_administrador,
                'fecha_nacimiento' => $edad_years,
                'cedula' => $objeto->cedula_administrador,
                'tipo' => $objeto->nombre_tipo_administrador,
                'telefono' => $objeto->telefono_administrador,
                'direccion' => $objeto->direccion_administrador,
                'correo' => $objeto->correo_administrador,
                'sexo' => $objeto->sexo_administrador,
                'adicional' => $objeto->adicional_administrador,
                'avatar' => '../../img/' . $objeto->avatar_administrador
            );
        }
        echo json_encode($json);
        exit();
    }
    
    // Capturar datos para editar
    if ($funcion == 'capturar_datos') {
        $id_administrador = $_POST['id_administrador'];
        
        // ✅ Verificar que está editando su propio perfil
        if($id_administrador != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para editar este perfil']);
            exit();
        }
        
        $json = array();
        $administrador->obtener_datos($id_administrador);     
        if(empty($administrador->objetos)) {
            echo json_encode(['error' => 'No se encontró el administrador']);
            exit();
        }    
        foreach ($administrador->objetos as $objeto) {
            $json = array(            
                'telefono' => $objeto->telefono_administrador,
                'direccion' => $objeto->direccion_administrador,
                'correo' => $objeto->correo_administrador,
                'sexo' => $objeto->sexo_administrador,
                'adicional' => $objeto->adicional_administrador
            );
        }
        echo json_encode($json);
        exit();
    }
    
    // Editar administrador
    if ($funcion == 'editar_administrador') {
        $id_administrador = $_POST['id_administrador'];
        
        // ✅ Verificar que está editando su propio perfil
        if($id_administrador != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para editar este perfil']);
            exit();
        }
        
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $sexo = $_POST['sexo'];
        $adicional = $_POST['adicional'];
        
        // ✅ Validar que los datos no estén vacíos (opcional)
        if(empty($telefono) && empty($direccion) && empty($correo)) {
            echo json_encode(['error' => 'Debe completar al menos un campo']);
            exit();
        }
        
        $administrador->editar($id_administrador, $telefono, $direccion, $correo, $sexo, $adicional);     
        echo json_encode(['success' => true, 'message' => 'editado']);
        exit();
    }
    
    // Cambiar foto
    if ($funcion == 'cambiar_foto') {
        // ✅ Usar el ID de la sesión, ignorar el enviado por POST
        $id_administrador = $id_sesion;
        
        if(empty($id_administrador)) {
            echo json_encode(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            exit();
        }
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['photo']['type'], $allowed_types)) {
                $nombre = uniqid() . '-' . $_FILES['photo']['name'];           
                $ruta = '../img/' . $nombre;
                move_uploaded_file($_FILES['photo']['tmp_name'], $ruta);
                $administrador->cambiar_photo($id_administrador, $nombre);
                foreach ($administrador->objetos as $objeto) {
                    if($objeto->avatar_administrador != 'avatarDES.jpg') {
                        @unlink('../img/' . $objeto->avatar_administrador);
                    }
                }
                $json = array(
                    'ruta' => $ruta,
                    'alert' => 'edit'
                );
                echo json_encode($json);
            } else {
                echo json_encode(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido']);
            }
        } else {
            echo json_encode(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
        }
        exit();
    }
}

echo json_encode(['error' => 'Función no válida']);
?>