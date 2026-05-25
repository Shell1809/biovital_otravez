<?php
if($_SESSION['us_tipo'] != 3 || $_SESSION['rol'] != 'asistente'){
    header('Location: ' . APP_URL . '/login/asistente');
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario');
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
    <title>Asistente | Panel</title>
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
        <a href="<?php echo APP_URL; ?>/panel/asistente" class="brand-link">
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
                    <li class="nav-header">Usuario</li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                            <i class="nav-icon fas fa-user-cog"></i><p>Datos personales</p>
                        </a>
                    </li>
                    <li class="nav-header">Clínica</li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                            <i class="nav-icon fas fa-prescription-bottle-alt"></i><p>Recetas</p>
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
                    <div class="col-sm-6"><h1>Panel de Asistencia</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/asistente">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                <!-- Welcome Banner -->
                <div class="bv-welcome-banner asistente bv-animate">
                    <h2>Bienvenido, <?php echo $nombre_usuario; ?></h2>
                    <p>Gestión de consultorios y preparación de pacientes.</p>
                    <div class="bv-role-tag"><i class="fas fa-clipboard-list"></i> Asistente</div>
                </div>

                <!-- Quick Access Cards -->
                <div class="bv-dash-grid">
                    <a href="<?php echo APP_URL; ?>/recetas" class="bv-dash-card asistente bv-animate bv-animate-delay-1">
                        <div class="bv-card-icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                        <h3>Recetas</h3>
                        <p>Gestiona y visualiza las recetas médicas del sistema.</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/documentos" class="bv-dash-card asistente bv-animate bv-animate-delay-2">
                        <div class="bv-card-icon"><i class="fas fa-file-medical"></i></div>
                        <h3>Documentos médicos</h3>
                        <p>Visualiza recipes, constancias, justificativos, diagnósticos y laboratorios para imprimir o guardar en PDF.</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/perfil" class="bv-dash-card asistente bv-animate bv-animate-delay-3">
                        <div class="bv-card-icon"><i class="fas fa-user-cog"></i></div>
                        <h3>Mi Perfil</h3>
                        <p>Actualiza tu información personal y datos de contacto.</p>
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
</body>
</html>
