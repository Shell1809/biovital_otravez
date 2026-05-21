<?php
header('Content-Type: application/json');
include_once '../modelo/Asistente.php';
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


$asistente = new Asistente();
$funcion = $_POST['funcion'] ?? '';

if ($funcion == 'crear_asistente') {
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
    $tipo = 3; // Tipo 3 = Asistente
    $avatar = 'avatarDES.jpg';
    
    $errores = [];
    if(empty($nombre)) $errores[] = 'Nombre requerido';
    if(empty($apellidos)) $errores[] = 'Apellidos requeridos';
    if(empty($cedula)) $errores[] = 'Cédula requerida';
    if(empty($pass)) $errores[] = 'Contraseña requerida';
    if(strlen($pass) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres';
    
    if(!empty($errores)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errores)]);
        exit();
    }
    
    // 🔐 ENCRIPTAR CONTRASEÑA
    $password_hash = password_hash($pass, PASSWORD_DEFAULT);
    
    ob_start();
    $asistente->crear($nombre, $apellidos, $fecha_nacimiento, $cedula, $telefono, $direccion, $correo, $sexo, $adicional, $password_hash, $tipo, $avatar);
    $resultado = ob_get_clean();
    $resultado = trim($resultado);
    
    if($resultado == 'add') {
        echo json_encode(['success' => true, 'message' => 'Cuenta de asistente creada exitosamente']);
    } else if($resultado == 'existe') {
        echo json_encode(['success' => false, 'message' => 'Ya existe un asistente con esta cédula o correo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear la cuenta: ' . $resultado]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Función no válida']);
?>