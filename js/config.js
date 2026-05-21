/**
 * Configuración global del sistema
 * Detecta automáticamente la URL base del proyecto
 */

(function() {
    // Detectar la URL base del proyecto
    var path = window.location.pathname;
    var baseUrl = '';
    
    // Patrones comunes de instalación
    var patterns = [
        { pattern: '/biovital/', base: '/biovital' },
        { pattern: '/public/', base: '/public' },
        { pattern: '/medico/', base: '' },
        { pattern: '/paciente/', base: '' },
        { pattern: '/administrador/', base: '' },
        { pattern: '/asistente/', base: '' }
    ];
    
    // Buscar coincidencia con patrones conocidos
    for (var i = 0; i < patterns.length; i++) {
        if (path.includes(patterns[i].pattern)) {
            baseUrl = patterns[i].base;
            break;
        }
    }
    
    // Si no se detectó, inferir de la estructura de carpetas
    if (baseUrl === '') {
        var parts = path.split('/');
        // Eliminar partes vacías
        parts = parts.filter(function(p) { return p !== ''; });
        
        // Buscar la carpeta del proyecto (la que contiene 'controlador', 'modelo', 'vista')
        // Por defecto, la primera parte suele ser el proyecto
        if (parts.length > 0 && !parts[0].includes('.php') && !parts[0].includes('.')) {
            baseUrl = '/' + parts[0];
        }
    }
    
    // Variables globales de configuración
    window.CONFIG = {
        BASE_URL: baseUrl,
        API_URL: baseUrl + '/controlador',
        UPLOADS_URL: baseUrl + '/img',
        getControllerUrl: function(controller) {
            return this.API_URL + '/' + controller + '.php';
        }
    };
    
    console.log('Configuración cargada:', window.CONFIG);
})();
