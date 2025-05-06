<?php
class Boleta {
    private $conn;
    private $table_name = "boleta";

    public $id;
    public $id_personal;
    public $nombre;
    public $pago;
    public $fecha_pago;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todas las boletas
    public function leer() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_pago DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear boleta
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id_personal=:id_personal, nombre=:nombre, 
                  pago=:pago, fecha_pago=:fecha_pago";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->id_personal = htmlspecialchars(strip_tags($this->id_personal));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->pago = htmlspecialchars(strip_tags($this->pago));
        $this->fecha_pago = htmlspecialchars(strip_tags($this->fecha_pago));

        // Vincular valores
        $stmt->bindParam(":id_personal", $this->id_personal);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":pago", $this->pago);
        $stmt->bindParam(":fecha_pago", $this->fecha_pago);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar boleta
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

    // Leer una sola boleta por ID
    public function leerUna() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }
}
?>