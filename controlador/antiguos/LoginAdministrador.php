<?php
include_once '../modelo/LoginAdministrador.php';
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


$login = new LoginAdministrador();
$funcion = $_POST['funcion'] ?? '';

if ($funcion == 'cambiar_contra') {
    $id_administrador = $_POST['id_administrador'];
    $oldpass = $_POST['oldpass'];
    $newpass = $_POST['newpass'];
    
    ob_start();
    $login->cambiar_contra($id_administrador, $oldpass, $newpass);
    $resultado = ob_get_clean();
    echo json_encode(['resultado' => trim($resultado)]);
    exit();
}
?>
