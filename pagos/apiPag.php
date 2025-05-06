<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root"; // Usuario por defecto en XAMPP
$password = ""; // Contraseña por defecto en XAMPP
$database = "boleta";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Método GET: Obtener todas las boletas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Usar prepared statement para seguridad
    $stmt = $conn->prepare("SELECT * FROM Boleta");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $boletas = [];
    while ($row = $result->fetch_assoc()) {
        $boletas[] = $row;
    }
    
    echo json_encode($boletas);
    $stmt->close();
}

// Método POST: Crear una nueva boleta de pago
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validar datos requeridos
    if (!isset($data['id_personal'], $data['nombre'], $data['pago'], $data['fecha_pago'])) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        exit;
    }
    
    // Sanitizar y validar datos
    $id_personal = filter_var($data['id_personal'], FILTER_VALIDATE_INT);
    $nombre = htmlspecialchars($data['nombre'], ENT_QUOTES, 'UTF-8');
    $pago = filter_var($data['pago'], FILTER_VALIDATE_FLOAT);
    $fecha_pago = $data['fecha_pago'];
    
    if ($id_personal === false || $pago === false || !strtotime($fecha_pago)) {
        http_response_code(400);
        echo json_encode(["error" => "Datos inválidos"]);
        exit;
    }
    
    // Usar prepared statement para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO boleta (id_personal, nombre, pago, fecha_pago) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $id_personal, $nombre, $pago, $fecha_pago);
    
    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Boleta creada correctamente", "id" => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al crear boleta: " . $stmt->error]);
    }
    
    $stmt->close();
}

// Método DELETE: Eliminar una boleta por ID
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID no proporcionado"]);
        exit;
    }
    
    $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    
    if ($id === false) {
        http_response_code(400);
        echo json_encode(["error" => "ID inválido"]);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM Boleta WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["mensaje" => "Boleta eliminada correctamente"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Boleta no encontrada"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar boleta: " . $stmt->error]);
    }
    
    $stmt->close();
}

// Método no soportado
else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
?>