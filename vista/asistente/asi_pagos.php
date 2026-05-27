<?php
// vista/asistente/asi_pagos.php
// Sistema de visualización, confirmación de pagos y emisión de facturas
// Diseñado para integrarse dentro del layout base dashboard.php

// Verificar autenticación y rol de asistente (Rol 3)
if($_SESSION['us_tipo'] != 3 || $_SESSION['rol'] != 'asistente'){
    header('Location: ' . APP_URL . '/login/asistente');
    exit();
}

$id_asistente = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_us'];

// Incluir Security usando la constante MODEL_PATH
$securityPath = MODEL_PATH . '/Security.php';
if (!file_exists($securityPath)) {
    die("Error: No se encuentra Security.php en: " . $securityPath);
}
include_once $securityPath;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
        var ID_ASISTENTE = <?php echo json_encode($id_asistente); ?>;
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <title>Asistente | Control de Pagos</title>
    
    <style>
        .payments-header {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .payments-header::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        .table-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .table-card .card-header {
            background: white;
            border-bottom: 2px solid #9333ea;
            padding: 1rem 1.5rem;
        }
        .table-card .card-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }
        .btn-confirmar {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: 0.4rem 1rem;
            transition: all 0.3s;
        }
        .btn-confirmar:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16,185,129,0.2);
            color: white;
        }
        .btn-factura {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: 0.4rem 1rem;
            transition: all 0.3s;
        }
        .btn-factura:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59,130,246,0.2);
            color: white;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-pendiente {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-completado {
            background-color: #d1fae5;
            color: #065f46;
        }
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1rem;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

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

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo APP_URL; ?>/panel/asistente" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">BioVital</span>
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
                <li class="nav-header"><i class="fas fa-user-nurse"></i> Usuario</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header"><i class="fas fa-clinic-medical"></i> Clínica</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Recetas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/pagos" class="nav-link active">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Control de Pagos</p>
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
                    <h1><i class="fas fa-wallet"></i> Gestión de Pagos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/asistente">Home</a></li>
                        <li class="breadcrumb-item active">Pagos</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="alert alert-success alert-custom" id="alerta-exito" style="display:none;">
                <i class="fas fa-check-circle"></i> <span id="texto-exito">Operación procesada con éxito.</span>
            </div>
            <div class="alert alert-danger alert-custom" id="alerta-error" style="display:none;">
                <i class="fas fa-exclamation-circle"></i> <span id="texto-error">Ocurrió un inconveniente.</span>
            </div>

            <div class="payments-header text-white">
                <div class="row">
                    <div class="col-md-8">
                        <h2>Historial General de Transacciones</h2>
                        <p class="mb-0">Como asistente, puedes verificar los reportes financieros de los pacientes, validar transferencias bancarias / efectivo, cambiar estatus y generar la factura digital correspondiente.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-card card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h3><i class="fas fa-file-invoice-dollar"></i> Lista de Transacciones Registradas</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="tabla-pagos" class="table table-hover table-striped width-100">
                                <thead>
                                    <tr>
                                        <th>ID Pago</th>
                                        <th>Paciente</th>
                                        <th>Cédula</th>
                                        <th>Método</th>
                                        <th>Monto</th>
                                        <th>Fecha Pago</th>
                                        <th>Estado</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="contenedor-pagos">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<div id="csrf-token-holder">
    <?php echo Security::campoCSRF(); ?>
</div>

<footer class="main-footer">
    <div class="float-right d-none d-sm-block"><b>Version</b> 1.0.0</div>
    <strong>Copyright &copy; 2026 BioVital.</strong> Todos los derechos reservados.
</footer>

</div>

<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    console.log('=== INICIANDO GESTIÓN DE PAGOS ===');
    
    // Inicializar y Cargar Datos mediante el Controlador de Pagos
    listarPagos();

    function listarPagos() {
        // Validación del formato DataTable si ya existiera una instancia previa
        if ($.fn.DataTable.isDataTable('#tabla-pagos')) {
            $('#tabla-pagos').DataTable().destroy();
        }

        $.ajax({
            url: APP_URL + '/api/pagos/listar',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de pagos:', response);
                let html = '';
                let pagos = response.data || response;

                if(pagos.length === 0) {
                    html = '<tr><td colspan="8" class="text-center">No hay registros de pagos disponibles</td></tr>';
                } else {
                    pagos.forEach(pago => {
                        // Construcción de insignias de estado dinámicas
                        let badgeClass = pago.status === 'pendiente' ? 'status-pendiente' : 'status-completado';
                        
                        // Validar si el botón de confirmación debe mostrarse deshabilitado o activo
                        let disableConfirmar = pago.status === 'completado' ? 'disabled' : '';
                        
                        html += `
                            <tr>
                                <td><strong>#${pago.id_pago}</strong></td>
                                <td>${pago.nombre_paciente} ${pago.apellido_paciente}</td>
                                <td>${pago.cedula_paciente}</td>
                                <td><i class="fas fa-credit-card text-muted"></i> ${pago.metodo_pago}</td>
                                <td><strong>$${parseFloat(pago.monto).toFixed(2)}</strong></td>
                                <td>${pago.fecha_pago}</td>
                                <td><span class="status-badge ${badgeClass}">${pago.status.toUpperCase()}</span></td>
                                <td class="text-right">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-confirmar btn-sm mr-2 btn-accion-confirmar" data-id="${pago.id_pago}" ${disableConfirmar}>
                                            <i class="fas fa-check-double"></i> Confirmar Pago
                                        </button>
                                        <button class="btn btn-factura btn-sm btn-accion-factura" data-id="${pago.id_pago}">
                                            <i class="fas fa-file-pdf"></i> Emitir Factura
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#contenedor-pagos').html(html);
                
                // Inicialización de DataTables en español para ordenamiento y búsquedas nativas
                $('#tabla-pagos').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                    },
                    "order": [[ 0, "desc" ]]
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al recuperar listado de pagos:', error);
                $('#contenedor-pagos').html('<tr><td colspan="8" class="text-center text-danger">Error de comunicación con el servidor.</td></tr>');
            }
        });
    }

    // ==================== ACCIÓN: CONFIRMAR PAGO ====================
    $(document).on('click', '.btn-accion-confirmar', function() {
        let idPago = $(this).data('id');
        let tokenCsrf = $('input[name="token_csrf"]').val(); // Captura automática del token de seguridad

        if (confirm(`¿Estás seguro de que deseas confirmar y aprobar el pago #${idPago}?`)) {
            $.ajax({
                url: APP_URL + '/api/pagos/confirmar',
                type: 'POST',
                data: {
                    id_pago: idPago,
                    token_csrf: tokenCsrf
                },
                dataType: 'json',
                success: function(response) {
                    if(
