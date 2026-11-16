<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: interfaz_usuario.php");
    exit();
}

include 'conexion.php';

$mensaje_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $codigo_usuario = trim($_POST['codigo_usuario']);
    $password = trim($_POST['password']);

    if (!empty($codigo_usuario) && !empty($password)) {

        $stmt = $conexion->prepare("
            SELECT autenticacion.password,
                   usuarios.id_rol,
                   usuarios.avatar
            FROM autenticacion
            INNER JOIN usuarios
            ON autenticacion.codigo_usuario = usuarios.codigo_usuario
            WHERE autenticacion.codigo_usuario = ?
        ");

        $stmt->bind_param("s", $codigo_usuario);

        $stmt->execute();

        $stmt->bind_result($hash, $id_rol, $avatar);

        if ($stmt->fetch()) {

            if (password_verify($password, $hash)) {

                $_SESSION['codigo_usuario'] = $codigo_usuario;
                $_SESSION['id_rol'] = $id_rol;
                $_SESSION['usuario'] = true;
                $_SESSION['avatar'] = $avatar;

                if ($id_rol == 2) {

                    echo "tutor ya registrado en la tabla";

                } elseif ($id_rol == 3) {

                    header("Location: interfaz_usuario.php");
                    exit;

                } else {

                    echo "rol desconocido";
                }

            } else {

                $mensaje_error = "Usuario o contraseña incorrectos, intente nuevamente";
            }

        } else {

            $mensaje_error = "Usuario o contraseña incorrectos, intente nuevamente";
        }

        $stmt->close();

    } else {

        $mensaje_error = "Debe ingresar todos los datos, intente nuevamente";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url('img/fondo.webp');
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .fondo {
            backdrop-filter: blur(6px);
            background-color: rgba(248, 249, 250, 0.8);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">

<div class="fondo" style="width: 100%; max-width: 400px;">

    <h3 class="mb-4 text-center">Iniciar Sesión</h3>

    <?php if (!empty($mensaje_error)): ?>

        <div class="alert alert-danger" role="alert">
            <?php echo $mensaje_error; ?>
        </div>

    <?php endif; ?>

    <form action="" method="POST">

        <div class="mb-3">
            <label for="codigo_usuario" class="form-label">
                Código de Usuario
            </label>

            <input type="text"
                   class="form-control"
                   id="codigo_usuario"
                   name="codigo_usuario"
                   required>
        </div>

        <div class="mb-3">

            <label for="password" class="form-label">
                Contraseña
            </label>

            <div class="input-group">

                <input type="password"
                       class="form-control"
                       id="password"
                       name="password"
                       required>

                <button type="button"
                        class="btn btn-outline-secondary"
                        onclick="togglePassword()">

                    Mostrar

                </button>

            </div>
        </div>

        <div class="d-grid mb-3">

            <button type="submit" class="btn btn-primary">
                Ingresar
            </button>

        </div>

    </form>

    <div class="text-center">

        <a href="recuperar_contraseña.php" class="d-block mb-2">
            ¿Olvidaste tu contraseña?
        </a>

        <a href="registro_usuario.php" class="text-decoration-none">
            Registrarme
        </a>

    </div>

</div>

<script>

function togglePassword() {

    const input = document.getElementById("password");
    const btn = event.target;

    if (input.type === "password") {

        input.type = "text";
        btn.textContent = "Ocultar";

    } else {

        input.type = "password";
        btn.textContent = "Mostrar";
    }
}

</script>

</body>
</html>