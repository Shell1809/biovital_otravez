<?php
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['nombre_us'] ?? 'Administrador');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>var APP_URL = '<?php echo APP_URL; ?>';</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Administrador | Panel</title>
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
            <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </ul>
    </nav>

    <!-- Main Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?php echo APP_URL; ?>/panel/administrador" class="brand-link">
            <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity:.8">
            <span class="brand-text font-weight-light">BIOVITAL</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img id="avatar4" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo $nombre_usuario; ?></a>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-header"><i class="fas fa-user-shield"></i> Usuario</li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                            <i class="nav-icon fas fa-user-cog"></i><p>Datos personales</p>
                        </a>
                    </li>
                    <li class="nav-header"><i class="fas fa-chart-line"></i> Gestión</li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/administrador/usuarios" class="nav-link">
                            <i class="nav-icon fas fa-users"></i><p>Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-header"><i class="fas fa-hospital-user"></i> Clínica</li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/consultorios" class="nav-link">
                            <i class="nav-icon fas fa-building"></i><p>Consultorios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                            <i class="nav-icon fas fa-prescription-bottle-alt"></i><p>Recetas</p>
                        </a>
                    </li>
                    <li class="nav-header"><i class="fas fa-chart-bar"></i> Reportes</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i><p>Estadísticas</p>
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
                    <div class="col-sm-6"><h1>Panel de Administración</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                <!-- Welcome Banner -->
                <div class="bv-welcome-banner admin bv-animate">
                    <h2>Bienvenido, <?php echo $nombre_usuario; ?></h2>
                    <p>Supervisa métricas operativas globales y gestiona el sistema.</p>
                    <div class="bv-role-tag"><i class="fas fa-sliders-h"></i> Administrador</div>
                </div>

                <!-- Info Boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box bv-animate bv-animate-delay-1">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Usuarios</span>
                                <span class="info-box-number" id="total_usuarios">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box bv-animate bv-animate-delay-1">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-prescription-bottle-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Recetas</span>
                                <span class="info-box-number" id="total_recetas">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box bv-animate bv-animate-delay-2">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-md"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Médicos</span>
                                <span class="info-box-number" id="total_medicos">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box bv-animate bv-animate-delay-2">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-friends"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pacientes</span>
                                <span class="info-box-number" id="total_pacientes">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Access Cards -->
                <div class="bv-dash-grid">
                    <a href="<?php echo APP_URL; ?>/administrador/usuarios" class="bv-dash-card admin bv-animate bv-animate-delay-1">
                        <div class="bv-card-icon"><i class="fas fa-users-cog"></i></div>
                        <h3>Gestión de Usuarios</h3>
                        <p>Administra cuentas de pacientes, médicos y asistentes.</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/consultorios" class="bv-dash-card admin bv-animate bv-animate-delay-2">
                        <div class="bv-card-icon"><i class="fas fa-building"></i></div>
                        <h3>Consultorios</h3>
                        <p>Gestiona sedes, horarios y asignación de espacios.</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/recetas" class="bv-dash-card admin bv-animate bv-animate-delay-3">
                        <div class="bv-card-icon"><i class="fas fa-prescription"></i></div>
                        <h3>Recetas Médicas</h3>
                        <p>Supervisa y audita todas las recetas del sistema.</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/perfil" class="bv-dash-card admin bv-animate bv-animate-delay-3">
                        <div class="bv-card-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Mi Perfil</h3>
                        <p>Actualiza tus datos de administrador y seguridad.</p>
                    </a>
                </div>

            </div>
        </section>
    </div>

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block"><b>Version</b> 1.0.0</div>
        <strong>Copyright © 2026 BioVital.</strong> Todos los derechos reservados.
    </footer>

</div>

<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#total_usuarios').text('4');
    $('#total_recetas').text('8');
    $('#total_medicos').text('1');
    $('#total_pacientes').text('2');
});
</script>
</body>
</html>
