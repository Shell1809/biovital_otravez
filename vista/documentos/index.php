<?php
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['paciente', 'asistente'])) {
    header('Location: ' . APP_URL . '/login');
    exit();
}

$esPaciente = $_SESSION['rol'] === 'paciente';
$titulo = $esPaciente ? 'Mis Documentos Médicos' : 'Documentos Médicos de Pacientes';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BioVital | <?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        :root {
            --bv-primary: #0077b6;
            --bv-accent: #4361ee;
            --bv-bg: #FDFBF7;
        }
        body {
            background: var(--bv-bg);
        }
        .document-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .document-card .card-header {
            background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
            color: #fff;
        }
        .doc-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: .6rem;
        }
        .doc-actions .btn {
            border-radius: 999px;
            font-weight: 600;
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
            <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">Cerrar sesión</a>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?php echo APP_URL; ?>/panel/<?php echo $_SESSION['rol']; ?>" class="brand-link">
            <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">BIOVITAL</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
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
                            <i class="nav-icon fas fa-user-cog"></i><p>Datos personales</p>
                        </a>
                    </li>
                    <li class="nav-header">Clínica</li>
                    <li class="nav-item">
                        <a href="<?php echo $esPaciente ? APP_URL . '/paciente/recetas' : APP_URL . '/recetas'; ?>" class="nav-link">
                            <i class="nav-icon fas fa-prescription-bottle-alt"></i><p><?php echo $esPaciente ? 'Mis Recetas' : 'Recetas'; ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/documentos" class="nav-link active">
                            <i class="nav-icon fas fa-file-medical"></i><p>Documentos médicos</p>
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
                    <div class="col-sm-8">
                        <h1><i class="fas fa-file-medical"></i> <?php echo $titulo; ?></h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <?php if (empty($documentos)): ?>
                    <div class="alert alert-info">No hay documentos disponibles para visualizar.</div>
                <?php else: ?>
                    <?php foreach ($documentos as $doc): ?>
                        <div class="card document-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-notes-medical"></i>
                                    Receta #<?php echo (int)$doc->id_receta; ?> - <?php echo htmlspecialchars($doc->nombre_paciente ?? 'Paciente'); ?>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3"><strong>Fecha:</strong> <?php echo htmlspecialchars($doc->fecha_receta ?? ''); ?></div>
                                    <div class="col-md-3"><strong>Médico:</strong> <?php echo htmlspecialchars($doc->nombre_medico ?? 'N/A'); ?></div>
                                    <div class="col-md-3"><strong>Medicamento:</strong> <?php echo htmlspecialchars($doc->nombre_medicamento ?? ''); ?></div>
                                    <div class="col-md-3"><strong>Cédula:</strong> <?php echo htmlspecialchars($doc->cedula_paciente ?? 'N/A'); ?></div>
                                </div>
                                <div class="doc-actions">
                                    <a target="_blank" class="btn btn-primary" href="<?php echo APP_URL; ?>/documentos/ver/recipes/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-prescription"></i> Recipe / Tratamiento</a>
                                    <a target="_blank" class="btn btn-info" href="<?php echo APP_URL; ?>/documentos/ver/indicaciones/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-list-check"></i> Indicaciones</a>
                                    <a target="_blank" class="btn btn-warning" href="<?php echo APP_URL; ?>/documentos/ver/justificativos/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-file-signature"></i> Justificativo</a>
                                    <a target="_blank" class="btn btn-secondary" href="<?php echo APP_URL; ?>/documentos/ver/constancia-estudio/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-graduation-cap"></i> Constancia estudio</a>
                                    <a target="_blank" class="btn btn-dark" href="<?php echo APP_URL; ?>/documentos/ver/constancia-trabajo/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-briefcase"></i> Constancia trabajo</a>
                                    <a target="_blank" class="btn btn-success" href="<?php echo APP_URL; ?>/documentos/ver/diagnostico/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-stethoscope"></i> Diagnóstico</a>
                                    <a target="_blank" class="btn btn-danger" href="<?php echo APP_URL; ?>/documentos/ver/laboratorio/<?php echo (int)$doc->id_receta; ?>"><i class="fas fa-flask"></i> Laboratorio</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
</body>
</html>
