
<?php

require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $apaterno = $_POST['apaterno'];
    $amaterno = $_POST['amaterno'];
    $correo = $_POST['correo'];
    $paquete = $_POST['paquete'];
    $fecha = $_POST['fecha'];
    $badulto = (int) $_POST['b-adulto'];
    $bnino = (int) $_POST['b-nino'];
    $estado = 1;

    $btotales = $badulto + $bnino;
    $nombreCompleto = $nombre . " " . $apaterno . " " . $amaterno;

    // Insertar datos en la tabla `orden`
    $stmt1 = $conn->prepare("INSERT INTO orden (correo, nombre) VALUES (?, ?)");
    $stmt1->bind_param("ss", $correo, $nombreCompleto);
    if (!$stmt1->execute()) {
        die("Error en el registro de la orden: " . $stmt1->error);
    }

    // Obtener el ID de la orden recién creada
    $orden = $conn->query("SELECT MAX(id_orden) AS max_id FROM orden");
    if ($orden) {
        $fila = $orden->fetch_assoc();
        $id_orden = $fila['max_id'];
    } else {
        die("Error al obtener el ID de la orden: " . $conn->error);
    }
    $stmt1->close();

    // Insertar boletos de adulto
    for ($i = 0; $i < $badulto; $i++) {
        $stmt2 = $conn->prepare("INSERT INTO boleto (tipo, fecha, activo, id_paquete, id_orden) VALUES (?, ?, ?, ?, ?)");
        $tipo = "Adulto";
        $stmt2->bind_param("ssiii", $tipo, $fecha, $estado, $paquete, $id_orden);
        if (!$stmt2->execute()) {
            die("Error al insertar boletos de adulto: " . $stmt2->error);
        }
    }
    $stmt2->close();

    // Insertar boletos de niño
    for ($i = 0; $i < $bnino; $i++) {
        $stmt3 = $conn->prepare("INSERT INTO boleto (tipo, fecha, activo, id_paquete, id_orden) VALUES (?, ?, ?, ?, ?)");
        $tipo = "Niño";
        $stmt3->bind_param("ssiii", $tipo, $fecha, $estado, $paquete, $id_orden);
        if (!$stmt3->execute()) {
            die("Error al insertar boletos de niño: " . $stmt3->error);
        }
    }
    $stmt3->close();

    $conn->close();

    // Redirigir a generarQR.php pasando el número total de boletos
    
    exit();
} else {
    die("No se recibieron datos del formulario.");
}
?>


?>