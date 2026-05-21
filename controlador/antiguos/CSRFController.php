<?php
session_start();
header('Content-Type: application/json');

// Verificar si es AJAX
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if(!$is_ajax) {
    echo json_encode(['error' => 'Acceso no permitido']);
    exit();
}

// Función para generar token CSRF
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiry'] = time() + 3600; // 1 hora
    }
    
    // Si el token está por expirar (menos de 10 minutos), renovar
    if (time() > $_SESSION['csrf_token_expiry'] - 600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiry'] = time() + 3600;
    }
    
    return [
        'token' => $_SESSION['csrf_token'],
        'expiry' => $_SESSION['csrf_token_expiry'] * 1000 // Convertir a milisegundos para JS
    ];
}

// Función para verificar token CSRF
function verificarTokenCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        return false;
    }
    
    // Verificar si el token ha expirado
    if (time() > $_SESSION['csrf_token_expiry']) {
        return false;
    }
    
    // Verificar que el token coincida
    return hash_equals($_SESSION['csrf_token'], $token);
}

$funcion = $_POST['funcion'] ?? '';

if ($funcion == 'get_token') {
    $tokenData = generarTokenCSRF();
    echo json_encode($tokenData);
    exit();
}

if ($funcion == 'verify_token') {
    $token = $_POST['csrf_token'] ?? '';
    $esValido = verificarTokenCSRF($token);
    echo json_encode(['valid' => $esValido]);
    exit();
}

echo json_encode(['error' => 'Función no válida']);
?>
