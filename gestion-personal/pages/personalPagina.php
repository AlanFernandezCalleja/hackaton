<?php
// personalPagina.php
$api_url = 'http://localhost/hackaton/hackaton/gestion-personal/empleados-api/controllers/EmpleadoController.php';

function obtenerEmpleados($url) {
    $context = stream_context_create([
        'http' => ['ignore_errors' => true]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        die("Error al conectar con la API");
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error decodificando JSON: " . json_last_error_msg());
    }
    
    return $data;
}

$empleados = obtenerEmpleados($api_url);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-container { margin-top: 20px; }
        .header-title {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .sueldo-positivo { color: #28a745; font-weight: bold; }
        .badge-prueba { background-color: #ffc107; color: #212529; }
        .badge-completado { background-color: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-title text-center">
            <h1><i class="fas fa-users me-2"></i>Gestión de Empleados</h1>
        </div>
        
        <div class="card table-container">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Lista de Empleados</h5>
                <a href="agregar_empleado.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Agregar Empleado
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Área</th>
                                <th>Cargo</th>
                                <th>Sueldo</th>
                                <th>Tiempo Prueba</th>
                                <th>Fecha Ingreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($empleados['records']) && count($empleados['records']) > 0): ?>
                                <?php foreach ($empleados['records'] as $empleado): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($empleado['id']) ?></td>
                                        <td><?= htmlspecialchars($empleado['nombre']) ?></td>
                                        <td><?= htmlspecialchars($empleado['area']) ?></td>
                                        <td><?= htmlspecialchars($empleado['cargo']) ?></td>
                                        <td class="sueldo-positivo">$<?= number_format($empleado['sueldo'], 2) ?></td>
                                        <td>
                                            <?php if ($empleado['tiempo_prueba'] == 0): ?>
                                                <span class="badge badge-completado">Completado</span>
                                            <?php else: ?>
                                                <span class="badge badge-prueba"><?= htmlspecialchars($empleado['tiempo_prueba']) ?> días</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($empleado['fecha_ingreso'])) ?></td>
                                        <td>
                                            <a href="ver_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-outline-success me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="<?= $empleado['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No se encontraron empleados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery para AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    $(document).ready(function() {
        const apiUrl = '<?= $api_url ?>';
        
        // Eliminar empleado
        $('.btn-eliminar').click(function() {
            const id = $(this).data('id');
            if (confirm('¿Estás seguro de eliminar este empleado?')) {
                $.ajax({
                    url: apiUrl,
                    type: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({id: id}),
                    success: function() {
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error al eliminar empleado:', error);
                        alert('Error al eliminar empleado');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>