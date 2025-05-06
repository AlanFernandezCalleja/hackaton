<?php
class Empleado {
    private $conn;
    private $table_name = "empleado";

    public $id;
    public $nombre;
    public $area;
    public $cargo;
    public $sueldo;
    public $tiempo_prueba;
    public $fecha_ingreso;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todos los empleados
    public function leer() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_ingreso DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear empleado
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre=:nombre, area=:area, cargo=:cargo, 
                  sueldo=:sueldo, tiempo_prueba=:tiempo_prueba, fecha_ingreso=:fecha_ingreso";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->area = htmlspecialchars(strip_tags($this->area));
        $this->cargo = htmlspecialchars(strip_tags($this->cargo));
        $this->sueldo = htmlspecialchars(strip_tags($this->sueldo));
        $this->tiempo_prueba = htmlspecialchars(strip_tags($this->tiempo_prueba));
        $this->fecha_ingreso = htmlspecialchars(strip_tags($this->fecha_ingreso));

        // Vincular valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":area", $this->area);
        $stmt->bindParam(":cargo", $this->cargo);
        $stmt->bindParam(":sueldo", $this->sueldo);
        $stmt->bindParam(":tiempo_prueba", $this->tiempo_prueba);
        $stmt->bindParam(":fecha_ingreso", $this->fecha_ingreso);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Actualizar empleado
    public function actualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre=:nombre, area=:area, cargo=:cargo, 
                  sueldo=:sueldo, tiempo_prueba=:tiempo_prueba, fecha_ingreso=:fecha_ingreso
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->area = htmlspecialchars(strip_tags($this->area));
        $this->cargo = htmlspecialchars(strip_tags($this->cargo));
        $this->sueldo = htmlspecialchars(strip_tags($this->sueldo));
        $this->tiempo_prueba = htmlspecialchars(strip_tags($this->tiempo_prueba));
        $this->fecha_ingreso = htmlspecialchars(strip_tags($this->fecha_ingreso));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":area", $this->area);
        $stmt->bindParam(":cargo", $this->cargo);
        $stmt->bindParam(":sueldo", $this->sueldo);
        $stmt->bindParam(":tiempo_prueba", $this->tiempo_prueba);
        $stmt->bindParam(":fecha_ingreso", $this->fecha_ingreso);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar empleado
    public function eliminar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>