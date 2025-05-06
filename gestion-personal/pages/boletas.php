<?php
// boletas.php
$api_url = 'http://localhost/hackaton/hackaton/gestion-personal/api-boleta/controllers/BoletaController.php';

function obtenerBoletas($url) {
    $context = stream_context_create([
        'http' => ['ignore_errors' => true]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        die("Error al conectar con la API de boletas");
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error decodificando JSON: " . json_last_error_msg());
    }
    
    return $data;
}

$boletas = obtenerBoletas($api_url);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Boletas de Pago</title>
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
        .pago-positivo { color: #28a745; font-weight: bold; }
        .badge-reciente { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-title text-center">
            <h1><i class="fas fa-file-invoice-dollar me-2"></i>Boletas de Pago</h1>
        </div>
        
        <div class="card table-container">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Listado de Boletas</h5>
                <div>
                    <a href="agregar_boleta.php" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-plus me-1"></i> Nueva Boleta
                    </a>
                    <a href="personalPagina.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-users me-1"></i> Ver Empleados
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>ID Personal</th>
                                <th>Nombre</th>
                                <th>Pago</th>
                                <th>Fecha de Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($boletas['records']) && count($boletas['records']) > 0): ?>
                                <?php foreach ($boletas['records'] as $boleta): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($boleta['id']) ?></td>
                                        <td><?= htmlspecialchars($boleta['id_personal']) ?></td>
                                        <td><?= htmlspecialchars($boleta['nombre']) ?></td>
                                        <td class="pago-positivo">$<?= number_format($boleta['pago'], 2) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($boleta['fecha_pago'])) ?>
                                            <?php if (strtotime($boleta['fecha_pago']) >= strtotime('-7 days')): ?>
                                                <span class="badge badge-reciente ms-2">Reciente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="ver_boleta.php?id=<?= $boleta['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_boleta.php?id=<?= $boleta['id'] ?>" class="btn btn-sm btn-outline-success me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger btn-eliminar-boleta" data-id="<?= $boleta['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron boletas</td>
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
        
        // Eliminar boleta
        $('.btn-eliminar-boleta').click(function() {
            const id = $(this).data('id');
            if (confirm('¿Estás seguro de eliminar esta boleta?')) {
                $.ajax({
                    url: apiUrl,
                    type: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({id: id}),
                    success: function() {
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error al eliminar boleta:', error);
                        alert('Error al eliminar boleta');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>