<?php
    $view = __FILE__;
?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Buscar cotización</h5>
        <form method="GET">
            <input type="hidden" name="action" value="result">
            <div class="mb-3">
                <label for="id" class="form-label">Número de cotización (ID)</label>
                <input type="text" class="form-control" id="id" name="id" placeholder="Ej: 1" required>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    </div>
</div>