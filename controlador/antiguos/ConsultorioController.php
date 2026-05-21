<?php
error_reporting(0);
ini_set('display_errors', 0);

include_once '../modelo/Consultorio.php';
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

// ==================== VERIFICACIÓN DE AUTENTICACIÓN ====================
// Las funciones de ubicación NO requieren autenticación
$funciones_publicas = [
    'listar_estados',
    'listar_ciudades',
    'listar_municipios',
    'listar_parroquias',
    'lista_especialidades'
];

$funcion = $_POST['funcion'] ?? '';

// Verificar si la función es pública (no requiere autenticación)
$es_publica = in_array($funcion, $funciones_publicas);

if (!$es_publica) {
    // Solo verificar autenticación para funciones que NO son públicas
    if(!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
        $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        if($is_ajax) {
            echo json_encode(['error' => 'Sesión no iniciada']);
        } else {
            header('Location: ../login_administrador.php');
        }
        exit();
    }
}

// Determinar si el usuario es administrador (solo relevante para funciones protegidas)
$es_administrador = ($_SESSION['rol'] ?? '') == 'administrador';
$rol_usuario = $_SESSION['rol'] ?? '';

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if($is_ajax) {
    header('Content-Type: application/json');
}

$consultorio = new Consultorio();

if(isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    
    // ✅ LISTA DE FUNCIONES QUE MODIFICAN DATOS (requieren CSRF)
    $funciones_que_modifican = [
        'crear_consultorio', 
        'editar_consultorio', 
        'eliminar_consultorio',
        'asignar_medico',
        'remover_medico',
        'guardar_horario'
    ];
    
    // Verificar CSRF para funciones que modifican datos
    if (in_array($funcion, $funciones_que_modifican)) {
        if (!verificarCSRF()) {
            if ($is_ajax) {
                echo json_encode(['resultado' => 'error_csrf', 'mensaje' => 'Token CSRF inválido']);
            } else {
                die('Error de seguridad: Token CSRF inválido');
            }
            exit();
        }
    }
    
    // ==================== FUNCIONES PÚBLICAS (No requieren autenticación) ====================
    
    // Listar estados - PÚBLICO (no requiere sesión)
    if ($funcion == 'listar_estados') {
        $estados = $consultorio->listarEstados();
        echo json_encode($estados);
        exit();
    }
    
    // Listar ciudades por estado - PÚBLICO
    if ($funcion == 'listar_ciudades') {
        $id_estado = $_POST['id_estado'];
        $ciudades = $consultorio->listarCiudades($id_estado);
        echo json_encode($ciudades);
        exit();
    }
    
    // Listar municipios por estado - PÚBLICO
    if ($funcion == 'listar_municipios') {
        $id_estado = $_POST['id_estado'];
        $municipios = $consultorio->listarMunicipios($id_estado);
        echo json_encode($municipios);
        exit();
    }
    
    // Listar parroquias por municipio - PÚBLICO
    if ($funcion == 'listar_parroquias') {
        $id_municipio = $_POST['id_municipio'];
        $parroquias = $consultorio->listarParroquias($id_municipio);
        echo json_encode($parroquias);
        exit();
    }
    
    // Lista de especialidades predefinidas - PÚBLICO
    if ($funcion == 'lista_especialidades') {
        $especialidades = $consultorio->obtenerListaEspecialidades();
        echo json_encode($especialidades);
        exit();
    }
    
    // ==================== FUNCIONES PROTEGIDAS (Requieren autenticación) ====================
    
    // Listar consultorios (requiere autenticación)
    if ($funcion == 'listar_consultorios') {
        $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
        $resultados = $consultorio->listar($busqueda);
        $json = array();
        
        if(!empty($resultados)) {
            foreach ($resultados as $objeto) {
                $json[] = array(
                    'id_consultorio' => $objeto->id_consultorio,
                    'nombre' => $objeto->nombre,
                    'ciudad' => $objeto->ciudad,
                    'direccion' => $objeto->direccion_detallada,
                    'telefono' => $objeto->telefono,
                    'email' => $objeto->email,
                    'total_medicos' => $objeto->total_medicos ?? 0,
                    'apertura' => $objeto->apertura_habitual,
                    'cierre' => $objeto->cierre_habitual
                );
            }
        }
        echo json_encode($json);
        exit();
    }
    
    // Crear consultorio - Requiere administrador
    if ($funcion == 'crear_consultorio') {
        if(!$es_administrador) {
            echo json_encode(['resultado' => 'no_autorizado', 'mensaje' => 'No tiene permisos para realizar esta acción']);
            exit();
        }
        
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $apertura = $_POST['apertura'];
        $cierre = $_POST['cierre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $id_estado = $_POST['id_estado'];
        $id_ciudad = $_POST['id_ciudad'];
        $id_municipio = $_POST['id_municipio'] ?? null;
        $id_parroquia = $_POST['id_parroquia'] ?? null;
        $direccion = $_POST['direccion'];
        $especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : array();
        
        ob_start();
        $consultorio->crear($nombre, $descripcion, $apertura, $cierre, $telefono, $email, 
                            $id_estado, $id_ciudad, $id_municipio, $id_parroquia, $direccion, $especialidades);
        $resultado = ob_get_clean();
        
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Editar consultorio - Requiere administrador
    if ($funcion == 'editar_consultorio') {
        if(!$es_administrador) {
            echo json_encode(['resultado' => 'no_autorizado', 'mensaje' => 'No tiene permisos para realizar esta acción']);
            exit();
        }
        
        $id_consultorio = $_POST['id_consultorio'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $apertura = $_POST['apertura'];
        $cierre = $_POST['cierre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $estado = $_POST['estado'];
        $ciudad = $_POST['ciudad'];
        $municipio = $_POST['municipio'] ?? '';
        $parroquia = $_POST['parroquia'] ?? '';
        $direccion = $_POST['direccion'];
        $especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : array();
        
        ob_start();
        $consultorio->editar($id_consultorio, $nombre, $descripcion, $apertura, $cierre, $telefono, $email, 
                            $estado, $ciudad, $municipio, $parroquia, $direccion, $especialidades);
        $resultado = ob_get_clean();
        
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Eliminar consultorio - Requiere administrador
    if ($funcion == 'eliminar_consultorio') {
        if(!$es_administrador) {
            echo json_encode(['resultado' => 'no_autorizado', 'mensaje' => 'No tiene permisos para realizar esta acción']);
            exit();
        }
        
        $id_consultorio = $_POST['id_consultorio'];
        ob_start();
        $consultorio->eliminar($id_consultorio);
        $resultado = ob_get_clean();
        
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Asignar médico - Requiere administrador
    if ($funcion == 'asignar_medico') {
        if(!$es_administrador) {
            echo json_encode(['resultado' => 'no_autorizado', 'mensaje' => 'No tiene permisos para realizar esta acción']);
            exit();
        }
        
        $id_consultorio = $_POST['id_consultorio'];
        $id_medico = $_POST['id_medico'];
        ob_start();
        $consultorio->asignarMedico($id_consultorio, $id_medico);
        $resultado = ob_get_clean();
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Remover médico - Requiere administrador
    if ($funcion == 'remover_medico') {
        if(!$es_administrador) {
            echo json_encode(['resultado' => 'no_autorizado', 'mensaje' => 'No tiene permisos para realizar esta acción']);
            exit();
        }
        
        $id_asignacion = $_POST['id_asignacion'];
        ob_start();
        $consultorio->removerMedico($id_asignacion);
        $resultado = ob_get_clean();
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Guardar horario - Requiere administrador
    if ($funcion == 'guardar_horario') {
        if(!$es_administrador) {
            echo json_encode(['resultado' => 'no_autorizado', 'mensaje' => 'No tiene permisos para realizar esta acción']);
            exit();
        }
        
        $id_consultorio = $_POST['id_consultorio'];
        $dia = $_POST['dia'];
        $turno = $_POST['turno'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $id_medico = !empty($_POST['id_medico']) ? $_POST['id_medico'] : null;
        
        if ($hora_inicio >= $hora_fin) {
            echo json_encode(['resultado' => 'error_horario', 'mensaje' => 'La hora de fin debe ser mayor que la hora de inicio']);
            exit();
        }
        
        ob_start();
        $consultorio->guardarHorario($id_consultorio, $dia, $turno, $hora_inicio, $hora_fin, $id_medico);
        $resultado = ob_get_clean();
        
        echo json_encode(['resultado' => trim($resultado)]);
        exit();
    }
    
    // Listar médicos disponibles - Requiere autenticación
    if ($funcion == 'listar_medicos_disponibles') {
        $medicos = $consultorio->listarMedicos();
        echo json_encode($medicos);
        exit();
    }
    
    // Obtener estadísticas - Requiere autenticación
    if ($funcion == 'obtener_estadisticas') {
        $total_consultorios = $consultorio->totalActivos();
        echo json_encode([
            'total_consultorios' => $total_consultorios,
            'activos' => $total_consultorios
        ]);
        exit();
    }
    
    // Obtener detalle del consultorio - Requiere autenticación
    if ($funcion == 'obtener_detalle') {
        $id_consultorio = $_POST['id_consultorio'];
        $consultorio->obtener($id_consultorio);
        $detalle = array();
        
        if(!empty($consultorio->objetos)) {
            foreach ($consultorio->objetos as $objeto) {
                $detalle = array(
                    'id_consultorio' => $objeto->id_consultorio,
                    'nombre' => $objeto->nombre,
                    'descripcion' => $objeto->descripcion,
                    'apertura' => $objeto->apertura_habitual,
                    'cierre' => $objeto->cierre_habitual,
                    'telefono' => $objeto->telefono,
                    'email' => $objeto->email,
                    'estado' => $objeto->estado,
                    'ciudad' => $objeto->ciudad,
                    'municipio' => $objeto->municipio,
                    'parroquia' => $objeto->parroquia,
                    'direccion' => $objeto->direccion_detallada
                );
            }
        }
        
        $especialidades = $consultorio->obtenerEspecialidades($id_consultorio);
        $lista_especialidades = array();
        foreach($especialidades as $esp) {
            $lista_especialidades[] = $esp->especialidad;
        }
        $detalle['especialidades'] = $lista_especialidades;
        
        $medicos = $consultorio->obtenerMedicos($id_consultorio);
        $lista_medicos = array();
        foreach($medicos as $med) {
            $lista_medicos[] = array(
                'id' => $med->id,
                'id_medico' => $med->id_medico,
                'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico,
                'cedula' => $med->cedula_medico,
                'telefono' => $med->telefono_medico
            );
        }
        $detalle['medicos'] = $lista_medicos;
        
        echo json_encode($detalle);
        exit();
    }
    
    // Obtener horarios del consultorio - Requiere autenticación
    if ($funcion == 'obtener_horarios') {
        $id_consultorio = $_POST['id_consultorio'];
        $horarios = $consultorio->obtenerHorarios($id_consultorio);
        
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $turnos = ['Mañana', 'Tarde'];
        
        $horarios_organizados = array();
        foreach($dias as $dia) {
            $horarios_organizados[$dia] = array('Mañana' => null, 'Tarde' => null);
            foreach($turnos as $turno) {
                foreach($horarios as $hor) {
                    if($hor->dia_semana == $dia && $hor->turno == $turno) {
                        $horarios_organizados[$dia][$turno] = array(
                            'id_horario' => $hor->id_horario,
                            'hora_inicio' => substr($hor->hora_inicio, 0, 5),
                            'hora_fin' => substr($hor->hora_fin, 0, 5),
                            'id_medico' => $hor->id_medico,
                            'nombre_medico' => $hor->nombre_medico
                        );
                    }
                }
            }
        }
        
        $medicos = $consultorio->listarMedicos();
        $lista_medicos = array();
        foreach($medicos as $med) {
            $lista_medicos[] = array(
                'id' => $med->id_medico,
                'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico
            );
        }
        
        echo json_encode([
            'horarios' => $horarios_organizados,
            'medicos' => $lista_medicos
        ]);
        exit();
    }
}

echo json_encode(['error' => 'Función no válida: ' . ($funcion ?? 'null')]);
?>