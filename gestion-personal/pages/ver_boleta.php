<?php
// ver_boleta.php
$api_url = 'http://localhost/hackaton/hackaton/gestion-personal/api-boleta/controllers/BoletaController.php';
$api_empleados = 'http://localhost/hackaton/hackaton/gestion-personal/empleados-api/controllers/EmpleadoController.php';

// Obtener boleta específica
$id = $_GET['id'] ?? null;
$boleta = null;

if ($id) {
    $response = file_get_contents($api_url . '?id=' . $id);
    $boleta = json_decode($response, true);
}

// Obtener información del empleado
$empleado = null;
if ($boleta && isset($boleta['id_personal'])) {
    $response = file_get_contents($api_empleados . '?id=' . $boleta['id_personal']);
    $empleado = json_decode($response, true);
}

if (!$boleta || isset($boleta['message'])) {
    header('Location: boletas.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta #<?= $boleta['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-header { background-color: #343a40; color: white; }
        .info-label { font-weight: bold; }
        .boleta-container { max-width: 600px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow boleta-container">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Boleta de Pago</h3>
                        <h4 class="mt-2">#<?= $boleta['id'] ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 info-label">Fecha de Pago:</div>
                            <div class="col-md-6"><?= date('d/m/Y', strtotime($boleta['fecha_pago'])) ?></div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3"><i class="fas fa-user-tie me-2"></i>Datos del Empleado</h5>
                        <div class="row mb-2">
                            <div class="col-md-6 info-label">ID Empleado:</div>
                            <div class="col-md-6"><?= $boleta['id_personal'] ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6 info-label">Nombre:</div>
                            <div class="col-md-6"><?= htmlspecialchars($boleta['nombre']) ?></div>
                        </div>
                        <?php if ($empleado): ?>
                        <div class="row mb-2">
                            <div class="col-md-6 info-label">Cargo:</div>
                            <div class="col-md-6"><?= htmlspecialchars($empleado['cargo']) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-label">Área:</div>
                            <div class="col-md-6"><?= htmlspecialchars($empleado['area']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h5 class="mb-3"><i class="fas fa-money-bill-wave me-2"></i>Detalles del Pago</h5>
                        <div class="row mb-2">
                            <div class="col-md-6 info-label">Monto:</div>
                            <div class="col-md-6 text-success fw-bold">$<?= number_format($boleta['pago'], 2) ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-label">Estado:</div>
                            <div class="col-md-6">
                                <span class="badge bg-success">Completado</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="editar_boleta.php?id=<?= $boleta['id'] ?>" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="boletas.php" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>