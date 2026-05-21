<?php
// Desactivar errores en pantalla para producción
error_reporting(0);
ini_set('display_errors', 0);

include_once '../modelo/Asistente.php';
include_once '../modelo/Security.php';
session_start();

// ==================== VERIFICACIÓN CSRF ====================
function verificarCSRF() {
    // Solo verificar peticiones POST que modifican datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }
    
    // Obtener token de diferentes fuentes
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (empty($token)) {
        error_log("CSRF: Token no proporcionado para " . ($_POST['funcion'] ?? 'unknown'));
        return false;
    }
    
    if (!Security::verificarTokenCSRF($token)) {
        error_log("CSRF: Token inválido para " . ($_POST['funcion'] ?? 'unknown'));
        return false;
    }
    
    return true;
}
// ==================== FIN CSRF ====================

// Detectar si es AJAX
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if($is_ajax) {
    header('Content-Type: application/json');
}

$asistente = new Asistente();

// Verificar sesión
if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'asistente') {
    if($is_ajax) {
        echo json_encode(['error' => 'Sesión no válida o no autorizada']);
    } else {
        header('Location: ../login_asistente.php');
    }
    exit();
}

$id_sesion = $_SESSION['usuario'];

if(isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    
    // ✅ LISTA DE FUNCIONES QUE MODIFICAN DATOS (requieren CSRF)
    $funciones_que_modifican = [
        'editar_asistente',
        'cambiar_foto',
        'cambiar_contra'
    ];
    
    // Verificar CSRF para funciones que modifican datos
    if (in_array($funcion, $funciones_que_modifican)) {
        if (!verificarCSRF()) {
            if ($is_ajax) {
                echo json_encode(['success' => false, 'error' => 'Token CSRF inválido', 'message' => 'Error de seguridad: Token CSRF inválido']);
            } else {
                die('Error de seguridad: Token CSRF inválido');
            }
            exit();
        }
    }
    
    // Buscar asistente (solo lectura - no requiere CSRF)
    if ($funcion == 'buscar_asistente') {
        $dato = $_POST['dato'];
        if($dato != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para ver este perfil']);
            exit();
        }
        
        $json = array();
        $fecha_actual = new DateTime();
        $asistente->obtener_datos($dato);     
        if(empty($asistente->objetos)) {
            echo json_encode(['error' => 'No se encontró el asistente']);
            exit();
        }    
        foreach ($asistente->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_asistente;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            $edad_years = $edad->y;
            $json = array(
                'nombre' => $objeto->nombre_asistente,
                'apellidos' => $objeto->apellido_asistente,
                'fecha_nacimiento' => $edad_years,
                'cedula' => $objeto->cedula_asistente,
                'tipo' => $objeto->nombre_tipo_asistente,
                'telefono' => $objeto->telefono_asistente,
                'direccion' => $objeto->direccion_asistente,
                'correo' => $objeto->correo_asistente,
                'sexo' => $objeto->sexo_asistente,
                'adicional' => $objeto->adicional_asistente,
                'avatar' => '../../img/' . $objeto->avatar_asistente
            );
        }
        echo json_encode($json);
        exit();
    }
    
    // Capturar datos para editar (solo lectura - no requiere CSRF)
    if ($funcion == 'capturar_datos') {
        $id_asistente = $_POST['id_asistente'];
        if($id_asistente != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para editar este perfil']);
            exit();
        }
        
        $json = array();
        $asistente->obtener_datos($id_asistente);     
        if(empty($asistente->objetos)) {
            echo json_encode(['error' => 'No se encontró el asistente']);
            exit();
        }    
        foreach ($asistente->objetos as $objeto) {
            $json = array(            
                'telefono' => $objeto->telefono_asistente,
                'direccion' => $objeto->direccion_asistente,
                'correo' => $objeto->correo_asistente,
                'sexo' => $objeto->sexo_asistente,
                'adicional' => $objeto->adicional_asistente
            );
        }
        echo json_encode($json);
        exit();
    }
    
    // Editar asistente (REQUIERE CSRF)
    if ($funcion == 'editar_asistente') {
        $id_asistente = $_POST['id_asistente'];
        if($id_asistente != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para editar este perfil']);
            exit();
        }
        
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $sexo = $_POST['sexo'];
        $adicional = $_POST['adicional'];
        
        $asistente->editar($id_asistente, $telefono, $direccion, $correo, $sexo, $adicional);     
        echo json_encode(['success' => true, 'message' => 'editado']);
        exit();
    }
    
    // Cambiar foto (REQUIERE CSRF)
    if ($funcion == 'cambiar_foto') {
        $id_asistente = $id_sesion;
        
        if(empty($id_asistente)) {
            echo json_encode(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            exit();
        }
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['photo']['type'], $allowed_types)) {
                $nombre = uniqid() . '-' . $_FILES['photo']['name'];           
                $ruta = '../img/' . $nombre;
                move_uploaded_file($_FILES['photo']['tmp_name'], $ruta);
                $asistente->cambiar_photo($id_asistente, $nombre);
                foreach ($asistente->objetos as $objeto) {
                    if($objeto->avatar_asistente != 'avatarDES.jpg') {
                        @unlink('../img/' . $objeto->avatar_asistente);
                    }
                }
                $json = array(
                    'ruta' => $ruta,
                    'alert' => 'edit'
                );
                echo json_encode($json);
            } else {
                echo json_encode(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido. Use JPG, PNG o GIF']);
            }
        } else {
            echo json_encode(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
        }
        exit();
    }
    
    // Cambiar contraseña (REQUIERE CSRF)
    if ($funcion == 'cambiar_contra') {
        $id_asistente = $_POST['id_asistente'];
        if($id_asistente != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para cambiar esta contraseña']);
            exit();
        }
        
        $oldpass = $_POST['oldpass'];
        $newpass = $_POST['newpass'];
        
        if(strlen($newpass) < 6) {
            echo json_encode(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            exit();
        }
        
        ob_start();
        $asistente->cambiar_contra($id_asistente, $oldpass, $newpass);
        $resultado = ob_get_clean();
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
}

echo json_encode(['error' => 'Función no válida: ' . ($funcion ?? 'null')]);
?>