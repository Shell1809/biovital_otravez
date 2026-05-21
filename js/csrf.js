/**
 * CSRF Protection - Generación y manejo de tokens
 * Versión simplificada que no depende de funciones externas
 */

var CSRF = (function() {
    // Almacenar token en memoria
    var currentToken = null;
    var tokenExpiry = null;
    
    // Verificar que APP_URL esté definida
    if (typeof APP_URL === 'undefined') {
        console.error('APP_URL no está definida para CSRF');
        window.APP_URL = '';
    }
    
    // Función para generar un token aleatorio (fallback)
    function generateToken() {
        return Math.random().toString(36).substring(2, 15) + 
               Math.random().toString(36).substring(2, 15) + 
               Date.now().toString(36);
    }
    
    // Obtener token del servidor
    function fetchToken() {
        var tokenUrl = APP_URL + '/api/csrf/token';
        console.log('Obteniendo token CSRF desde:', tokenUrl);
        
        $.ajax({
            url: tokenUrl,
            type: 'POST',
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response && response.status === 'success' && response.csrf_token) {
                    currentToken = response.csrf_token;
                    tokenExpiry = Date.now() + 3600000;
                    console.log('Token CSRF obtenido correctamente');
                } else {
                    console.error('Respuesta inválida del servidor CSRF');
                    currentToken = generateToken();
                    tokenExpiry = Date.now() + 3600000;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener token CSRF:', error);
                currentToken = generateToken();
                tokenExpiry = Date.now() + 3600000;
            }
        });
    }
    
    // Inicializar token
    function init() {
        fetchToken();
        // Renovar token cada 50 minutos
        setInterval(function() {
            fetchToken();
        }, 50 * 60 * 1000);
    }
    
    // Obtener token actual
    function getToken() {
        if (!currentToken || (tokenExpiry && Date.now() > tokenExpiry)) {
            fetchToken();
        }
        return currentToken;
    }
    
    // Agregar token a los datos de una petición AJAX
    function addTokenToData(data) {
        if (typeof data === 'object' && data !== null) {
            data.csrf_token = getToken();
        }
        return data;
    }
    
    // Interceptar peticiones AJAX POST
    function setupAjaxInterceptor() {
        $(document).ajaxSend(function(event, xhr, settings) {
            if (settings.type === 'POST' && settings.data) {
                if (typeof settings.data === 'string') {
                    if (settings.data.indexOf('csrf_token=') === -1) {
                        settings.data += '&csrf_token=' + encodeURIComponent(getToken());
                    }
                } 
                else if (settings.data instanceof FormData) {
                    if (!settings.data.has('csrf_token')) {
                        settings.data.append('csrf_token', getToken());
                    }
                }
                else if (typeof settings.data === 'object') {
                    if (!settings.data.csrf_token) {
                        settings.data.csrf_token = getToken();
                    }
                }
            }
        });
    }
    
    // Agregar token a formularios HTML
    function addTokenToForms() {
        $('form').each(function() {
            if ($(this).find('input[name="csrf_token"]').length === 0) {
                $(this).append('<input type="hidden" name="csrf_token" value="' + getToken() + '">');
            } else {
                $(this).find('input[name="csrf_token"]').val(getToken());
            }
        });
    }
    
    // Actualizar tokens periódicamente
    function setupFormUpdater() {
        setInterval(function() {
            $('form input[name="csrf_token"]').each(function() {
                $(this).val(getToken());
            });
        }, 30 * 60 * 1000);
    }
    
    // API pública
    return {
        init: init,
        getToken: getToken,
        addTokenToData: addTokenToData,
        setupAjaxInterceptor: setupAjaxInterceptor,
        addTokenToForms: addTokenToForms,
        setupFormUpdater: setupFormUpdater
    };
})();

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    console.log('Inicializando CSRF...');
    CSRF.init();
    CSRF.setupAjaxInterceptor();
    CSRF.addTokenToForms();
    CSRF.setupFormUpdater();
});