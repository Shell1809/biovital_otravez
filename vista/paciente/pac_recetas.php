<?php

if($_SESSION['us_tipo'] != 1 || $_SESSION['rol'] != 'paciente'){
    header('Location: ' . APP_URL . '/login/paciente');
    exit();
}

// Usar rutas absolutas con dirname()
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
    
    <title>Paciente | Mis Recetas</title>
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
    <a href="<?php echo APP_URL; ?>/panel/paciente" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">BIOVITAL</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img id="avatar4" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-header">Usuario</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header">Clínica</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/paciente/recetas" class="nav-link">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Mis Recetas</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-prescription-bottle-alt"></i> Mis Recetas Médicas</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Recetas</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="buscar_receta" class="form-control float-right" placeholder="Buscar...">
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Medicamento</th>
                                        <th>Marca</th>
                                        <th>Cantidad</th>
                                        <th>Dosis</th>
                                        <th>Médico</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_recetas">
                                    <tr><td colspan="7" class="text-center">Cargando recetas...</td</tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    listar_recetas();

    $('#buscar_receta').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#tabla_recetas tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    function listar_recetas() {
        $('#tabla_recetas').html('<tr><td colspan="7" class="text-center">Cargando recetas...<div class="spinner-border spinner-border-sm ml-2"></div></td></tr>');
        
        $.ajax({
            url: APP_URL + '/api/recetas/mis-recetas',
            type: 'POST',
            data: { id_paciente: <?php echo $_SESSION['usuario']; ?> },
            dataType: 'json',
            success: function(recetas) {
                let html = '';
                if (!recetas || recetas.length === 0) {
                    html = '<tr><td colspan="7" class="text-center">No hay recetas registradas</td></tr>';
                } else {
                    for (let receta of recetas) {
                        html += `
                            <tr>
                                <td>${receta.id_receta}</td>
                                <td><strong>${escapeHtml(receta.nombre_medicamento)}</strong></td>
                                <td>${escapeHtml(receta.marca)}</td>
                                <td>${escapeHtml(receta.cantidad)}</td>
                                <td>${escapeHtml(receta.dosis || '-')}</td>
                                <td>${escapeHtml(receta.medico || 'N/A')}</td>
                                <td>${receta.fecha_receta}</td>
                            </tr>
                        `;
                    }
                }
                $('#tabla_recetas').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#tabla_recetas').html('<tr><td colspan="7" class="text-center text-danger">Error al cargar recetas</td></tr>');
            }
        });
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
});
</script>

<?php
include_once dirname(__DIR__) . '/layouts/footer.php';
?>