<?php
// Desactivar errores en pantalla para producción
error_reporting(0);
ini_set('display_errors', 0);

include_once '../modelo/Medico.php';
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

$medico = new Medico();

// Verificar sesión
if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'medico') {
    if($is_ajax) {
        echo json_encode(['error' => 'Sesión no válida o no autorizada']);
    } else {
        header('Location: ../login_medico.php');
    }
    exit();
}

$id_sesion = $_SESSION['usuario'];

if(isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    
    // ✅ LISTA DE FUNCIONES QUE MODIFICAN DATOS (requieren CSRF)
    $funciones_que_modifican = [
        'editar_medico',
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
    
    // Buscar médico (solo lectura - no requiere CSRF)
    if ($funcion == 'buscar_medico') {
        $dato = $_POST['dato'];
        // ✅ Solo puede buscar su propio perfil
        if($dato != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para ver este perfil']);
            exit();
        }
        
        $json = array();
        $fecha_actual = new DateTime();
        $medico->obtener_datos($dato);     
        if(empty($medico->objetos)) {
            echo json_encode(['error' => 'No se encontró el médico']);
            exit();
        }    
        foreach ($medico->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_medico;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            $edad_years = $edad->y;
            
            $avatar_path = (!empty($objeto->avatar_medico) && $objeto->avatar_medico != 'avatarDES.jpg') 
                           ? '../../img/' . $objeto->avatar_medico 
                           : '../../img/avatarDES.jpg';
            
            $json = array(
                'nombre' => $objeto->nombre_medico,
                'apellidos' => $objeto->apellido_medico,
                'fecha_nacimiento' => $edad_years,
                'cedula' => $objeto->cedula_medico,
                'tipo' => $objeto->nombre_tipo,
                'telefono' => $objeto->telefono_medico,
                'direccion' => $objeto->direccion_medico,
                'correo' => $objeto->correo_medico,
                'sexo' => $objeto->sexo_medico,
                'adicional' => $objeto->adicional_medico,
                'avatar' => $avatar_path
            );
        }
        echo json_encode($json);
        exit();
    }
    
    // Capturar datos para editar (solo lectura - no requiere CSRF)
    if ($funcion == 'capturar_datos') {
        $id_medico = $_POST['id_medico'];
        
        // ✅ Verificar que está editando su propio perfil
        if($id_medico != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para editar este perfil']);
            exit();
        }
        
        $json = array();
        $medico->obtener_datos($id_medico);     
        if(empty($medico->objetos)) {
            echo json_encode(['error' => 'No se encontró el médico']);
            exit();
        }    
        foreach ($medico->objetos as $objeto) {
            $json = array(            
                'telefono' => $objeto->telefono_medico,
                'direccion' => $objeto->direccion_medico,
                'correo' => $objeto->correo_medico,
                'sexo' => $objeto->sexo_medico,
                'adicional' => $objeto->adicional_medico
            );
        }
        echo json_encode($json);
        exit();
    }
    
    // Editar médico (REQUIERE CSRF)
    if ($funcion == 'editar_medico') {
        $id_medico = $_POST['id_medico'];
        
        // ✅ Verificar que está editando su propio perfil
        if($id_medico != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para editar este perfil']);
            exit();
        }
        
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $sexo = $_POST['sexo'];
        $adicional = $_POST['adicional'];
        
        $medico->editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional);     
        echo json_encode(['success' => true, 'message' => 'editado']);
        exit();
    }
    
    // Cambiar foto (REQUIERE CSRF)
    if ($funcion == 'cambiar_foto') {
        // ✅ Usar el ID de la sesión, ignorar el enviado por POST
        $id_medico = $id_sesion;
        
        if(empty($id_medico)) {
            echo json_encode(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            exit();
        }
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['photo']['type'], $allowed_types)) {
                $nombre = uniqid() . '-' . $_FILES['photo']['name'];           
                $ruta = '../img/' . $nombre;
                move_uploaded_file($_FILES['photo']['tmp_name'], $ruta);
                $medico->cambiar_photo($id_medico, $nombre);
                foreach ($medico->objetos as $objeto) {
                    if($objeto->avatar_medico != 'avatarDES.jpg') {
                        @unlink('../img/' . $objeto->avatar_medico);
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
        $id_medico = $_POST['id_medico'];
        
        // ✅ Verificar que está cambiando su propia contraseña
        if($id_medico != $id_sesion) {
            echo json_encode(['error' => 'No autorizado para cambiar esta contraseña']);
            exit();
        }
        
        $oldpass = $_POST['oldpass'];
        $newpass = $_POST['newpass'];
        
        // Validar que la nueva contraseña tenga al menos 6 caracteres
        if(strlen($newpass) < 6) {
            echo json_encode(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            exit();
        }
        
        ob_start();
        $medico->cambiar_contra($id_medico, $oldpass, $newpass);
        $resultado = ob_get_clean();
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Mis estadísticas (solo lectura - no requiere CSRF)
    if ($funcion == 'mis_estadisticas') {
        $id_medico = $_POST['id_medico'];
        
        // ✅ Verificar que está viendo sus propias estadísticas
        if($id_medico != $id_sesion) {
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        $total_recetas = $medico->contarRecetas($id_medico);
        $total_pacientes = $medico->contarPacientes($id_medico);
        echo json_encode([
            'total_recetas' => $total_recetas,
            'total_pacientes' => $total_pacientes
        ]);
        exit();
    }
    
    // Listar pacientes del médico (solo lectura - no requiere CSRF)
    if ($funcion == 'listar_pacientes') {
        $id_medico = $_POST['id_medico'];
        
        // ✅ Verificar que está viendo sus propios pacientes
        if($id_medico != $id_sesion) {
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        $pacientes = $medico->listarPacientes($id_medico);
        echo json_encode($pacientes);
        exit();
    }
}

echo json_encode(['error' => 'Función no válida: ' . ($funcion ?? 'null')]);
?>