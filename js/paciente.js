/**
 * js/paciente.js
 * Funcionalidades para el panel del paciente
 * Maneja: carga de datos, edición de perfil, cambio de contraseña y avatar
 */

$(document).ready(function() {
    // ==================== VARIABLES GLOBALES ====================
    var id_usuario = $('#id_usuario').val();
    var edit = false;
    
    // ==================== VERIFICACIÓN INICIAL ====================
    console.log('=== PACIENTE JS ===');
    console.log('APP_URL:', typeof APP_URL !== 'undefined' ? APP_URL : 'NO DEFINIDA');
    console.log('ID Usuario:', id_usuario);
    
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        $('#nombre_us').html('Error de configuración');
        return;
    }
    
    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de paciente no encontrado');
        $('#nombre_us').html('Error: ID no encontrado');
        return;
    }
    
    // ==================== FUNCIÓN PRINCIPAL: CARGAR DATOS ====================
    function cargarDatosPaciente(id) {
        console.log('Cargando datos del paciente ID:', id);
        $('#nombre_us').html('Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/buscar',
            type: 'POST',
            data: { 
                dato: id, 
                id_paciente: id 
            },
            dataType: 'json',
            timeout: 10000,
            success: function(paciente) {
                console.log('Datos recibidos del servidor:', paciente);
                
                if (paciente.error) {
                    console.error('Error del servidor:', paciente.error);
                    $('#nombre_us').html('Error: ' + paciente.error);
                    return;
                }
                
                actualizarUI(paciente);
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX - Status:', status);
                console.error('Error AJAX - Detalle:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                $('#nombre_us').html('Error de conexión: ' + status);
            }
        });
    }
    
    // ==================== ACTUALIZAR INTERFAZ ====================
    function actualizarUI(paciente) {
        // Datos básicos
        $('#nombre_us').html(paciente.nombre || 'No disponible');
        $('#apellidos_us').html(paciente.apellidos || 'No disponible');
        $('#edad').html(paciente.fecha_nacimiento || '-');
        $('#cedula_us').html(paciente.cedula || '-');
        $('#us_tipo').html(paciente.tipo || 'Paciente');
        
        // Datos de contacto
        $('#telefono_us').html(paciente.telefono || '-');
        $('#correo_us').html(paciente.correo || '-');
        $('#sexo_us').html(paciente.sexo || '-');
        $('#adicional_us').html(paciente.adicional || '-');
        $('#direccion_us').html(paciente.direccion || '-');
        
        // Cargar datos en campos de edición
        $('#telefono').val(paciente.telefono || '');
        $('#correo').val(paciente.correo || '');
        $('#sexo').val(paciente.sexo || '');
        $('#adicional').val(paciente.adicional || '');
        $('#direccion_detallada').val('');
        
        // Cargar avatar
        if (paciente.avatar && paciente.avatar !== '') {
            var avatarUrl = paciente.avatar;
            if (avatarUrl.indexOf('http') === -1 && avatarUrl.indexOf(APP_URL) === -1) {
                avatarUrl = APP_URL + '/' + avatarUrl.replace(/^\//, '');
            }
            $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', avatarUrl);
        } else {
            var defaultAvatar = APP_URL + '/img/avatarDES.jpg';
            $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', defaultAvatar);
        }
        
        // Cargar dirección en selects de ubicación si existe la función
        if (paciente.direccion && paciente.direccion !== '-' && typeof cargarDireccionExistente === 'function') {
            cargarDireccionExistente(paciente.direccion);
        }
        
        console.log('UI actualizada correctamente');
    }
    
    // ==================== BOTÓN EDITAR ====================
    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        edit = true;
        
        console.log('Editando paciente ID:', id_usuario);
        
        // Mostrar indicador de carga
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/capturar-datos',
            type: 'POST',
            data: { id_paciente: id_usuario },
            dataType: 'json',
            success: function(paciente) {
                console.log('Datos para edición:', paciente);
                
                if (paciente.error) {
                    mostrarMensaje('Error: ' + paciente.error, 'error');
                    return;
                }
                
                // Habilitar campos del formulario principal
                $('#telefono').prop('disabled', false);
                $('#correo').prop('disabled', false);
                $('#sexo').prop('disabled', false);
                $('#adicional').prop('disabled', false);
                
                // Habilitar campos de ubicación
                $('#estado').prop('disabled', false);
                $('#ciudad').prop('disabled', false);
                $('#municipio').prop('disabled', false);
                $('#parroquia').prop('disabled', false);
                $('#direccion_detallada').prop('disabled', false);
                
                // Cambiar estilo del botón guardar
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
                mostrarMensaje('Campos habilitados para edición', 'success');
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                mostrarMensaje('Error al cargar datos para edición: ' + status, 'error');
            },
            complete: function() {
                $btn.html(originalText);
            }
        });
    });
    
    // ==================== GUARDAR CAMBIOS ====================
    $('#form-usuario').submit(function(e) {
        e.preventDefault();
        
        if (!edit) {
            mostrarMensaje('Primero haga clic en Editar', 'error');
            return;
        }
        
        // Construir dirección completa desde los selects
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion_detallada').val();
        
        var direccion_completa = '';
        
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...') {
            direccion_completa = estado_nombre;
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...' && ciudad_nombre !== 'Seleccione un estado primero...') {
            direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...' && municipio_nombre !== 'Seleccione un estado primero...') {
            direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        }
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== 'Seleccione un municipio primero...') {
            direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        }
        if (direccion_detallada && direccion_detallada !== '') {
            direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        }
        
        var datos = {
            id_paciente: id_usuario,
            telefono: $('#telefono').val(),
            direccion: direccion_completa,
            correo: $('#correo').val(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val()
        };
        
        console.log('Guardando datos:', datos);
        console.log('Dirección completa:', direccion_completa);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/editar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    mostrarMensaje('Datos actualizados correctamente', 'success');
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional').prop('disabled', true);
                    $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    edit = false;
                    
                    // Restaurar estilo del botón guardar
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    // Recargar datos actualizados
                    cargarDatosPaciente(id_usuario);
                    
                    // Mostrar alerta de éxito
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                } else {
                    mostrarMensaje(response.error || 'Error al guardar', 'error');
                    $('#noeditado').show(1000);
                    setTimeout(function() { 
                        $('#noeditado').hide(2000); 
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar:', error);
                mostrarMensaje('Error de conexión: ' + status, 'error');
                $('#noeditado').show(1000);
                setTimeout(function() { 
                    $('#noeditado').hide(2000); 
                }, 3000);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== CAMBIAR CONTRASEÑA ====================
    $('#form-pass').submit(function(e) {
        e.preventDefault();
        
        var oldpass = $('#oldpass').val();
        var newpass = $('#newpass').val();
        
        if (newpass.length < 6) {
            mostrarMensaje('La nueva contraseña debe tener al menos 6 caracteres', 'error');
            return;
        }
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/cambiar-password',
            type: 'POST',
            data: {
                id_paciente: id_usuario,
                oldpass: oldpass,
                newpass: newpass
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio contraseña:', response);
                
                if (response.resultado === 'update') {
                    mostrarMensaje('Contraseña actualizada correctamente', 'success');
                    $('#update').show(1000);
                    setTimeout(function() { 
                        $('#update').hide(2000); 
                    }, 3000);
                    $('#form-pass').trigger('reset');
                    setTimeout(function() { 
                        $('#cambiocontra').modal('hide'); 
                    }, 1500);
                } else {
                    mostrarMensaje('Contraseña actual incorrecta', 'error');
                    $('#noupdate').show(1000);
                    setTimeout(function() { 
                        $('#noupdate').hide(2000); 
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar contraseña:', error);
                mostrarMensaje('Error de conexión: ' + status, 'error');
                $('#noupdate').show(1000);
                setTimeout(function() { 
                    $('#noupdate').hide(2000); 
                }, 3000);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== CAMBIAR AVATAR ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('id_paciente', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/cambiar-foto',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio foto:', response);
                
                if (response.alert === 'edit') {
                    var nuevaRuta = response.ruta;
                    if (nuevaRuta && nuevaRuta !== '') {
                        $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', nuevaRuta + '?t=' + new Date().getTime());
                    }
                    mostrarMensaje('Avatar actualizado correctamente', 'success');
                    $('#edit').show(1000);
                    setTimeout(function() { 
                        $('#edit').hide(2000); 
                    }, 3000);
                    $('#form-photo').trigger('reset');
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    cargarDatosPaciente(id_usuario);
                } else {
                    mostrarMensaje(response.error || 'Formato no admitido', 'error');
                    $('#noedit').show(1000);
                    setTimeout(function() { 
                        $('#noedit').hide(2000); 
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                mostrarMensaje('Error de conexión: ' + status, 'error');
                $('#noedit').show(1000);
                setTimeout(function() { 
                    $('#noedit').hide(2000); 
                }, 3000);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== FUNCIÓN PARA MOSTRAR MENSAJES ====================
    function mostrarMensaje(mensaje, tipo) {
        console.log('Mensaje:', tipo, '-', mensaje);
        
        if (tipo === 'success') {
            // Usar SweetAlert si está disponible, si no, alert normal
            if (typeof Swal !== 'undefined') {
                Swal.fire('Éxito', mensaje, 'success');
            } else {
                alert('✓ Éxito: ' + mensaje);
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', mensaje, 'error');
            } else {
                alert('✗ Error: ' + mensaje);
            }
        }
    }
    
    // ==================== INICIALIZAR CARGA DE DATOS ====================
    cargarDatosPaciente(id_usuario);
    
    // ==================== EVENTOS ADICIONALES ====================
    // Limpiar formulario al cerrar modales
    $('#cambiocontra').on('hidden.bs.modal', function() {
        $('#form-pass').trigger('reset');
        $('#update, #noupdate').hide();
    });
    
    $('#cambiophoto').on('hidden.bs.modal', function() {
        $('#form-photo').trigger('reset');
        $('#edit, #noedit').hide();
    });
});