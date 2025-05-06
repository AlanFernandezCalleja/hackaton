<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/config/database.php';

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Depuración - muestra la ruta solicitada
error_log("Solicitud recibida en: " . $request_uri);

// Rutas válidas (ajusta según tu configuración)
$valid_routes = [
    '/hackaton/hackaton/gestion-personal/empleados-api/empleados',
    '/empleados-api/empleados',
    '/empleados' // Prueba con ruta más simple
];

foreach ($valid_routes as $route) {
    if (strpos($request_uri, $route) !== false) {
        require_once __DIR__ . '/controllers/EmpleadoController.php';
        exit();
    }
}

http_response_code(404);
echo json_encode([
    'message' => 'Endpoint no encontrado',
    'debug_info' => [
        'request_uri' => $request_uri,
        'valid_routes' => $valid_routes
    ]
]);
?>