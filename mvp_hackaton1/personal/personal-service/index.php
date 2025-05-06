<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$conn = new mysqli("localhost", "root", "", "personaldb");

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida"]));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO funcionarios (nombre, area, cargo, salario, activo) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("sssd", $data["nombre"], $data["area"], $data["cargo"], $data["salario"]);
    $stmt->execute();
    echo json_encode(["status" => "ok", "id" => $conn->insert_id]);
}
?>