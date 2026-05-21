// Esperar a que APP_URL esté definida
$(document).ready(function() {
    // Verificar que APP_URL esté definida
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        $('#nombre_us').html('Error de configuración. Recargue la página.');
        return;
    }
    
    console.log('=== DEPURACIÓN MÉDICO ===');
    console.log('APP_URL:', APP_URL);
    
    var id_usuario = $('#id_usuario').val();
    var edit = false;
    
    console.log('ID Usuario:', id_usuario);
    
    if(!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de médico no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        return;
    }
    
    cargarDatosMedico(id_usuario);
    
    function cargarDatosMedico(id) {
        console.log('Cargando datos del médico ID:', id);
        console.log('URL de petición:', APP_URL + '/api/medicos/buscar');
        
        $.ajax({
            url: APP_URL + '/api/medicos/buscar',
            type: 'POST',
            data: { dato: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if(response.error) {
                    console.error('Error del servidor:', response.error);
                    $('#nombre_us').html('Error: ' + response.error);
                    return;
                }
                
                actualizarUI(response);
                
                // Cargar dirección en los campos de edición
                if (response.direccion && response.direccion !== '-') {
                    cargarDireccionExistente(response.direccion);
                } else {
                    cargarEstados();
                }
                
                console.log('Datos actualizados correctamente');
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX - Status:', status);
                console.error('Error AJAX - Detalle:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                $('#nombre_us').html('Error de conexión: ' + status);
                cargarEstados();
            }
        });
    }
    
    function actualizarUI(medico) {
        console.log('Actualizando UI con datos:', medico);
        
        $('#nombre_us').html(medico.nombre || 'No disponible');
        $('#apellidos_us').html(medico.apellidos || 'No disponible');
        $('#edad').html(medico.fecha_nacimiento || 'No disponible');
        $('#cedula_us').html(medico.cedula || 'No disponible');
        $('#us_tipo').html(medico.tipo || 'Médico');
        $('#telefono_us').html(medico.telefono || '-');
        $('#correo_us').html(medico.correo || '-');
        $('#sexo_us').html(medico.sexo || '-');
        $('#adicional_us').html(medico.adicional || '-');
        $('#direccion_us').html(medico.direccion || '-');
        
        // Cargar datos en los campos del formulario de edición
        $('#telefono').val(medico.telefono || '');
        $('#correo').val(medico.correo || '');
        $('#sexo').val(medico.sexo || '');
        $('#adicional').val(medico.adicional || '');
        $('#direccion_detallada').val('');
        
        // Cargar avatar
        if(medico.avatar && medico.avatar !== '') {
            var avatarUrl = medico.avatar;
            if(avatarUrl.indexOf('http') === -1 && avatarUrl.indexOf('/') === 0) {
                avatarUrl = APP_URL + avatarUrl;
            } else if(avatarUrl.indexOf('http') === -1) {
                avatarUrl = APP_URL + '/' + avatarUrl;
            }
            console.log('Cargando avatar desde:', avatarUrl);
            $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', avatarUrl);
        } else {
            var defaultAvatar = APP_URL + '/img/avatarDES.jpg';
            $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', defaultAvatar);
        }
        
        console.log('UI actualizada correctamente');
    }
    
    // ==================== FUNCIONES DE UBICACIÓN ====================
    
    function cargarDireccionExistente(direccion_completa) {
        console.log('Parseando dirección existente:', direccion_completa);
        
        if (!direccion_completa || direccion_completa === '-') {
            console.log('No hay dirección guardada');
            cargarEstados();
            return;
        }
        
        let direccion_detallada = '';
        let ubicacion = direccion_completa;
        
        // Separar la dirección detallada de la ubicación
        if (direccion_completa.includes(' - ')) {
            let partes = direccion_completa.split(' - ');
            ubicacion = partes[0];
            direccion_detallada = partes.slice(1).join(' - ');
        }
        
        // Dividir la ubicación por comas
        let ubicacion_partes = ubicacion.split(', ').filter(p => p.trim() !== '');
        console.log('Partes de ubicación:', ubicacion_partes);
        
        // Cargar la dirección detallada en el campo
        $('#direccion_detallada').val(direccion_detallada);
        
        // Cargar los selects con los valores existentes
        cargarEstadosConSeleccion(ubicacion_partes);
    }
    
    function cargarEstadosConSeleccion(ubicacion_partes) {
        // Habilitar los selects de ubicación
        $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(estados) {
                let options = '<option value="">Seleccione un estado...</option>';
                for (let estado of estados) {
                    options += `<option value="${estado.id_estado}">${estado.estado}</option>`;
                }
                $('#estado').html(options).prop('disabled', false);
                
                // Seleccionar el estado guardado
                if (ubicacion_partes[0] && ubicacion_partes[0] !== '') {
                    let estado_nombre = ubicacion_partes[0].trim();
                    $('#estado option').each(function() {
                        if ($(this).text() === estado_nombre) {
                            $(this).prop('selected', true);
                            let id_estado = $(this).val();
                            if (id_estado) {
                                cargarCiudadesConSeleccion(id_estado, ubicacion_partes);
                                cargarMunicipiosConSeleccion(id_estado, ubicacion_partes);
                            }
                        }
                    });
                }
            },
            error: function() {
                cargarEstadosFallbackConSeleccion(ubicacion_partes);
            }
        });
    }
    
    function cargarEstadosFallbackConSeleccion(ubicacion_partes) {
        const estados = [
            {id_estado: 1, estado: 'Amazonas'}, {id_estado: 2, estado: 'Anzoátegui'},
            {id_estado: 3, estado: 'Apure'}, {id_estado: 4, estado: 'Aragua'},
            {id_estado: 5, estado: 'Barinas'}, {id_estado: 6, estado: 'Bolívar'},
            {id_estado: 7, estado: 'Carabobo'}, {id_estado: 8, estado: 'Cojedes'},
            {id_estado: 9, estado: 'Delta Amacuro'}, {id_estado: 10, estado: 'Falcón'},
            {id_estado: 11, estado: 'Guárico'}, {id_estado: 12, estado: 'Lara'},
            {id_estado: 13, estado: 'Mérida'}, {id_estado: 14, estado: 'Miranda'},
            {id_estado: 15, estado: 'Monagas'}, {id_estado: 16, estado: 'Nueva Esparta'},
            {id_estado: 17, estado: 'Portuguesa'}, {id_estado: 18, estado: 'Sucre'},
            {id_estado: 19, estado: 'Táchira'}, {id_estado: 20, estado: 'Trujillo'},
            {id_estado: 21, estado: 'La Guaira'}, {id_estado: 22, estado: 'Yaracuy'},
            {id_estado: 23, estado: 'Zulia'}, {id_estado: 24, estado: 'Distrito Capital'}
        ];
        let options = '<option value="">Seleccione un estado...</option>';
        for (let estado of estados) {
            options += `<option value="${estado.id_estado}">${estado.estado}</option>`;
        }
        $('#estado').html(options).prop('disabled', false);
        
        if (ubicacion_partes[0] && ubicacion_partes[0] !== '') {
            let estado_nombre = ubicacion_partes[0].trim();
            $('#estado option').each(function() {
                if ($(this).text() === estado_nombre) {
                    $(this).prop('selected', true);
                    let id_estado = $(this).val();
                    if (id_estado) {
                        cargarCiudadesConSeleccion(id_estado, ubicacion_partes);
                        cargarMunicipiosConSeleccion(id_estado, ubicacion_partes);
                    }
                }
            });
        }
    }
    
    function cargarCiudadesConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(ciudades) {
                let options = '<option value="">Seleccione una ciudad...</option>';
                for (let ciudad of ciudades) {
                    options += `<option value="${ciudad.id_ciudad}">${ciudad.ciudad}</option>`;
                }
                $('#ciudad').html(options).prop('disabled', false);
                
                if (ubicacion_partes[1] && ubicacion_partes[1] !== '') {
                    let ciudad_nombre = ubicacion_partes[1].trim();
                    $('#ciudad option').each(function() {
                        if ($(this).text() === ciudad_nombre) {
                            $(this).prop('selected', true);
                        }
                    });
                }
            },
            error: function() {
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarMunicipiosConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(municipios) {
                let options = '<option value="">Seleccione un municipio...</option>';
                for (let municipio of municipios) {
                    options += `<option value="${municipio.id_municipio}">${municipio.municipio}</option>`;
                }
                $('#municipio').html(options).prop('disabled', false);
                
                if (ubicacion_partes[2] && ubicacion_partes[2] !== '') {
                    let municipio_nombre = ubicacion_partes[2].trim();
                    $('#municipio option').each(function() {
                        if ($(this).text() === municipio_nombre) {
                            $(this).prop('selected', true);
                            let id_municipio = $(this).val();
                            if (id_municipio) {
                                cargarParroquiasConSeleccion(id_municipio, ubicacion_partes);
                            }
                        }
                    });
                }
            },
            error: function() {
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarParroquiasConSeleccion(id_municipio, ubicacion_partes) {
        if (!id_municipio) return;
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(parroquias) {
                let options = '<option value="">Seleccione una parroquia...</option>';
                for (let parroquia of parroquias) {
                    options += `<option value="${parroquia.id_parroquia}">${parroquia.parroquia}</option>`;
                }
                $('#parroquia').html(options).prop('disabled', false);
                
                if (ubicacion_partes[3] && ubicacion_partes[3] !== '') {
                    let parroquia_nombre = ubicacion_partes[3].trim();
                    $('#parroquia option').each(function() {
                        if ($(this).text() === parroquia_nombre) {
                            $(this).prop('selected', true);
                        }
                    });
                }
            },
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarEstados() {
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(estados) {
                let options = '<option value="">Seleccione un estado...</option>';
                for (let estado of estados) {
                    options += `<option value="${estado.id_estado}">${estado.estado}</option>`;
                }
                $('#estado').html(options).prop('disabled', false);
            },
            error: function() {
                cargarEstadosFallback();
            }
        });
    }
    
    function cargarEstadosFallback() {
        const estados = [
            {id_estado: 1, estado: 'Amazonas'}, {id_estado: 2, estado: 'Anzoátegui'},
            {id_estado: 3, estado: 'Apure'}, {id_estado: 4, estado: 'Aragua'},
            {id_estado: 5, estado: 'Barinas'}, {id_estado: 6, estado: 'Bolívar'},
            {id_estado: 7, estado: 'Carabobo'}, {id_estado: 8, estado: 'Cojedes'},
            {id_estado: 9, estado: 'Delta Amacuro'}, {id_estado: 10, estado: 'Falcón'},
            {id_estado: 11, estado: 'Guárico'}, {id_estado: 12, estado: 'Lara'},
            {id_estado: 13, estado: 'Mérida'}, {id_estado: 14, estado: 'Miranda'},
            {id_estado: 15, estado: 'Monagas'}, {id_estado: 16, estado: 'Nueva Esparta'},
            {id_estado: 17, estado: 'Portuguesa'}, {id_estado: 18, estado: 'Sucre'},
            {id_estado: 19, estado: 'Táchira'}, {id_estado: 20, estado: 'Trujillo'},
            {id_estado: 21, estado: 'La Guaira'}, {id_estado: 22, estado: 'Yaracuy'},
            {id_estado: 23, estado: 'Zulia'}, {id_estado: 24, estado: 'Distrito Capital'}
        ];
        let options = '<option value="">Seleccione un estado...</option>';
        for (let estado of estados) {
            options += `<option value="${estado.id_estado}">${estado.estado}</option>`;
        }
        $('#estado').html(options).prop('disabled', false);
    }
    
    function cargarCiudades(id_estado) {
        if (!id_estado) {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#ciudad').html('<option value="">Cargando ciudades...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(ciudades) {
                let options = '<option value="">Seleccione una ciudad...</option>';
                for (let ciudad of ciudades) {
                    options += `<option value="${ciudad.id_ciudad}">${ciudad.ciudad}</option>`;
                }
                $('#ciudad').html(options).prop('disabled', false);
            },
            error: function() {
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarMunicipios(id_estado) {
        if (!id_estado) {
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#municipio').html('<option value="">Cargando municipios...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(municipios) {
                let options = '<option value="">Seleccione un municipio...</option>';
                for (let municipio of municipios) {
                    options += `<option value="${municipio.id_municipio}">${municipio.municipio}</option>`;
                }
                $('#municipio').html(options).prop('disabled', false);
                $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            },
            error: function() {
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarParroquias(id_municipio) {
        if (!id_municipio) {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(parroquias) {
                let options = '<option value="">Seleccione una parroquia...</option>';
                for (let parroquia of parroquias) {
                    options += `<option value="${parroquia.id_parroquia}">${parroquia.parroquia}</option>`;
                }
                $('#parroquia').html(options).prop('disabled', false);
            },
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }
    
    // ==================== EVENTOS DE UBICACIÓN ====================
    $(document).on('change', '#estado', function() {
        let id_estado = $(this).val();
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipios(id_estado);
        } else {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    $(document).on('change', '#municipio', function() {
        let id_municipio = $(this).val();
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    // ==================== BOTÓN EDITAR ====================
    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        edit = true;
        
        console.log('Editando médico ID:', id_usuario);
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/medicos/capturar-datos',
            type: 'POST',
            data: { id_medico: id_usuario },
            dataType: 'json',
            success: function(medico) {
                console.log('Datos a editar:', medico);
                
                if(medico.error) {
                    alert('Error: ' + medico.error);
                    return;
                }
                
                // Llenar los campos del formulario
                $('#telefono').val(medico.telefono || '').prop('disabled', false);
                $('#correo').val(medico.correo || '').prop('disabled', false);
                $('#sexo').val(medico.sexo || '').prop('disabled', false);
                $('#adicional').val(medico.adicional || '').prop('disabled', false);
                
                // Cargar la dirección existente en los selectores
                if (medico.direccion && medico.direccion !== '-') {
                    cargarDireccionExistente(medico.direccion);
                } else {
                    cargarEstados();
                }
                
                // Cambiar el estilo del botón guardar
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
                $('#editado').show(1000);
                setTimeout(function() { $('#editado').hide(2000); }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                alert('Error al cargar datos para edición: ' + status);
            },
            complete: function() {
                $btn.html(originalText);
            }
        });
    });
    
    // ==================== FORMULARIO DE EDICIÓN - GUARDAR CAMBIOS ====================
    $('#form-usuario').submit(function(e) {
        e.preventDefault();
        
        if (!edit) {
            alert('Primero haga clic en Editar');
            return;
        }
        
        // Construir dirección completa
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion_detallada').val();
        
        console.log('Estado seleccionado:', estado_nombre);
        console.log('Ciudad seleccionada:', ciudad_nombre);
        console.log('Municipio seleccionado:', municipio_nombre);
        console.log('Parroquia seleccionada:', parroquia_nombre);
        console.log('Dirección detallada:', direccion_detallada);
        
        var direccion_completa = '';
        
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...' && estado_nombre !== '') {
            direccion_completa = estado_nombre;
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...' && ciudad_nombre !== '' && ciudad_nombre !== 'Seleccione un estado primero...') {
            direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...' && municipio_nombre !== '' && municipio_nombre !== 'Seleccione un estado primero...') {
            direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        }
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== '' && parroquia_nombre !== 'Seleccione un municipio primero...') {
            direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        }
        if (direccion_detallada && direccion_detallada !== '') {
            direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        }
        
        console.log('Dirección completa a guardar:', direccion_completa);
        
        var datos = {
            id_medico: id_usuario,
            telefono: $('#telefono').val(),
            direccion: direccion_completa,
            correo: $('#correo').val(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val()
        };
        
        console.log('Guardando datos:', datos);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/medicos/editar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta editar:', response);
                
                if (response.success) {
                    $('#editado').show(1000);
                    setTimeout(function() { $('#editado').hide(2000); }, 2000);
                    
                    // Resetear formulario
                    $('#form-usuario').trigger('reset');
                    $('#telefono, #correo, #sexo, #adicional').prop('disabled', true);
                    $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    edit = false;
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    // Recargar datos del médico
                    cargarDatosMedico(id_usuario);
                    
                    alert('Datos actualizados correctamente');
                } else {
                    $('#noeditado').show(1000);
                    setTimeout(function() { $('#noeditado').hide(2000); }, 2000);
                    alert(response.error || 'Error al guardar');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar:', error);
                $('#noeditado').show(1000);
                setTimeout(function() { $('#noeditado').hide(2000); }, 2000);
                alert('Error de conexión: ' + status);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== CAMBIAR CONTRASEÑA ====================
    $('#form-pass').submit(function(e) {
        e.preventDefault();
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/medicos/cambiar-password',
            type: 'POST',
            data: {
                id_medico: id_usuario,
                oldpass: $('#oldpass').val(),
                newpass: $('#newpass').val()
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio contraseña:', response);
                
                if (response.resultado == 'update') {
                    $('#update').show(1000);
                    setTimeout(function() { $('#update').hide(2000); }, 2000);
                    $('#form-pass').trigger('reset');
                    setTimeout(function() { $('#cambiocontra').modal('hide'); }, 1500);
                } else {
                    $('#noupdate').show(1000);
                    setTimeout(function() { $('#noupdate').hide(2000); }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#noupdate').show(1000);
                setTimeout(function() { $('#noupdate').hide(2000); }, 2000);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== CAMBIAR FOTO ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('id_medico', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/medicos/cambiar-foto',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio foto:', response);
                
                if (response.alert == 'edit') {
                    var nuevaRuta = response.ruta;
                    $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', nuevaRuta + '?t=' + new Date().getTime());
                    $('#edit').show(1000);
                    setTimeout(function() { $('#edit').hide(2000); }, 2000);
                    $('#form-photo').trigger('reset');
                    setTimeout(function() { $('#cambiophoto').modal('hide'); }, 1500);
                    cargarDatosMedico(id_usuario);
                } else {
                    $('#noedit').show(1000);
                    setTimeout(function() { $('#noedit').hide(2000); }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                $('#noedit').show(1000);
                setTimeout(function() { $('#noedit').hide(2000); }, 2000);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});