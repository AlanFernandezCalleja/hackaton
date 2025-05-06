<?php
require('GeneradorReportes/fpdf.php');
$mysqli = new mysqli('localhost', 'root', '', 'personal');

// Verifica la conexión a la base de datos
if ($mysqli->connect_error) {
    die("Error en la conexión: " . $mysqli->connect_error);
}

// Recuperar el ID desde la variable POST (este paso es gestionado por AJAX más adelante)
$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID de empleado inválido.");
}

// Consulta SQL para obtener el empleado por ID
$query = "SELECT * FROM empleado WHERE id = $id";
$resultado = $mysqli->query($query);

if (!$resultado || $resultado->num_rows === 0) {
    die("No se encontró ningún empleado con ese ID.");
}

// Crear el PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('EMPRESA DE SERVICIOS AUXILIARES FINANCIEROS ARCA LTDA.'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, utf8_decode('Contrato de Colaboración Técnica para el Desarrollo del MVP - Gestión Humana'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function GenerarContrato($empleado)
    {
        $this->SetFont('Arial', '', 11);

        $this->MultiCell(
            0,
            8,
            utf8_decode(
                "En la ciudad de La Paz, a la fecha de ingreso: " . date("d/m/Y", strtotime($empleado['fecha_ingreso'])) . ", "
                    . "la EMPRESA DE SERVICIOS AUXILIARES FINANCIEROS ARCA LTDA. suscribe el presente acuerdo con el/la colaborador(a) "
                    . "{$empleado['nombre']}, quien desarrollará funciones técnicas en el cargo de {$empleado['cargo']} dentro del área de {$empleado['area']}.\n\n"

                    . "ARCA LTDA., tras adquirir un sistema Core de arquitectura basada en microservicios, ha decidido prescindir del módulo de gestión humana "
                    . "por motivos de costos. Sin embargo, se ha determinado internamente que es viable desarrollar un módulo propio con mayores prestaciones.\n\n"

                    . "En este sentido, el/la colaborador(a) forma parte del equipo responsable de la construcción de un MVP funcional en un tiempo récord de 2 horas, "
                    . "el cual será presentado al Directorio de la empresa como muestra de viabilidad técnica. Este MVP incluirá funcionalidades básicas requeridas por el área de Gestión Humana.\n\n"

                    . "El tiempo de prueba será de {$empleado['tiempo_prueba']} días y se establece una remuneración mensual de Bs {$empleado['sueldo']}.\n\n"

                    . "Ambas partes se comprometen a cumplir con los objetivos del proyecto y respetar las condiciones técnicas y organizacionales establecidas."
            )
        );

        $this->Ln(20);
        $this->Cell(90, 10, '_________________________', 0, 0, 'C');
        $this->Cell(10, 10, '', 0, 0);
        $this->Cell(90, 10, '_________________________', 0, 1, 'C');
        $this->Cell(90, 10, 'Firma ARCA LTDA.', 0, 0, 'C');
        $this->Cell(10, 10, '', 0, 0);
        $this->Cell(90, 10, 'Firma del Colaborador', 0, 1, 'C');
        $this->AddPage();
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Cargar empleado desde la base de datos
$empleado = $resultado->fetch_assoc();
$pdf->GenerarContrato($empleado);

// Salida del PDF
$pdf->Output('D', 'Contratos_ARCA_MVP.pdf');
