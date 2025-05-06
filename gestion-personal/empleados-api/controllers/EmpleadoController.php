<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Usar rutas absolutas con __DIR__
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Empleado.php';

$database = new Database();
$db = $database->getConnection();

$empleado = new Empleado($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Leer empleados
        $stmt = $empleado->leer();
        $num = $stmt->rowCount();

        if($num > 0) {
            $empleados_arr = array();
            $empleados_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $empleado_item = array(
                    "id" => $id,
                    "nombre" => $nombre,
                    "area" => $area,
                    "cargo" => $cargo,
                    "sueldo" => $sueldo,
                    "tiempo_prueba" => $tiempo_prueba,
                    "fecha_ingreso" => $fecha_ingreso
                );

                array_push($empleados_arr["records"], $empleado_item);
            }

            http_response_code(200);
            echo json_encode($empleados_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No se encontraron empleados."));
        }
        break;
        
    case 'POST':
        // Crear empleado
        $data = json_decode(file_get_contents("php://input"));

        if(
            !empty($data->nombre) &&
            !empty($data->area) &&
            !empty($data->cargo) &&
            !empty($data->sueldo) &&
            !empty($data->tiempo_prueba) &&
            !empty($data->fecha_ingreso)
        ) {
            $empleado->nombre = $data->nombre;
            $empleado->area = $data->area;
            $empleado->cargo = $data->cargo;
            $empleado->sueldo = $data->sueldo;
            $empleado->tiempo_prueba = $data->tiempo_prueba;
            $empleado->fecha_ingreso = $data->fecha_ingreso;

            if($empleado->crear()) {
                http_response_code(201);
                echo json_encode(array("message" => "Empleado creado correctamente."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el empleado."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos. No se pudo crear el empleado."));
        }
        break;
        
    case 'PUT':
        // Actualizar empleado
        $data = json_decode(file_get_contents("php://input"));

        $empleado->id = $data->id;
        
        $empleado->nombre = $data->nombre;
        $empleado->area = $data->area;
        $empleado->cargo = $data->cargo;
        $empleado->sueldo = $data->sueldo;
        $empleado->tiempo_prueba = $data->tiempo_prueba;
        $empleado->fecha_ingreso = $data->fecha_ingreso;

        if($empleado->actualizar()) {
            http_response_code(200);
            echo json_encode(array("message" => "Empleado actualizado correctamente."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "No se pudo actualizar el empleado."));
        }
        break;
        
    case 'DELETE':
        // Eliminar empleado
        $data = json_decode(file_get_contents("php://input"));
        
        $empleado->id = $data->id;

        if($empleado->eliminar()) {
            http_response_code(200);
            echo json_encode(array("message" => "Empleado eliminado correctamente."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "No se pudo eliminar el empleado."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido."));
        break;
}

?>