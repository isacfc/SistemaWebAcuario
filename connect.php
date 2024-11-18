<?php
$nombre = $_POST['nombre'];
$apaterno = $_POST['apaterno'];
$amaterno = $_POST['amaterno'];
$correo = $_POST['correo'];
$paquete = $_POST['paquete'];
$tarjeta= $_POST['tarjeta'];
$fecha = $_POST['fecha'];
$caducidad = $_POST['caducidad'];
$cvv = $_POST['cvv'];

$conn = new mysqli('localhost','root','','acuario_bd');
if($conn->connect_error){
    die('Error al conectar: '.$conn->connect_error);
}else{
    $stmt = $conn->prepare("insert into orden(correo,nombre) values(?,?)");

    $stmt-> bind_param("ss",$correo,$nombre);
    $stmt->execute();
    if ($stmt->execute()) {
        echo "Registro exitoso";
    } else {
        echo "Error al registrar: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();


}


?>