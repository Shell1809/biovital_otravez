<?php
// NO iniciar sesión aquí - el Front Controller ya lo hace
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}

$securityPath = dirname(__DIR__, 2) . '/modelo/Security.php';
if (!file_exists($securityPath)) {
    die("Error: No se encuentra Security.php en: " . $securityPath);
}
include_once $securityPath;

$nombre_usuario = $_SESSION['nombre_us'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
        console.log('APP_URL:', APP_URL);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <title>Administrador | Nuevo Consultorio</title>
    
    <style>
        .checkbox-group {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 10px;
        }
        .checkbox-group .form-check {
            margin-bottom: 5px;
        }
        .preview-card {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .csrf-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            text-align: center;
        }
        .required-field {
            color: #dc3545;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">Cerrar sesión</a>
    </ul>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo APP_URL; ?>/panel/administrador" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">BIOVITAL</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
           <div class="image">
    <img id="avatar_nav" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
</div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($nombre_usuario); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-header">
                    <i class="fas fa-user-shield"></i> Usuario
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-chart-line"></i> Gestión
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/administrador/usuarios" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Usuarios</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-hospital-user"></i> Clínica
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/consultorios" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Consultorios</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Recetas</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-chart-bar"></i> Reportes
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Estadísticas</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus-circle"></i> Crear Consultorio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/consultorios">Consultorios</a></li>
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Información Básica</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success" id="alertExito" style="display:none;">
                                <i class="fas fa-check-circle"></i> Consultorio creado exitosamente
                            </div>
                            <div class="alert alert-danger" id="alertError" style="display:none;">
                                <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                            </div>
                            
                            <form id="formCrearConsultorio" method="POST">
                                <?php echo Security::campoCSRF(); ?>
                                
                                <div class="form-group">
                                    <label class="required-field">Nombre del Consultorio</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">Apertura Habitual</label>
                                            <input type="time" class="form-control" id="apertura" name="apertura" value="08:00" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">Cierre Habitual</label>
                                            <input type="time" class="form-control" id="cierre" name="cierre" value="17:00" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Especialidades Admitidas</label>
                                    <div class="checkbox-group" id="especialidades_container">
                                        <div class="text-center">Cargando especialidades...</div>
                                    </div>
                                </div>

                                <h4 class="mt-4"><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">Estado</label>
                                            <select class="form-control" id="estado" name="estado" required>
                                                <option value="">Seleccione un estado...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">Ciudad</label>
                                            <select class="form-control" id="ciudad" name="ciudad" required disabled>
                                                <option value="">Primero seleccione un estado...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Municipio</label>
                                            <select class="form-control" id="municipio" name="municipio" disabled>
                                                <option value="">Primero seleccione un estado...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Parroquia</label>
                                            <select class="form-control" id="parroquia" name="parroquia" disabled>
                                                <option value="">Primero seleccione un municipio...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="required-field">Dirección Detallada</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required placeholder="Av. Principal, Edificio, Número, etc.">
                                    <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Piso 3, Oficina 5</small>
                                </div>

                                <h4 class="mt-4"><i class="fas fa-address-card"></i> Datos de Contacto</h4>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Teléfono Interno/Directo</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email del Consultorio</label>
                                            <input type="email" class="form-control" id="email" name="email">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-save"></i> Guardar Consultorio
                                </button>
                                
                                <div class="csrf-info mt-3">
                                    <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-eye"></i> Vista Previa</h3>
                        </div>
                        <div class="card-body">
                            <div class="preview-card p-3">
                                <h4 id="preview_nombre">Nombre del Consultorio</h4>
                                <p><i class="fas fa-map-marker-alt"></i> <span id="preview_ciudad">Ciudad</span></p>
                                <p id="preview_descripcion" class="text-muted small">Descripción</p>
                                <p><i class="fas fa-phone"></i> <span id="preview_telefono">-</span></p>
                                <p><i class="fas fa-envelope"></i> <span id="preview_email">-</span></p>
                                <hr>
                                <div class="text-center">
                                    <span class="badge badge-success">Consultorio disponible</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2024 BioVital.</strong> Todos los derechos reservados.
</footer>

</div>

<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ==================== FUNCIONES DE UBICACIÓN (funcionan igual que en pac_editar_datos.php) ====================

function cargarEstados() {
    console.log('Cargando estados...');
    $.ajax({
        url: APP_URL + '/api/ubicacion/estados',
        type: 'POST',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Estados recibidos:', response);
            var estados = Array.isArray(response) ? response : (response.data || response.estados || []);
            if (!Array.isArray(estados) || estados.length === 0) {
                cargarEstadosFallback();
                return;
            }
            var options = '<option value="">Seleccione un estado...</option>';
            for (var i = 0; i < estados.length; i++) {
                var estado = estados[i];
                var id = estado.id_estado || estado.id || '';
                var nombre = estado.estado || estado.nombre || '';
                options += '<option value="' + id + '">' + escapeHtml(nombre) + '</option>';
            }
            $('#estado').html(options);
            $('#estado').prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error('Error cargando estados:', error);
            cargarEstadosFallback();
        }
    });
}

function cargarEstadosFallback() {
    console.log('Usando datos de estados fallback');
    var estados = [
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
    var options = '<option value="">Seleccione un estado...</option>';
    for (var i = 0; i < estados.length; i++) {
        options += '<option value="' + estados[i].id_estado + '">' + estados[i].estado + '</option>';
    }
    $('#estado').html(options);
    $('#estado').prop('disabled', false);
}

function cargarCiudades(id_estado) {
    if (!id_estado) {
        $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
        return;
    }
    
    console.log('Cargando ciudades para estado ID:', id_estado);
    $('#ciudad').html('<option value="">Cargando ciudades...</option>').prop('disabled', true);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/ciudades',
        type: 'POST',
        data: { id_estado: id_estado },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Ciudades recibidas:', response);
            var ciudades = Array.isArray(response) ? response : (response.data || response.ciudades || []);
            if (!Array.isArray(ciudades) || ciudades.length === 0) {
                $('#ciudad').html('<option value="">No hay ciudades disponibles</option>').prop('disabled', false);
                return;
            }
            var options = '<option value="">Seleccione una ciudad...</option>';
            for (var i = 0; i < ciudades.length; i++) {
                var ciudad = ciudades[i];
                var id = ciudad.id_ciudad || ciudad.id || '';
                var nombre = ciudad.ciudad || ciudad.nombre || '';
                options += '<option value="' + id + '">' + escapeHtml(nombre) + '</option>';
            }
            $('#ciudad').html(options).prop('disabled', false);
        },
        error: function(xhr) {
            console.error('Error cargando ciudades:', xhr.responseText);
            $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
        }
    });
}

function cargarMunicipiosPorEstado(id_estado) {
    if (!id_estado) {
        $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
        $('#parroquia').html('<option value="">Primero seleccione un municipio...</option>').prop('disabled', true);
        return;
    }
    
    console.log('Cargando municipios para estado ID:', id_estado);
    $('#municipio').html('<option value="">Cargando municipios...</option>').prop('disabled', true);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/municipios',
        type: 'POST',
        data: { id_estado: id_estado },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Municipios recibidos:', response);
            var municipios = Array.isArray(response) ? response : (response.data || response.municipios || []);
            if (!Array.isArray(municipios) || municipios.length === 0) {
                $('#municipio').html('<option value="">No hay municipios disponibles</option>').prop('disabled', false);
                $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
                return;
            }
            var options = '<option value="">Seleccione un municipio...</option>';
            for (var i = 0; i < municipios.length; i++) {
                var municipio = municipios[i];
                var id = municipio.id_municipio || municipio.id || '';
                var nombre = municipio.municipio || municipio.nombre || '';
                options += '<option value="' + id + '">' + escapeHtml(nombre) + '</option>';
            }
            $('#municipio').html(options).prop('disabled', false);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        },
        error: function(xhr) {
            console.error('Error cargando municipios:', xhr.responseText);
            $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
        }
    });
}

function cargarParroquias(id_municipio) {
    if (!id_municipio) {
        $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        return;
    }
    
    console.log('Cargando parroquias para municipio ID:', id_municipio);
    $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', true);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/parroquias',
        type: 'POST',
        data: { id_municipio: id_municipio },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Parroquias recibidas:', response);
            var parroquias = Array.isArray(response) ? response : (response.data || response.parroquias || []);
            if (!Array.isArray(parroquias) || parroquias.length === 0) {
                $('#parroquia').html('<option value="">No hay parroquias disponibles</option>').prop('disabled', false);
                return;
            }
            var options = '<option value="">Seleccione una parroquia...</option>';
            for (var i = 0; i < parroquias.length; i++) {
                var parroquia = parroquias[i];
                var id = parroquia.id_parroquia || parroquia.id || '';
                var nombre = parroquia.parroquia || parroquia.nombre || '';
                options += '<option value="' + id + '">' + escapeHtml(nombre) + '</option>';
            }
            $('#parroquia').html(options).prop('disabled', false);
        },
        error: function(xhr) {
            console.error('Error cargando parroquias:', xhr.responseText);
            $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
        }
    });
}

// ==================== FUNCIONES DE ESPECIALIDADES ====================

function cargarListaEspecialidades() {
    $.ajax({
        url: APP_URL + '/api/ubicacion/especialidades',
        type: 'POST',
        dataType: 'json',
        success: function(especialidades) {
            console.log('Especialidades recibidas:', especialidades);
            var html = '';
            if (!especialidades || especialidades.length === 0) {
                html = '<div class="text-muted">No hay especialidades disponibles</div>';
            } else {
                for (var i = 0; i < especialidades.length; i++) {
                    var esp = especialidades[i];
                    var espNombre = typeof esp === 'string' ? esp : (esp.especialidad || esp.nombre || '');
                    var espId = 'esp_' + espNombre.replace(/\s/g, '_').replace(/[áéíóú]/g, function(match) {
                        return { 'á':'a', 'é':'e', 'í':'i', 'ó':'o', 'ú':'u' }[match] || match;
                    });
                    html += `
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input especialidad-check" value="${escapeHtml(espNombre)}" id="${espId}">
                            <label class="form-check-label" for="${espId}">${escapeHtml(espNombre)}</label>
                        </div>
                    `;
                }
            }
            $('#especialidades_container').html(html);
        },
        error: function(xhr) {
            console.error('Error cargando especialidades:', xhr.responseText);
            $('#especialidades_container').html('<div class="text-danger">Error al cargar especialidades</div>');
        }
    });
}

function obtenerEspecialidadesSeleccionadas() {
    var especialidades = [];
    $('.especialidad-check:checked').each(function() {
        especialidades.push($(this).val());
    });
    return especialidades;
}

// ==================== FUNCIONES DE VISTA PREVIA ====================

function actualizarPreview() {
    $('#preview_nombre').text($('#nombre').val() || 'Nombre del Consultorio');
    var ciudadNombre = $('#ciudad option:selected').text();
    if (ciudadNombre && ciudadNombre !== 'Seleccione una ciudad...') {
        $('#preview_ciudad').text(ciudadNombre);
    } else {
        $('#preview_ciudad').text('Ciudad no seleccionada');
    }
    $('#preview_descripcion').text($('#descripcion').val() || 'Sin descripción');
    $('#preview_telefono').text($('#telefono').val() || '-');
    $('#preview_email').text($('#email').val() || '-');
}

// ==================== FUNCIÓN PARA CREAR CONSULTORIO ====================

function crearConsultorio() {
    var especialidades = obtenerEspecialidadesSeleccionadas();
    
    var datos = {
        nombre: $('#nombre').val(),
        descripcion: $('#descripcion').val(),
        apertura: $('#apertura').val(),
        cierre: $('#cierre').val(),
        telefono: $('#telefono').val(),
        email: $('#email').val(),
        id_estado: $('#estado').val(),
        id_ciudad: $('#ciudad').val(),
        id_municipio: $('#municipio').val(),
        id_parroquia: $('#parroquia').val(),
        direccion: $('#direccion').val(),
        especialidades: especialidades,
        csrf_token: CSRF.getToken()
    };
    
    console.log('Datos a enviar:', datos);
    
    // Validaciones
    if (!datos.nombre) {
        mostrarError('El nombre del consultorio es requerido');
        return;
    }
    if (!datos.id_estado) {
        mostrarError('Debe seleccionar un estado');
        return;
    }
    if (!datos.id_ciudad) {
        mostrarError('Debe seleccionar una ciudad');
        return;
    }
    if (!datos.direccion) {
        mostrarError('La dirección detallada es requerida');
        return;
    }
    
    var $btn = $('#formCrearConsultorio button[type="submit"]');
    var originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/crear',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta crear consultorio:', response);
            if (response.resultado === 'creado') {
                $('#alertExito').show();
                setTimeout(function() {
                    window.location.href = APP_URL + '/consultorios';
                }, 2000);
            } else {
                mostrarError('Error al crear el consultorio: ' + (response.error || response.resultado));
            }
        },
        error: function(xhr) {
            console.error('Error crear consultorio:', xhr.responseText);
            mostrarError('Error de conexión: ' + xhr.status);
        },
        complete: function() {
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

function mostrarError(mensaje) {
    $('#errorMensaje').text(mensaje);
    $('#alertError').show();
    setTimeout(function() { $('#alertError').hide(); }, 4000);
}

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// ==================== INICIALIZACIÓN ====================

$(document).ready(function() {
    console.log('=== INICIALIZANDO FORMULARIO DE CREACIÓN ===');
    
    // Cargar estados al iniciar
    cargarEstados();
    
    // Cargar especialidades
    cargarListaEspecialidades();
    
    // Evento change del select de estado
    $(document).on('change', '#estado', function() {
        var id_estado = $(this).val();
        console.log('Estado seleccionado ID:', id_estado);
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipiosPorEstado(id_estado);
        } else {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    // Evento change del select de municipio
    $(document).on('change', '#municipio', function() {
        var id_municipio = $(this).val();
        console.log('Municipio seleccionado ID:', id_municipio);
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    // Evento change del select de ciudad (para vista previa)
    $(document).on('change', '#ciudad', function() {
        actualizarPreview();
    });
    
    // Eventos para actualizar vista previa
    $('#nombre, #descripcion, #telefono, #email').on('input', function() {
        actualizarPreview();
    });
    
    // Enviar formulario
    $('#formCrearConsultorio').submit(function(e) {
        e.preventDefault();
        crearConsultorio();
    });
    
    console.log('Inicialización completada');
});
</script>

</body>
</html>