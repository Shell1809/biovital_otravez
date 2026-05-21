<?php
// NO iniciar sesión aquí - el Front Controller ya lo hace
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}

include_once dirname(__DIR__) . '/modelo/Security.php';
$nombre_usuario = $_SESSION['nombre_us'] ?? 'Administrador';
$id_consultorio = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <title>Administrador | Editar Consultorio</title>
    
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
            border-left: 4px solid #ffc107;
        }
        .csrf-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            text-align: center;
        }
        .required-field::after {
            content: " *";
            color: red;
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
                    <h1><i class="fas fa-edit"></i> Editar Consultorio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/consultorios">Consultorios</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/consultorios/detalle?id=<?php echo $id_consultorio; ?>">Detalle</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <input type="hidden" id="id_consultorio" value="<?php echo $id_consultorio; ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Editar Información</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success" id="alertExito" style="display:none;">
                                <i class="fas fa-check-circle"></i> Consultorio actualizado exitosamente
                            </div>
                            <div class="alert alert-danger" id="alertError" style="display:none;">
                                <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                            </div>
                            
                            <form id="formEditarConsultorio" method="POST">
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
                                            <input type="time" class="form-control" id="apertura" name="apertura" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">Cierre Habitual</label>
                                            <input type="time" class="form-control" id="cierre" name="cierre" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Especialidades Admitidas</label>
                                    <div class="checkbox-group" id="especialidades_container">
                                        <div class="text-center">Cargando especialidades...</div>
                                    </div>
                                    <small class="form-text text-muted">Seleccione las especialidades que se atienden en este consultorio</small>
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
                                                <option value="">Primero seleccione una ciudad...</option>
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
                                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej: 0212-5551234">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email del Consultorio</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Ej: consultorio@correo.com">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-warning btn-lg btn-block">
                                    <i class="fas fa-save"></i> Actualizar Consultorio
                                </button>
                                <a href="<?php echo APP_URL; ?>/consultorios/detalle?id=<?php echo $id_consultorio; ?>" class="btn btn-secondary btn-block mt-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                
                                <div class="csrf-info mt-3">
                                    <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF - Todos los cambios son seguros
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-outline card-warning">
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
<script src="<?php echo APP_URL; ?>/js/consultorio.js"></script>

</body>
</html>