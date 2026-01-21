<?php


///////////////////Incluye las librerías necesarias///////////////////////
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'phpqrcode/qrlib.php';
require 'fpdf/fpdf.php';

///////////////////////////////////////////////////////////////////////////
$nombre = $_POST['nombre'];
$apaterno = $_POST['apaterno'];
$amaterno = $_POST['amaterno'];
$correo = $_POST['correo'];
$paquete = $_POST['paquete'];
$fecha = $_POST['fecha'];
$badulto = (int) $_POST['b-adulto'];
$bnino = (int) $_POST['b-nino'];
$estado=1;


$btotales = $badulto + $bnino;

$nombreCompleto = $nombre . " " . $apaterno . " " . $amaterno;

$conn = new mysqli('localhost','root','','acuario_bd');
if($conn->connect_error){
    die('Error al conectar: '.$conn->connect_error);
}else{

    
    
    $stmt1 = $conn->prepare("INSERT INTO orden (correo, nombre) VALUES (?, ?)");
    $stmt1->bind_param("ss", $correo, $nombreCompleto);
    if (!$stmt1->execute()) {
        echo "Error en el primer registro: " . $stmt1->error;
    }

    $orden= $conn->query("SELECT MAX(id_orden) AS max_id FROM orden;");

    if ($orden) {
        $fila = $orden->fetch_assoc(); // Recuperar el resultado como array asociativo
        $id_orden = $fila['max_id'];   // Extraer el valor de 'max_id'
    } else {
        echo "Error al obtener el ID de orden: " . $conn->error;
        exit(); // Salir en caso de error
    }
    


    $stmt1->close();

    // Segundo INSERT

    $boletos = [];

    for($i=0;$i<$badulto;$i++){
        $stmt2 = $conn->prepare("INSERT INTO boleto (tipo, fecha, activo, id_paquete,id_orden) VALUES (?, ?, ?, ?,?)");
        $tipo = "Adulto";
        $stmt2->bind_param("ssiii", $tipo, $fecha,$estado, $paquete, $id_orden);
        
        if ($stmt2->execute()) {
            $boletos[] = $conn->insert_id; // Guardamos el ID del boleto insertado
        } else {
            echo "Error boletos adulto : " . $stmt2->error;
        }
    }

    }

    $stmt2->close();

    if($bnino>0){
        for($i=0;$i<$bnino;$i++){
            $stmt3 = $conn->prepare("INSERT INTO boleto (tipo, fecha, activo, id_paquete,id_orden) VALUES (?, ?, ?, ?,?)");
            $tipo = "Niño";
            $stmt3->bind_param("ssiii", $tipo, $fecha,$estado, $paquete, $id_orden);
            
            if ($stmt3->execute()) {
                $boletos[] = $conn->insert_id; // Guardamos el ID del boleto insertado
            } else {
                echo "Error boletos niño : " . $stmt3->error;
            }
    
        }

        $stmt3->close();
    }


    $boletosDatos = [];
    foreach ($boletos as $idBoleto) {
        $query = $conn->query("SELECT * FROM boleto WHERE id_boleto = $idBoleto");
        if ($query) {
            $boletosDatos[] = $query->fetch_assoc(); // Recuperar los datos del boleto
        }
    }

    $conn->close();

    if (empty($boletosDatos)) {
        die("No se generaron boletos. Intente nuevamente.");
    }


    $dir = 'temp/';
    if (!file_exists($dir)) {
        mkdir($dir);
    }

    // Crear PDF y QR por cada boleto
    $archivosPDF = [];
    
    foreach ($boletosDatos as $boleto) {
        $idBoleto = $boleto['id_boleto'];
        $tipo = $boleto['tipo'];
        $fecha = $boleto['fecha'];
        $activo = $boleto['activo'];

        $filename = $dir . "qr_$idBoleto.png";
        $contenido = "Número de boleto: $idBoleto | Tipo: $tipo | Fecha: $fecha | Estado: Activo";
        QRcode::png($contenido, $filename, 'M', 10, 3);

        $pdf = new FPDF('P', 'cm', array(5, 10));
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->MultiCell(3, 0.5, mb_convert_encoding("Boleto de entrada al acuario", 'ISO-8859-1', 'UTF-8'), 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, mb_convert_encoding("Número del boleto: $idBoleto", 'ISO-8859-1', 'UTF-8'), 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, mb_convert_encoding("Tipo: $tipo", 'ISO-8859-1', 'UTF-8'), 1, 'C', 0);
        $pdf->MultiCell(3, 0.5, mb_convert_encoding("Fecha: $fecha", 'ISO-8859-1', 'UTF-8'), 1, 'C', 0);

        $pdf->Image($filename, 1, 5, 3);

        $filenamePDF = $dir . "boleto_$idBoleto.pdf";
        $pdf->Output($filenamePDF, 'F');
        $archivosPDF[] = $filenamePDF;
    }

    // Enviar correo con los boletos adjuntos
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->Username='ca411523@uaeh.edu.mx';//Mi correo
    $mail->Password='zxeo xugm bxgn mbtj';//Esta lleve te la genera google (en al configuración busca contraseña de aplicación)


    $mail->setFrom('ca411523@uaeh.edu.mx', 'Boleto de entrada al Acuario');//correo del emisor
    $mail->addAddress($correo, "Boleto de entrada al Acuario");

    foreach ($archivosPDF as $archivo) {
        $mail->addAttachment($archivo);
    }

    $mail->Subject = 'Tus boletos de entrada';
    $mail->Body = "Gracias por su compra. Adjuntamos sus boletos.";

    if ($mail->send()) {
        header("Location:packages.html");
    } else {
        echo 'Error al enviar los boletos: ' . $mail->ErrorInfo;
    }


    
    



    








?>