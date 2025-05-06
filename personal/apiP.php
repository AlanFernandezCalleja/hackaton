<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root"; // Usuario por defecto en XAMPP
$password = ""; // Contraseña por defecto en XAMPP
$database = "personal";
 
// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Método GET: Obtener todos los empleados
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM empleado";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $empleados = array();
        while ($row = $result->fetch_assoc()) {
            $empleados[] = $row;
        }
        echo json_encode($empleados);
    } else {
        echo json_encode([]);
    }
}

// Método POST: Crear un nuevo empleado
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = $data['nombre'];
    $area = $data['area'];
    $cargo = $data['cargo'];
    $sueldo = $data['sueldo'];
    $tiempo_prueba = $data['tiempo_prueba'];
    $fecha_ingreso = $data['fecha_ingreso'];

    $sql = "INSERT INTO empleado (nombre, area, cargo, sueldo, tiempo_prueba, fecha_ingreso) 
            VALUES ('$nombre', '$area', '$cargo', $sueldo, $tiempo_prueba, '$fecha_ingreso')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["mensaje" => "Empleado creado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al crear empleado: " . $conn->error]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $nombre = $data['nombre'];
    $area = $data['area'];
    $cargo = $data['cargo'];
    $sueldo = $data['sueldo'];
    $tiempo_prueba = $data['tiempo_prueba'];
    $fecha_ingreso = $data['fecha_ingreso'];

    $sql = "UPDATE empleado SET 
            nombre = '$nombre', 
            area = '$area', 
            cargo = '$cargo', 
            sueldo = $sueldo, 
            tiempo_prueba = $tiempo_prueba, 
            fecha_ingreso = '$fecha_ingreso'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["mensaje" => "Empleado actualizado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al actualizar empleado: " . $conn->error]);
    }
}

// Método DELETE: Eliminar un empleado por ID
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $sql = "DELETE FROM empleado WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(["mensaje" => "Empleado eliminado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al eliminar empleado: " . $conn->error]);
    }
}

// Cerrar conexión
$conn->close();
?>