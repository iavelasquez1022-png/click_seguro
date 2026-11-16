<?php 
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] =='POST'){
    $nombre_rol = trim($_POST['nombre_rol']);
    if (!empty($nombre_rol)){
        $stmt = $conexion->prepare("INSERT INTO roles(nombre_rol) VALUES (?)");
        $stmt->bind_param("s", $nombre_rol);
        if ($stmt->execute()){
            echo " Rol guardado exitosamente.";
        } else {
            echo " Error al guardar el rol: " . $stmt->error;
            $stmt->close();
        }
    } else {
        echo " El nombre del rol no puede estar vacio.";
    }
} else{
    echo " Acceso no permitido.";
}
?>