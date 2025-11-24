<?php
    $view = __FILE__;
?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Resultado de la búsqueda</h5>

        <?php if (!$data): ?>
            <div class="alert alert-warning">No se encontraron resultados para la cotización indicada.</div>
        <?php else: ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong><h5>Paciente</h5></strong>
                    <p class="mb-0"><strong>Nombre:</strong> <?= htmlspecialchars($data['paciente_nombre'] . ' ' . $data['paciente_apellido']) ?></p>
                    <p class="mb-0"><strong>Identificación:</strong> <?= htmlspecialchars($data['paciente_identificacion']) ?></p>
                </div>
                <div class="col-md-6">
                    <strong><h5>Profesional</h5></strong>
                    <p class="mb-0"><strong>Nombre:</strong> <?= htmlspecialchars($data['profesional_nombre'] . ' ' . $data['profesional_apellido']) ?></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0"><strong>Fecha de la cita:</strong> <?= htmlspecialchars($data['fecha_cita']) ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0"><strong>Hora:</strong> <?= htmlspecialchars($data['hora_inicio']) ?> - <?= htmlspecialchars($data['hora_fin']) ?></p>
                </div>
            </div>                          
        <?php endif; ?>

        <hr>
        <a href="/prueba_pacificHealth/public/" class="btn btn-secondary">Nueva búsqueda</a>
    </div>
</div>