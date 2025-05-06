<?php
$api_url = 'http://localhost/hackaton/hackaton/gestion-personal/empleados-api/controllers/EmpleadoController.php';

$id = $_GET['id'] ?? null;
$empleado = null;

if ($id) {
    $data = file_get_contents($api_url);
    $empleados = json_decode($data, true);
    
    if (isset($empleados['records'])) {
        foreach ($empleados['records'] as $emp) {
            if ($emp['id'] == $id) {
                $empleado = $emp;
                break;
            }
        }
    }
}

if (!$empleado) {
    header('Location: personalPagina.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-user me-2"></i>Detalles del Empleado</h4>
                            <a href="personalPagina.php" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">ID:</div>
                            <div class="col-md-8"><?= htmlspecialchars($empleado['id']) ?></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">Nombre:</div>
                            <div class="col-md-8"><?= htmlspecialchars($empleado['nombre']) ?></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">Área/Departamento:</div>
                            <div class="col-md-8"><?= htmlspecialchars($empleado['area']) ?></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">Cargo:</div>
                            <div class="col-md-8"><?= htmlspecialchars($empleado['cargo']) ?></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">Sueldo:</div>
                            <div class="col-md-8">$<?= number_format($empleado['sueldo'], 2) ?></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4 fw-bold">Período de Prueba:</div>
                            <div class="col-md-8">
                                <?php if ($empleado['tiempo_prueba'] == 0): ?>
                                    <span class="badge bg-success">Completado</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($empleado['tiempo_prueba']) ?> días</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 fw-bold">Fecha de Ingreso:</div>
                            <div class="col-md-8"><?= date('d/m/Y', strtotime($empleado['fecha_ingreso'])) ?></div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="editar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="personalPagina.php" class="btn btn-secondary">
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