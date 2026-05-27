<?php
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
// Configuración de la aplicación
// NO iniciar sesión aquí - el Front Controller ya lo hace

// Detectar URL base automáticamente
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim($protocol . $host . $scriptName, '/');
<<<<<<< HEAD
define('APP_NAME', 'BioVital');
define('APP_VERSION', '1.0.0');
define('APP_URL', $baseUrl);  
=======
<<<<<<< HEAD
define('APP_NAME', 'BioVital');
define('APP_VERSION', '1.0.0');
define('APP_URL', $baseUrl);  
=======

define('APP_NAME', 'BioVital');
define('APP_VERSION', '1.0.0');
define('APP_URL', $baseUrl);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852

// Configuración de seguridad
define('CSRF_TOKEN_LIFETIME', 3600);
define('PASSWORD_MIN_LENGTH', 6);

// Configuración de archivos
define('MAX_FILE_SIZE', 5242880);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Configuración de paginación
define('ITEMS_PER_PAGE', 15);
?>