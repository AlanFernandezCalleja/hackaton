<?php
// agregar_boleta.php
$api_url = 'http://localhost/hackaton/hackaton/gestion-personal/api-boleta/controllers/BoletaController.php';
$api_empleados = 'http://localhost/hackaton/hackaton/gestion-personal/empleados-api/controllers/EmpleadoController.php';

// Obtener lista de empleados para el select
function obtenerEmpleados($url) {
    $response = file_get_contents($url);
    return json_decode($response, true)['records'] ?? [];
}

$empleados = obtenerEmpleados($api_empleados);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_personal' => $_POST['id_personal'],
        'nombre' => $_POST['nombre'],
        'pago' => $_POST['pago'],
        'fecha_pago' => $_POST['fecha_pago']
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($api_url, false, $context);
    
    if ($result !== false) {
        header('Location: boletas.php');
        exit();
    } else {
        $error = "Error al crear la boleta";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nueva Boleta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-header { background-color: #343a40; color: white; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Nueva Boleta de Pago</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="id_personal" class="form-label">Empleado</label>
                                <select class="form-select" id="id_personal" name="id_personal" required>
                                    <option value="">Seleccionar empleado...</option>
                                    <?php foreach ($empleados as $emp): ?>
                                        <option value="<?= $emp['id'] ?>">
                                            <?= htmlspecialchars($emp['nombre']) ?> - <?= htmlspecialchars($emp['cargo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pago" class="form-label">Monto del Pago</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="pago" name="pago" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="boletas.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Boleta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Autocompletar nombre cuando seleccionan empleado
    document.getElementById('id_personal').addEventListener('change', function() {
        const empleadoId = this.value;
        const empleados = <?= json_encode($empleados) ?>;
        const empleado = empleados.find(e => e.id == empleadoId);
        
        if (empleado) {
            document.getElementById('nombre').value = empleado.nombre;
        }
    });
    </script>
</body>
</html>