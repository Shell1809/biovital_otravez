<?php
// Configurar header para JSON
header('Content-Type: application/json');

// Incluir el modelo
include_once '../modelo/Receta.php';
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


// Verificar sesión
if(!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit();
}

$receta = new Receta();
$funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

// ✅ Verificar permisos según el rol
$id_usuario = $_SESSION['usuario'];
$rol_usuario = $_SESSION['rol'] ?? '';

if ($funcion == 'listar_recetas') {
    // ✅ Médico solo ve sus recetas, Asistente ve todas (según necesidad)
    if ($rol_usuario == 'medico') {
        $resultados = $receta->obtener_recetas($id_usuario);
    } else {
        $resultados = $receta->obtener_recetas(null);
    }
    $json = array();
    
    if(!empty($resultados)) {
        foreach ($resultados as $objeto) {
            $json[] = array(
                'id_receta' => $objeto->id_receta,
                'nombre_medicamento' => $objeto->nombre_medicamento,
                'marca' => $objeto->marca,
                'cantidad' => $objeto->cantidad,
                'dosis' => isset($objeto->dosis) ? $objeto->dosis : '',
                'instrucciones' => isset($objeto->instrucciones) ? $objeto->instrucciones : '',
                'paciente' => isset($objeto->nombre_paciente) ? $objeto->nombre_paciente : 'N/A',
                'medico' => isset($objeto->nombre_medico) ? $objeto->nombre_medico : 'N/A',
                'fecha_receta' => $objeto->fecha_receta
            );
        }
    }
    echo json_encode($json);
    exit();
}

if ($funcion == 'crear_receta') {
    // ✅ Solo médico puede crear recetas
    if ($rol_usuario != 'medico') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }
    
    $nombre_medicamento = isset($_POST['nombre_medicamento']) ? $_POST['nombre_medicamento'] : '';
    $marca = isset($_POST['marca']) ? $_POST['marca'] : '';
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : '';
    $dosis = isset($_POST['dosis']) ? $_POST['dosis'] : '';
    $instrucciones = isset($_POST['instrucciones']) ? $_POST['instrucciones'] : '';
    $id_paciente = isset($_POST['id_paciente']) ? $_POST['id_paciente'] : '';
    $id_medico = $_SESSION['usuario'];
    $fecha_receta = isset($_POST['fecha_receta']) ? $_POST['fecha_receta'] : '';
    
    // Validaciones...
    if(empty($nombre_medicamento) || empty($marca) || empty($cantidad) || empty($id_paciente) || empty($fecha_receta)) {
        echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos requeridos']);
        exit();
    }
    
    ob_start();
    $receta->crear_receta($nombre_medicamento, $marca, $cantidad, $dosis, $instrucciones, $id_paciente, $id_medico, $fecha_receta);
    $output = ob_get_clean();
    
    if(trim($output) == 'creado') {
        echo json_encode(['success' => true, 'message' => 'Receta creada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear la receta: ' . $output]);
    }
    exit();
}

if ($funcion == 'editar_receta') {
    // ✅ Solo médico puede editar, y solo sus recetas
    if ($rol_usuario != 'medico') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }
    
    $id_receta = isset($_POST['id_receta']) ? $_POST['id_receta'] : '';
    
    // ✅ Verificar que la receta pertenezca al médico
    $receta_verificar = $receta->obtener_receta($id_receta);
    if(empty($receta_verificar) || $receta_verificar[0]->id_medico != $id_usuario) {
        echo json_encode(['success' => false, 'message' => 'No autorizado para editar esta receta']);
        exit();
    }
    
    $nombre_medicamento = isset($_POST['nombre_medicamento']) ? $_POST['nombre_medicamento'] : '';
    $marca = isset($_POST['marca']) ? $_POST['marca'] : '';
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : '';
    $dosis = isset($_POST['dosis']) ? $_POST['dosis'] : '';
    $instrucciones = isset($_POST['instrucciones']) ? $_POST['instrucciones'] : '';
    $id_paciente = isset($_POST['id_paciente']) ? $_POST['id_paciente'] : '';
    $fecha_receta = isset($_POST['fecha_receta']) ? $_POST['fecha_receta'] : '';
    
    if(empty($nombre_medicamento) || empty($marca) || empty($cantidad) || empty($id_paciente) || empty($fecha_receta)) {
        echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos requeridos']);
        exit();
    }
    
    ob_start();
    $receta->editar_receta($id_receta, $nombre_medicamento, $marca, $cantidad, $dosis, $instrucciones, $id_paciente, $fecha_receta);
    $output = ob_get_clean();
    
    if(trim($output) == 'editado') {
        echo json_encode(['success' => true, 'message' => 'Receta actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la receta: ' . $output]);
    }
    exit();
}

if ($funcion == 'borrar_receta') {
    // ✅ Solo médico puede borrar, y solo sus recetas
    if ($rol_usuario != 'medico') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }
    
    $id_receta = isset($_POST['id_receta']) ? $_POST['id_receta'] : '';
    
    // ✅ Verificar que la receta pertenezca al médico
    $receta_verificar = $receta->obtener_receta($id_receta);
    if(empty($receta_verificar) || $receta_verificar[0]->id_medico != $id_usuario) {
        echo json_encode(['success' => false, 'message' => 'No autorizado para eliminar esta receta']);
        exit();
    }
    
    if(!empty($id_receta)) {
        ob_start();
        $receta->borrar_receta($id_receta);
        $output = ob_get_clean();
        
        if(trim($output) == 'borrado') {
            echo json_encode(['success' => true, 'message' => 'Receta borrada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al borrar la receta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de receta no válido']);
    }
    exit();
}

if ($funcion == 'obtener_receta') {
    $id_receta = isset($_POST['id_receta']) ? $_POST['id_receta'] : '';
    
    $resultados = $receta->obtener_receta($id_receta);
    $json = array();
    
    if(!empty($resultados)) {
        // ✅ Si es médico, verificar que sea su receta
        if ($rol_usuario == 'medico' && $resultados[0]->id_medico != $id_usuario) {
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        // ✅ Si es paciente, verificar que sea su receta
        if ($rol_usuario == 'paciente' && $resultados[0]->id_paciente != $id_usuario) {
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        foreach ($resultados as $objeto) {
            $json = array(
                'id_receta' => $objeto->id_receta,
                'nombre_medicamento' => $objeto->nombre_medicamento,
                'marca' => $objeto->marca,
                'cantidad' => $objeto->cantidad,
                'dosis' => isset($objeto->dosis) ? $objeto->dosis : '',
                'instrucciones' => isset($objeto->instrucciones) ? $objeto->instrucciones : '',
                'id_paciente' => $objeto->id_paciente,
                'fecha_receta' => $objeto->fecha_receta
            );
        }
    }
    echo json_encode($json);
    exit();
}

if ($funcion == 'buscar_pacientes') {
    // ✅ Todos pueden buscar pacientes? Depende de la necesidad
    $dato = isset($_POST['dato']) ? $_POST['dato'] : '';
    $resultados = $receta->buscar_pacientes($dato);
    $json = array();
    
    if(!empty($resultados)) {
        foreach ($resultados as $objeto) {
            $json[] = array(
                'id_usuario' => $objeto->id_usuario,
                'nombre_completo' => $objeto->nombre_us . ' ' . $objeto->apellidos_us,
                'cedula' => $objeto->cedula_us,
                'fecha_nacimiento' => $objeto->edad,  
                'sexo' => $objeto->sexo_us  
            );
        }
    }
    echo json_encode($json);
    exit();
}

if ($funcion == 'mis_recetas') {
    $id_paciente = isset($_POST['id_paciente']) ? $_POST['id_paciente'] : '';
    
    // ✅ Verificar que el paciente vea sus propias recetas
    if ($rol_usuario == 'paciente' && $id_paciente != $id_usuario) {
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    
    $resultados = $receta->obtenerRecetasPorPaciente($id_paciente);
    $json = array();
    
    if(!empty($resultados)) {
        foreach ($resultados as $objeto) {
            $json[] = array(
                'id_receta' => $objeto->id_receta,
                'nombre_medicamento' => $objeto->nombre_medicamento,
                'marca' => $objeto->marca,
                'cantidad' => $objeto->cantidad,
                'dosis' => isset($objeto->dosis) ? $objeto->dosis : '',
                'instrucciones' => isset($objeto->instrucciones) ? $objeto->instrucciones : '',
                'medico' => isset($objeto->nombre_medico) ? $objeto->nombre_medico : 'N/A',
                'fecha_receta' => $objeto->fecha_receta
            );
        }
    }
    echo json_encode($json);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Función no válida: ' . $funcion]);
?>