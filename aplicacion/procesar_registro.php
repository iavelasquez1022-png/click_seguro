<?php
//recoleccion de datos
include 'conexion.php';
$id_usuario= $_POST['id_usuario'];
$nombres= $_POST['nombres'];
$apellidos= $_POST['apellidos'];
$email= $_POST['email'];
$id_rol= $_POST['id_rol'];
$telefono= $_POST['telefono'];
$avatar = $_POST['avatar'];

//encriptacion de contraseña
$password= password_hash($_POST['password'],PASSWORD_DEFAULT);
//verificacion del email en la base de datos

    $verificar= $conexion->prepare("SELECT email, telefono, id_usuario FROM usuarios WHERE email = ? OR telefono = ? OR id_usuario = ?");
    $verificar->bind_param("ssi",$email, $telefono, $id_usuario);
    $verificar->execute();
    $verificar->store_result();
    if ($verificar->num_rows > 0) {
        echo " El correo, numero de identificacion o telefono ya esta registrado. Verifique  e ingrese nuevos datos";
        exit;
    }
//Generar codigo aleatorio
    
    $codigo_usuario= substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,6);
    if ($id_rol==3) {
        $conexion->begin_transaction();
        try {
//inserccion en la tabla autenticacion 
            $stmt1= $conexion->prepare("INSERT INTO autenticacion (codigo_usuario, password) VALUES (?, ? )");
            $stmt1->bind_param("ss", $codigo_usuario, $password );
            $stmt1->execute();
        
//inserccion en tabla usuarios
            $stmt2= $conexion->prepare("INSERT INTO usuarios (id_usuario, nombres, apellidos, email, id_rol, codigo_usuario, telefono, avatar) VALUES (?, ? ,? ,? ,? ,? ,?, ? )");
            $stmt2->bind_param("isssisss", $id_usuario, $nombres, $apellidos, $email, $id_rol, $codigo_usuario, $telefono, $avatar);
            $stmt2->execute();
        
        
//si ambas tienen exito, confirma y redirecciona 
            $conexion->commit();
            echo "
    <div style='text-align:center; font-family:sans-serif; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:100%;'>
        <p>Registro exitoso. Tu código de usuario fue enviado a: <strong>$email</strong></p>
        <p>Serás redirigido al inicio de sesión en <span id='cuenta'>5</span> segundos.</p>
        <a href='login.php'><button style='padding:10px 20px; font-size:16px;'>Iniciar sesión ahora</button></a>
    </div>
    
    <script>
        let segundos = 15;
        let cuenta = document.getElementById('cuenta');
        let intervalo = setInterval(() => {
            segundos--;
            cuenta.innerText = segundos;
            if (segundos <= 0) {
                clearInterval(intervalo);
                window.location.href = 'login.php';
            }
        }, 2000);
    </script>";
            $stmt1->close();
            $stmt2->close();
            $conexion->close();
        
        } catch (Exception $e) {
            $conexion->rollback();
            echo "Error al registrar. Nada fue guardado";
        }

       
    } else if ($id_rol==2) {
        echo "<h3> Esperar nuestra respuesta en un plazo no mayor a 15 dias</h3> <p>Si no obtienes aprobación comuniquese al correo 1010078559@teinco.edu.co</p>";
}
?>
