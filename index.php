<?php
// ==================== FRONT CONTROLLER ====================
// Iniciar sesión SOLO UNA VEZ al principio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/app.php';

// Configuración de errores (desactivar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Constantes de rutas
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));
define('CONTROLLER_PATH', ROOT_PATH . DS . 'controlador');
define('MODEL_PATH', ROOT_PATH . DS . 'modelo');
define('VIEW_PATH', ROOT_PATH . DS . 'vista');
define('API_PATH', ROOT_PATH . DS . 'api');
define('IMG_PATH', ROOT_PATH . DS . 'img');

// Autoloader
spl_autoload_register(function($className) {
    $classMap = [
        'Security' => MODEL_PATH . DS . 'Security.php',
        'Conexion' => MODEL_PATH . DS . 'Conexion.php',
        'Paciente' => MODEL_PATH . DS . 'Paciente.php',
        'Medico' => MODEL_PATH . DS . 'Medico.php',
        'Asistente' => MODEL_PATH . DS . 'Asistente.php',
        'Administrador' => MODEL_PATH . DS . 'Administrador.php',
        'Consultorio' => MODEL_PATH . DS . 'Consultorio.php',
        'Receta' => MODEL_PATH . DS . 'Receta.php',
        'LoginPaciente' => MODEL_PATH . DS . 'LoginPaciente.php',
        'LoginMedico' => MODEL_PATH . DS . 'LoginMedico.php',
        'LoginAsistente' => MODEL_PATH . DS . 'LoginAsistente.php',
        'LoginAdministrador' => MODEL_PATH . DS . 'LoginAdministrador.php'
    ];
    
    if (isset($classMap[$className])) {
        require_once $classMap[$className];
        return;
    }
    
    $modelFile = MODEL_PATH . DS . $className . '.php';
    $controllerFile = CONTROLLER_PATH . DS . $className . '.php';
    
    if (file_exists($modelFile)) {
        require_once $modelFile;
    } elseif (file_exists($controllerFile)) {
        require_once $controllerFile;
    }
});

// Funciones auxiliares
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function redirect($url, $permanent = false) {
    if ($permanent) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
    exit();
}

function renderView($view, $data = []) {
    extract($data);
    $viewFile = VIEW_PATH . DS . $view . '.php';
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        die("Vista no encontrada: {$view}");
    }
}

// ==================== ROUTER ====================
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', $requestUri);
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Verificar si las rutas existen
$routesFile = ROOT_PATH . DS . 'config' . DS . 'routes.php';
if (!file_exists($routesFile)) {
    die("Error: Archivo de rutas no encontrado en: " . $routesFile);
}

// Definición de rutas
$routes = require_once $routesFile;

function verificarRol($rolRequerido, $rolUsuario) {
    if (is_array($rolRequerido)) {
        return in_array($rolUsuario, $rolRequerido);
    }
    return $rolRequerido === $rolUsuario;
}

$routeFound = false;
foreach ($routes as $route => $config) {
    if ($path === $route) {
        $routeFound = true;
        
        if (isset($config['method']) && $config['method'] !== $method) {
            jsonResponse(['error' => 'Método no permitido'], 405);
        }
        
        if (isset($config['auth']) && $config['auth'] === true) {
            if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
                if ($isAjax) {
                    jsonResponse(['error' => 'Sesión no iniciada'], 401);
                } else {
                    redirect('login');
                }
            }
        }
        
        if (isset($config['rol'])) {
            if (!isset($_SESSION['rol'])) {
                if ($isAjax) {
                    jsonResponse(['error' => 'No autorizado'], 403);
                } else {
                    redirect('login');
                }
            }
            if (!verificarRol($config['rol'], $_SESSION['rol'])) {
                if ($isAjax) {
                    jsonResponse(['error' => 'No tiene permisos'], 403);
                } else {
                    redirect('login');
                }
            }
        }
        
        $controllerName = $config['controller'];
        $actionName = $config['action'];
        $controllerFile = CONTROLLER_PATH . DS . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            jsonResponse(['error' => "Archivo de controlador no encontrado: {$controllerName}"], 500);
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            jsonResponse(['error' => "Controlador '{$controllerName}' no encontrado"], 500);
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $actionName)) {
            jsonResponse(['error' => "Acción '{$actionName}' no encontrada en {$controllerName}"], 500);
        }
        
        $controller->$actionName();
        break;
    }
}

if (!$routeFound) {
    if ($isAjax) {
        jsonResponse(['error' => 'Ruta no encontrada: ' . $path], 404);
    } else {
        http_response_code(404);
        if (file_exists(VIEW_PATH . DS . 'errors' . DS . '404.php')) {
            renderView('errors/404');
        } else {
            echo "<h1>404 - Página no encontrada</h1>";
            echo "<p>La ruta solicitada no existe: {$path}</p>";
        }
    }
}