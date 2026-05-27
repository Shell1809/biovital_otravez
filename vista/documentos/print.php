<?php
$labels = [
    'recipes' => 'Recipe médico / Tratamiento',
    'indicaciones' => 'Indicaciones de consumo o aplicación',
    'justificativos' => 'Justificativo médico',
    'constancia-estudio' => 'Constancia médica para estudio',
    'constancia-trabajo' => 'Constancia médica para trabajo',
    'diagnostico' => 'Diagnóstico médico',
    'laboratorio' => 'Estudios de laboratorio'
];

$titulo = $labels[$tipo] ?? 'Documento médico';
$paciente = htmlspecialchars($documento->nombre_paciente ?? 'Paciente');
$medico = htmlspecialchars($documento->nombre_medico ?? 'Médico tratante');
$fecha = htmlspecialchars($documento->fecha_receta ?? date('Y-m-d'));
$cedula = htmlspecialchars($documento->cedula_paciente ?? 'N/A');
$sexo = htmlspecialchars($documento->sexo_paciente ?? 'N/A');
$edad = 'N/A';

if (!empty($documento->fecha_nacimiento_pac)) {
    try {
        $edad = (new DateTime($documento->fecha_nacimiento_pac))->diff(new DateTime())->y . ' años';
    } catch (Exception $e) {
        $edad = 'N/A';
    }
}

function docText($value, $fallback = 'No registrado') {
    return nl2br(htmlspecialchars(trim((string)$value) !== '' ? $value : $fallback));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($titulo); ?> | BioVital</title>
    <style>
        :root {
            --primary: #0077b6;
            --accent: #4361ee;
            --text: #1f2937;
            --muted: #64748b;
            --paper: #ffffff;
            --bg: #FDFBF7;
            --border: #dbe5ef;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.55;
        }
        .toolbar {
            max-width: 900px;
            margin: 24px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .toolbar button {
            border: 0;
            border-radius: 999px;
            padding: 10px 18px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .sheet {
            width: 900px;
            min-height: 1120px;
            margin: 18px auto 40px;
            padding: 46px 54px;
            background: var(--paper);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
            position: relative;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 18px;
            margin-bottom: 28px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-badge {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
        }
        .brand h1 {
            margin: 0;
            color: var(--primary);
            font-size: 30px;
        }
        .brand p,
        .meta p {
            margin: 2px 0;
            color: var(--muted);
            font-size: 13px;
        }
        .meta {
            text-align: right;
        }
        .title {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 22px 0;
            color: var(--primary);
            font-size: 24px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 24px;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 26px;
            background: #fbfdff;
        }
        .info-item strong {
            display: block;
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
        }
        .section {
            margin-bottom: 24px;
        }
        .section h3 {
            margin: 0 0 10px;
            color: var(--primary);
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
            font-size: 17px;
        }
        .box {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
            min-height: 90px;
            background: #fff;
        }
        .statement {
            font-size: 17px;
            text-align: justify;
        }
        .signature {
            margin-top: 90px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            width: 300px;
            text-align: center;
            border-top: 1px solid var(--text);
            padding-top: 10px;
        }
        .footer {
            position: absolute;
            left: 54px;
            right: 54px;
            bottom: 32px;
            border-top: 1px solid var(--border);
            padding-top: 12px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }
        @media print {
            body {
                background: #fff;
            }
            .toolbar {
                display: none;
            }
            .sheet {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 28px 36px;
                border-radius: 0;
                box-shadow: none;
            }
            .footer {
                position: fixed;
                bottom: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
        <button onclick="window.close()">Cerrar</button>
    </div>

    <main class="sheet">
        <header class="header">
            <div class="brand">
                <div class="brand-badge">B</div>
                <div>
                    <h1>BioVital</h1>
                    <p>Consultoría médica inteligente</p>
                    <p>Documento generado para visualización, impresión o PDF</p>
                </div>
            </div>
            <div class="meta">
                <p><strong>Nro. referencia:</strong> <?php echo (int)$documento->id_receta; ?></p>
                <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>
            </div>
        </header>

        <h2 class="title"><?php echo htmlspecialchars($titulo); ?></h2>

        <section class="info-grid">
            <div class="info-item"><strong>Paciente</strong><?php echo $paciente; ?></div>
            <div class="info-item"><strong>Cédula</strong><?php echo $cedula; ?></div>
            <div class="info-item"><strong>Edad</strong><?php echo htmlspecialchars($edad); ?></div>
            <div class="info-item"><strong>Sexo</strong><?php echo $sexo; ?></div>
            <div class="info-item"><strong>Médico tratante</strong><?php echo $medico; ?></div>
            <div class="info-item"><strong>Fecha de emisión</strong><?php echo $fecha; ?></div>
        </section>

        <?php if ($tipo === 'recipes'): ?>
            <section class="section">
                <h3>Tratamiento indicado</h3>
                <div class="box">
                    <strong>Medicamento:</strong> <?php echo docText($documento->nombre_medicamento); ?><br>
                    <strong>Marca:</strong> <?php echo docText($documento->marca); ?><br>
                    <strong>Cantidad:</strong> <?php echo docText($documento->cantidad); ?><br>
                    <strong>Dosis:</strong> <?php echo docText($documento->dosis); ?>
                </div>
            </section>
            <section class="section">
                <h3>Tratamiento sugerido</h3>
                <div class="box"><?php echo docText($documento->trat_sugerido, 'Seguir las indicaciones establecidas por el médico tratante.'); ?></div>
            </section>
        <?php elseif ($tipo === 'indicaciones'): ?>
            <section class="section">
                <h3>Indicaciones de consumo o aplicación</h3>
                <div class="box"><?php echo docText($documento->instrucciones, 'Consumir o aplicar según la dosis indicada por el médico tratante.'); ?></div>
            </section>
        <?php elseif ($tipo === 'justificativos'): ?>
            <section class="section">
                <h3>Justificación médica</h3>
                <div class="box statement">Por medio de la presente se deja constancia de que el/la paciente <?php echo $paciente; ?>, titular de la cédula <?php echo $cedula; ?>, fue evaluado(a) médicamente en fecha <?php echo $fecha; ?>, por lo cual se justifica su ausencia o reposo según criterio médico y evolución clínica.</div>
            </section>
        <?php elseif ($tipo === 'constancia-estudio'): ?>
            <section class="section">
                <h3>Constancia para estudio</h3>
                <div class="box statement">Se hace constar que el/la estudiante <?php echo $paciente; ?>, titular de la cédula <?php echo $cedula; ?>, recibió atención médica en BioVital en fecha <?php echo $fecha; ?>. Se emite la presente constancia para fines académicos.</div>
            </section>
        <?php elseif ($tipo === 'constancia-trabajo'): ?>
            <section class="section">
                <h3>Constancia para trabajo</h3>
                <div class="box statement">Se hace constar que el/la ciudadano(a) <?php echo $paciente; ?>, titular de la cédula <?php echo $cedula; ?>, recibió atención médica en BioVital en fecha <?php echo $fecha; ?>. Se emite la presente constancia para fines laborales.</div>
            </section>
        <?php elseif ($tipo === 'diagnostico'): ?>
            <section class="section">
                <h3>Diagnóstico</h3>
                <div class="box"><?php echo docText($documento->diagnostico); ?></div>
            </section>
            <section class="section">
                <h3>Tratamiento sugerido</h3>
                <div class="box"><?php echo docText($documento->trat_sugerido); ?></div>
            </section>
        <?php elseif ($tipo === 'laboratorio'): ?>
            <section class="section">
                <h3>Estudios solicitados</h3>
                <div class="box"><?php echo docText($documento->est_solicitado); ?></div>
            </section>
            <section class="section">
                <h3>Observaciones adicionales</h3>
                <div class="box"><?php echo docText($documento->obs_adicional); ?></div>
            </section>
        <?php endif; ?>

        <section class="signature">
            <div class="signature-box">
                <strong><?php echo $medico; ?></strong><br>
                Médico tratante
            </div>
        </section>

        <footer class="footer">BioVital - Este documento puede imprimirse o guardarse como PDF desde el navegador.</footer>
    </main>
</body>
</html>
