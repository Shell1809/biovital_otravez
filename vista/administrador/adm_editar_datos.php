<?php
// NO session_start() - el Front Controller ya lo hace
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}

$id_administrador = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_us'];
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
    
    <title>Administrador | Editar datos</title>
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
          <input type="hidden" name="csrf_token" id="csrf_token_pass" value="">
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
          <input type="hidden" name="csrf_token" id="csrf_token_photo" value="">
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
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
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
                                <input id="id_usuario" type="hidden" value="<?php echo htmlspecialchars($id_administrador); ?>">
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
                                        <span id="us_tipo" class="float-right badge badge-primary">Administrador</span>
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
                                    <input type="hidden" name="csrf_token" id="csrf_token_form" value="">
                                    
                                    <div class="form-group row">
                                        <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                                        <div class="col-sm-10">
                                            <input type="tel" id="telefono" class="form-control" placeholder="Ej: 04141234567" disabled>
                                        </div>
                                    </div>
                                    
                                    <!-- ==================== SISTEMA DE UBICACIÓN ==================== -->
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
                                            <textarea class="form-control" id="adicional" rows="5" placeholder="Información adicional sobre el administrador..." disabled></textarea>
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
<script src="<?php echo APP_URL; ?>/js/administrador.js"></script>
<script src="<?php echo APP_URL; ?>/js/ubicacion.js"></script>

</body>
</html>