<?php
session_start();

// Incluir todos los modelos de login
include_once '../modelo/LoginPaciente.php';
include_once '../modelo/LoginMedico.php';
include_once '../modelo/LoginAsistente.php';
include_once '../modelo/LoginAdministrador.php';

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

// Verificar si es una petición AJAX
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$user = $_POST['user'] ?? '';
$pass = $_POST['pass'] ?? '';
$rol = $_POST['rol'] ?? ''; // Recibir el rol desde el formulario

// Si ya hay sesión, redirigir según el rol
if(!empty($_SESSION['us_tipo']) && !empty($_SESSION['rol'])) {
    $redirect = '';
    switch ($_SESSION['rol']) {
        case 'paciente':
            $redirect = '../vista/paciente/pac_catalogo.php';
            break;
        case 'medico':
            $redirect = '../vista/medico/med_catalogo.php';
            break;
        case 'asistente':
            $redirect = '../vista/asistente/asi_catalogo.php';
            break;
        case 'administrador':
            $redirect = '../vista/administrador/adm_catalogo.php';
            break;
    }
    
    if($is_ajax) {
        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        header('Location: ' . $redirect);
    }
    exit();
}

// Procesar login según el rol
$login_exitoso = false;
$objeto_usuario = null;
$rol_usuario = '';

if($rol == 'paciente') {
    $login = new LoginPaciente();
    $login->Loguearse($user, $pass);
    if(!empty($login->objetos)) {
        foreach($login->objetos as $objeto) {
            $_SESSION['usuario'] = $objeto->id_paciente;
            $_SESSION['us_tipo'] = $objeto->paciente_tipo;
            $_SESSION['nombre_us'] = $objeto->nombre_paciente;
            $_SESSION['rol'] = 'paciente';
            $login->actualizarUltimoAcceso($objeto->id_paciente);
        }
        $login_exitoso = true;
        $rol_usuario = 'paciente';
    }
}
elseif($rol == 'medico') {
    $login = new LoginMedico();
    $login->Loguearse($user, $pass);
    if(!empty($login->objetos)) {
        foreach($login->objetos as $objeto) {
            $_SESSION['usuario'] = $objeto->id_medico;  // ← Esto debe ser el ID correcto
            $_SESSION['us_tipo'] = $objeto->medico_tipo;
            $_SESSION['nombre_us'] = $objeto->nombre_medico;
            $_SESSION['rol'] = 'medico';
            $login->actualizarUltimoAcceso($objeto->id_medico);
            
            // DEBUG: Verificar que se guardó
            error_log("Médico logueado - ID: " . $objeto->id_medico . ", Nombre: " . $objeto->nombre_medico);
        }
        $login_exitoso = true;
        $rol_usuario = 'medico';
    }
}
elseif($rol == 'asistente') {
    $login = new LoginAsistente();
    $login->Loguearse($user, $pass);
    if(!empty($login->objetos)) {
        foreach($login->objetos as $objeto) {
            $_SESSION['usuario'] = $objeto->id_asistente;
            $_SESSION['us_tipo'] = $objeto->asistente_tipo;
            $_SESSION['nombre_us'] = $objeto->nombre_asistente;
            $_SESSION['rol'] = 'asistente';
            $login->actualizarUltimoAcceso($objeto->id_asistente);
        }
        $login_exitoso = true;
        $rol_usuario = 'asistente';
    }
}
elseif($rol == 'administrador') {
    $login = new LoginAdministrador();
    $login->Loguearse($user, $pass);
    if(!empty($login->objetos)) {
        foreach($login->objetos as $objeto) {
            $_SESSION['usuario'] = $objeto->id_administrador;
            $_SESSION['us_tipo'] = $objeto->administrador_tipo;
            $_SESSION['nombre_us'] = $objeto->nombre_administrador;
            $_SESSION['rol'] = 'administrador';
            $login->actualizarUltimoAcceso($objeto->id_administrador);
        }
        $login_exitoso = true;
        $rol_usuario = 'administrador';
    }
}

if($login_exitoso) {
    $redirect = '';
    switch ($rol_usuario) {
        case 'paciente':
            $redirect = '../vista/paciente/pac_catalogo.php';
            break;
        case 'medico':
            $redirect = '../vista/medico/med_catalogo.php';
            break;
        case 'asistente':
            $redirect = '../vista/asistente/asi_catalogo.php';
            break;
        case 'administrador':
            $redirect = '../vista/administrador/adm_catalogo.php';
            break;
    }
    
    if($is_ajax) {
        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        header('Location: ' . $redirect);
    }
} else {
    if($is_ajax) {
        echo json_encode(['success' => false, 'error' => 'Cédula o contraseña incorrecta']);
    } else {
        header('Location: ../index.php?error=1');
    }
}
?>