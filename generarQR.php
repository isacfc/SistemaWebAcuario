<?php
///////////////////Incluye las librerías necesarias///////////////////////
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'phpqrcode/qrlib.php';
require 'connect.php';
require 'fpdf/fpdf.php';

///////////////////////////////////////////////////////////////////////////

// Asegúrate de recibir la cantidad de boletos
if (isset($_GET['btotales'])) {
    $btotales = (int) $_GET['btotales']; // Cantidad de boletos
} else {
    echo "No se recibió el total de boletos.";
    exit();
}

// Crear carpeta temporal si no existe
$dir = 'temp/';
if (!file_exists($dir)) {
    mkdir($dir);
}

// Consulta para obtener los últimos `btotales` boletos
$result = $conn->query("SELECT * FROM boleto ORDER BY id_boleto DESC LIMIT $btotales");

if ($result && $result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        // Recuperar datos del boleto
        $idBoleto = $fila['id_boleto'];
        $tipo = $fila['tipo'];
        $fecha = $fila['fecha'];
        $activo = ($fila['activo'] == 1) ? "Activo" : "Inactivo";
        $precioTotal = $fila['precioTotal'];
        $idPaquete = $fila['id_paquete'];
        $idOrden = $fila['id_orden'];

        // Crear contenido del QR
        $contenido = "Número de boleto: $idBoleto | " .
                     "Tipo de boleto: $tipo | " .
                     "Fecha de boleto: $fecha | " .
                     "Estado del boleto: $activo | " .
                     "Precio del boleto: $precioTotal";

        // Generar QR
        $filenameQR = $dir . "qr_boleto_$idBoleto.png";
        $tamanio = 10; // Tamaño del QR
        $level = 'M'; // Precisión del QR
        $frameSize = 3; // Marco en blanco
        QRcode::png($contenido, $filenameQR, $level, $tamanio, $frameSize);

        // Generar PDF
        $pdf = new FPDF('P', 'cm', array(5, 10));
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->MultiCell(3, 0.5, 'Boleto de entrada al acuario', 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, 'Numero del boleto: ' . $idBoleto, 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, 'Tipo de boleto: ' . $tipo, 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, 'Fecha del boleto: ' . $fecha, 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, 'Estado del boleto: ' . $activo, 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, 'Precio del boleto: ' . $precioTotal, 1, 'C', 0);
        $pdf->Image($filenameQR, 1, 5, 3);

        // Guardar el PDF
        $filenamePDF = $dir . "boleto_$idBoleto.pdf";
        $pdf->Output($filenamePDF, 'F');

        // Enviar correo
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '587';
        $mail->Username = 'ca411523@uaeh.edu.mx'; // Correo emisor
        $mail->Password = 'zxeo xugm bxgn mbtj'; // Contraseña de la aplicación

        $mail->setFrom('ca411523@uaeh.edu.mx', 'Boleto de entrada al Acuario');
        $mail->addAddress('fr411593@uaeh.edu.mx', 'A quien corresponda'); // Destinatario

        $mail->addAttachment($filenamePDF); // Adjuntar el PDF
        $mail->Subject = 'Acuario Crystal';
        $mail->Body = "Gracias por su compra. Este es el resumen de su orden. Lo esperamos pronto. -El equipo de Acuario Crystal.";

        if (!$mail->send()) {
            echo "Error al enviar el correo para el boleto $idBoleto: " . $mail->ErrorInfo;
        } else {
            echo "Correo enviado para el boleto $idBoleto<br>";
        }
    }
} else {
    echo "No se encontraron boletos para procesar.";
}
?>
