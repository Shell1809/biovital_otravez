// csrf_helper.js - Versión corregida
function fetchCSRFToken() {
    var token = null;
    $.ajax({
        url: '../api/get_csrf_token.php',
        method: 'GET',
        dataType: 'json',
        async: false,
        success: function(response) {
            if (response && response.status === 'success' && response.csrf_token) {
                token = response.csrf_token;
            }
        },
        error: function(xhr) {
            console.error('Error fetching CSRF token:', xhr.status);
        }
    });
    return token;
}

function initializeCSRF() {
    if ($('#csrf_token').val() === '') {
        var token = fetchCSRFToken();
        if (token) {
            $('#csrf_token').val(token);
            console.log('CSRF token loaded successfully');
        } else {
            console.error('Failed to load CSRF token');
            mostrarErrorCSRF('No se pudo inicializar el formulario. Recargue la página.');
        }
    }
}

function mostrarErrorCSRF(mensaje) {
    $('#alert-error').hide();
    $('#error-message').text(mensaje);
    $('#alert-error').fadeIn();
    $('button[type="submit"]').prop('disabled', true);
}

$(document).ready(function() {
    initializeCSRF();
});