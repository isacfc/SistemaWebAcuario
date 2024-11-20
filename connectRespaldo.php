<?php
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

    for($i=0;$i<$badulto;$i++){
        $stmt2 = $conn->prepare("INSERT INTO boleto (tipo, fecha, activo, id_paquete,id_orden) VALUES (?, ?, ?, ?,?)");
        $tipo = "Adulto";
        $stmt2->bind_param("ssiii", $tipo, $fecha,$estado, $paquete, $id_orden);
        
        if (!$stmt2->execute()) {
        echo "Error boletos adulto : " . $stmt2->error;
    }

    }

    $stmt2->close();

    if($bnino>0){
        for($i=0;$i<$bnino;$i++){
            $stmt3 = $conn->prepare("INSERT INTO boleto (tipo, fecha, activo, id_paquete,id_orden) VALUES (?, ?, ?, ?,?)");
            $tipo = "Niño";
            $stmt3->bind_param("ssiii", $tipo, $fecha,$estado, $paquete, $id_orden);
            
            if (!$stmt3->execute()) {
            echo "Error boletos niño : " . $stmt3->error;
        }
    
        }

        $stmt3->close();
    }
    



    

    $conn->close();



}


?>