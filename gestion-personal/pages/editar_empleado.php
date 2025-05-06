<?php
$api_url = 'http://localhost/hackaton/hackaton/gestion-personal/empleados-api/controllers/EmpleadoController.php';

// Obtener datos del empleado a editar
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleadoActualizado = [
        'id' => $id,
        'nombre' => $_POST['nombre'],
        'area' => $_POST['area'],
        'cargo' => $_POST['cargo'],
        'sueldo' => $_POST['sueldo'],
        'tiempo_prueba' => $_POST['tiempo_prueba'],
        'fecha_ingreso' => $_POST['fecha_ingreso']
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($empleadoActualizado)
        ]
    ]);
    
    $result = file_get_contents($api_url, false, $context);
    
    if ($result !== false) {
        header('Location: personalPagina.php');
        exit();
    } else {
        $error = "Error al actualizar el empleado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Empleado</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($empleado['nombre']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="area" class="form-label">Área/Departamento</label>
                                <input type="text" class="form-control" id="area" name="area" 
                                       value="<?= htmlspecialchars($empleado['area']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cargo" class="form-label">Cargo</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" 
                                       value="<?= htmlspecialchars($empleado['cargo']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="sueldo" class="form-label">Sueldo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="sueldo" name="sueldo" 
                                           value="<?= htmlspecialchars($empleado['sueldo']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tiempo_prueba" class="form-label">Período de Prueba (días)</label>
                                <input type="number" class="form-control" id="tiempo_prueba" name="tiempo_prueba" 
                                       value="<?= htmlspecialchars($empleado['tiempo_prueba']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" 
                                       value="<?= htmlspecialchars($empleado['fecha_ingreso']) ?>" required>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="personalPagina.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>