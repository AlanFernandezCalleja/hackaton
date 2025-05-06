<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Boleta.php';

$database = new Database();
$db = $database->getConnection();

$boleta = new Boleta($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Leer boletas o una boleta específica
        if(isset($_GET['id'])) {
            $boleta->id = $_GET['id'];
            $stmt = $boleta->leerUna();
            $num = $stmt->rowCount();

            if($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                http_response_code(200);
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Boleta no encontrada."));
            }
        } else {
            $stmt = $boleta->leer();
            $num = $stmt->rowCount();

            if($num > 0) {
                $boletas_arr = array();
                $boletas_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $boleta_item = array(
                        "id" => $id,
                        "id_personal" => $id_personal,
                        "nombre" => $nombre,
                        "pago" => $pago,
                        "fecha_pago" => $fecha_pago
                    );
                    array_push($boletas_arr["records"], $boleta_item);
                }

                http_response_code(200);
                echo json_encode($boletas_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No se encontraron boletas."));
            }
        }
        break;
        
    case 'POST':
        // Crear nueva boleta
        $data = json_decode(file_get_contents("php://input"));

        if(
            !empty($data->id_personal) &&
            !empty($data->nombre) &&
            !empty($data->pago) &&
            !empty($data->fecha_pago)
        ) {
            $boleta->id_personal = $data->id_personal;
            $boleta->nombre = $data->nombre;
            $boleta->pago = $data->pago;
            $boleta->fecha_pago = $data->fecha_pago;

            if($boleta->crear()) {
                http_response_code(201);
                echo json_encode(array("message" => "Boleta creada correctamente."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear la boleta."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos. No se pudo crear la boleta."));
        }
        break;
        
    case 'DELETE':
        // Eliminar boleta
        $data = json_decode(file_get_contents("php://input"));
        
        $boleta->id = $data->id;

        if($boleta->eliminar()) {
            http_response_code(200);
            echo json_encode(array("message" => "Boleta eliminada correctamente."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "No se pudo eliminar la boleta."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido."));
        break;
}

?>