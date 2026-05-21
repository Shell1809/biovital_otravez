<?php
// NO iniciar sesión aquí - el Front Controller ya lo hace
if($_SESSION['us_tipo'] != 2 || $_SESSION['rol'] != 'medico'){
    header('Location: ' . APP_URL . '/login/medico');
    exit();
}

$id_medico = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_us'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ==================== VARIABLES GLOBALES ==================== -->
    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
        console.log('APP_URL definida:', APP_URL);
    </script>

    <!-- jQuery PRIMERO -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Configuración del sistema -->
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    
    <!-- CSRF Protection -->
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <!-- SCRIPT DE UBICACIÓN (IMPORTANTE - el mismo que usa pac_editar_datos) -->
    <script src="<?php echo APP_URL; ?>/js/ubicacion.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <title>Médico | Editar datos</title>
    <style>
        .select-group { margin-bottom: 15px; }
        .ubicacion-label { font-weight: 600; color: #0b7300; margin-bottom: 5px; display: block; }
        .help-text { font-size: 12px; color: #6c757d; margin-top: 5px; }
        .csrf-info { font-size: 12px; color: #6c757d; margin-top: 10px; text-align: center; }
        .required-field { color: #dc3545; }
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
    <a href="<?php echo APP_URL; ?>/panel/medico" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">BIOVITAL</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img id="avatar4" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($nombre_usuario); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-header">Usuario</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link active">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header">Clínica</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Recetas</p>
                    </a>
                </li>
                <li class="nav-header">Pacientes</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/medico/pacientes" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Mis Pacientes</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="cambiocontra" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar contraseña</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <img id="avatar3" src="<?php echo APP_URL; ?>/img/avatar.png" class="profile-user-img img-fluid img-circle">
          <b><?php echo htmlspecialchars($nombre_usuario); ?></b>
        </div>
        <div class="alert alert-success text-center" id="update" style="display:none;">Contraseña actualizada correctamente</div>
        <div class="alert alert-danger text-center" id="noupdate" style="display:none;">Contraseña actual incorrecta</div>
        <form id="form-pass" method="POST">
          <?php echo Security::campoCSRF(); ?>
          <input id="oldpass" type="password" class="form-control mb-2" placeholder="Contraseña actual" required>
          <input id="newpass" type="password" class="form-control" placeholder="Contraseña nueva" required>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Cambiar Avatar -->
<div class="modal fade" id="cambiophoto" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar avatar</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <img id="avatar3" src="<?php echo APP_URL; ?>/img/avatarDES.jpg" class="profile-user-img img-fluid img-circle">
          <b><?php echo htmlspecialchars($nombre_usuario); ?></b>
        </div>
        <div class="alert alert-success text-center" id="edit" style="display:none;">Avatar actualizado correctamente</div>
        <div class="alert alert-danger text-center" id="noedit" style="display:none;">Formato no admitido</div>
        <form id="form-photo" method="POST" enctype="multipart/form-data">
          <?php echo Security::campoCSRF(); ?>
          <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif" required>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Datos personales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Home</a></li>
                        <li class="breadcrumb-item active">Datos personales</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- COLUMNA IZQUIERDA - PERFIL -->
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img id="avatar2" src="<?php echo APP_URL; ?>/img/avatarDES.jpg" class="profile-user-img img-fluid img-circle">
                                </div>
                                <div class='text-center mt-1'>
                                    <button type='button' data-toggle="modal" data-target="#cambiophoto" class='btn btn-primary btn-sm'>Cambiar avatar</button>
                                </div>
                                <input id="id_usuario" type="hidden" value="<?php echo htmlspecialchars($id_medico); ?>">
                                <h3 id="nombre_us" class="profile-username text-center text-success">Cargando...</h3>
                                <p id="apellidos_us" class="text-muted text-center">Cargando...</p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Edad</b>
                                        <a id="edad" class="float-right">-</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Cédula</b>
                                        <a id="cedula_us" class="float-right">-</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Tipo Usuario</b>
                                        <span id="us_tipo" class="float-right badge badge-primary">Médico</span>
                                    </li>
                                    <button data-toggle="modal" data-target="#cambiocontra" type="button" class="btn btn-block btn-outline-warning btn-sm">Cambiar contraseña</button>
                                </ul>
                            </div>
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Sobre mi</h3>
                            </div>
                            <div class="card-body">
                                <strong><i class="fas fa-phone mr-1"></i>Teléfono</strong>
                                <p id="telefono_us" class="text-muted">-</p>
                                <strong><i class="fas fa-map-marker-alt mr-1"></i>Dirección</strong>
                                <p id="direccion_us" class="text-muted">-</p>
                                <strong><i class="fas fa-at mr-1"></i>Correo</strong>
                                <p id="correo_us" class="text-muted">-</p>
                                <strong><i class="fas fa-smile-wink mr-1"></i>Sexo</strong>
                                <p id="sexo_us" class="text-muted">-</p>
                                <strong><i class="fas fa-pencil-alt mr-1"></i>Información adicional</strong>
                                <p id="adicional_us" class="text-muted">-</p>
                                <button class="edit btn btn-block bg-gradient-danger">Editar</button>
                            </div>
                            <div class="card-footer">
                                <p class="text-muted">Click en el botón si desea editar</p>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA - FORMULARIO DE EDICIÓN -->
                    <div class="col-md-9">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Editar datos personales</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success text-center" id="editado" style="display:none;">
                                    <span><i class="fas fa-check m-1"></i>Editado</span>
                                </div>
                                <div class="alert alert-danger text-center" id="noeditado" style="display:none;">
                                    <span><i class="fas fa-times m-1"></i>Edición deshabilitada</span>
                                </div>
                                <form id="form-usuario" class="form-horizontal" method="POST">
                                    <?php echo Security::campoCSRF(); ?>
                                    
                                    <div class="form-group row">
                                        <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                                        <div class="col-sm-10">
                                            <input type="tel" id="telefono" class="form-control" placeholder="Ej: 04141234567" disabled>
                                        </div>
                                    </div>
                                    
                                    <!-- ==================== SISTEMA DE UBICACIÓN (IGUAL QUE pac_editar_datos) ==================== -->
                                    <h4 class="mt-4"><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="estado">Estado</label>
                                                <select class="form-control" id="estado" name="estado" disabled>
                                                    <option value="">Seleccione un estado...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ciudad">Ciudad</label>
                                                <select class="form-control" id="ciudad" name="ciudad" disabled>
                                                    <option value="">Seleccione un estado primero...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="municipio">Municipio</label>
                                                <select class="form-control" id="municipio" name="municipio" disabled>
                                                    <option value="">Seleccione un estado primero...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="parroquia">Parroquia</label>
                                                <select class="form-control" id="parroquia" name="parroquia" disabled>
                                                    <option value="">Seleccione un municipio primero...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="direccion_detallada">Dirección Detallada</label>
                                        <input type="text" class="form-control" id="direccion_detallada" name="direccion_detallada" placeholder="Av. Principal, Edificio, Número, etc." disabled>
                                        <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Casa #123</small>
                                    </div>

                                    <!-- Campo oculto para almacenar la dirección completa -->
                                    <input type="hidden" id="direccion" name="direccion">
                                    <!-- ==================== FIN SISTEMA DE UBICACIÓN ==================== -->
                                    
                                    <div class="form-group row">
                                        <label for="correo" class="col-sm-2 col-form-label">Correo</label>
                                        <div class="col-sm-10">
                                            <input type="email" id="correo" class="form-control" placeholder="ejemplo@correo.com" disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="sexo" class="col-sm-2 col-form-label">Sexo</label>
                                        <div class="col-sm-10">
                                            <select id="sexo" class="form-control" disabled>
                                                <option value="">Seleccione...</option>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="adicional" class="col-sm-2 col-form-label">Información adicional</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="adicional" rows="5" placeholder="Información adicional sobre el médico..." disabled></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10 float-right">
                                            <button type="submit" class="btn btn-block btn-outline-success" disabled>Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <div class="csrf-info">
                                    <i class="fas fa-shield-alt"></i> Todos los cambios están protegidos contra falsificación de solicitudes (CSRF)
                                </div>
                                <p class="text-muted mt-2">Cuidado con ingresar datos erróneos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>/js/medico.js"></script>

<script>
// ==================== FUNCIÓN PARA CARGAR DIRECCIÓN EXISTENTE (IGUAL QUE EN pac_editar_datos) ====================
function cargarDireccionExistente(direccion_completa) {
    console.log('Parseando dirección existente:', direccion_completa);
    
    if (!direccion_completa || direccion_completa === '-') {
        console.log('No hay dirección guardada');
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

// ==================== MODIFICAR medico.js PARA INCLUIR CARGA DE DIRECCIÓN ====================
// Esperar a que medico.js se cargue y luego agregar la funcionalidad de dirección
$(document).ready(function() {
    // Modificar la función buscar_medico para que después de cargar los datos, procese la dirección
    var originalBuscarMedico = window.buscar_medico;
    if (typeof window.buscar_medico === 'function') {
        window.buscar_medico = function(dato) {
            originalBuscarMedico(dato);
            // Después de cargar los datos, procesar dirección
            setTimeout(function() {
                var direccion = $('#direccion_us').text();
                if (direccion && direccion !== '-' && direccion !== 'No disponible') {
                    cargarDireccionExistente(direccion);
                }
            }, 500);
        };
    }
    
    // También modificar el evento de edición para habilitar los selects de ubicación
    $(document).on('click', '.edit', function() {
        setTimeout(function() {
            $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
        }, 100);
    });
    
    // Al guardar, construir la dirección completa
    $('#form-usuario').on('submit', function(e) {
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
        
        $('#direccion').val(direccion_completa);
        console.log('Dirección completa a guardar:', direccion_completa);
    });
});
</script>

</body>
</html>