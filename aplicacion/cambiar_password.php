<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'conexion.php';

$codigo_usuario = $_SESSION['codigo_usuario'];
$mensaje = '';
$tipo = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['password_actual'] ?? '';
    $nuevo = $_POST['password_nuevo'] ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';

    if ($actual === '' || $nuevo === '' || $confirmar === '') {
        $mensaje = 'Completa todos los campos.';
        $tipo = 'danger';
    } elseif ($nuevo !== $confirmar) {
        $mensaje = 'La nueva contraseña no coincide con la confirmación.';
        $tipo = 'danger';
    } elseif (strlen($nuevo) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
        $tipo = 'danger';
    } else {
        $stmt = $conexion->prepare('SELECT password FROM autenticacion WHERE codigo_usuario = ?');
        $stmt->bind_param('s', $codigo_usuario);
        $stmt->execute();
        $stmt->bind_result($hash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($actual, $hash)) {
            $mensaje = 'La contraseña actual es incorrecta.';
            $tipo = 'danger';
        } else {
            $nuevoHash = password_hash($nuevo, PASSWORD_DEFAULT);
            $update = $conexion->prepare('UPDATE autenticacion SET password = ? WHERE codigo_usuario = ?');
            $update->bind_param('ss', $nuevoHash, $codigo_usuario);

            if ($update->execute()) {
                $mensaje = 'Contraseña actualizada correctamente.';
                $tipo = 'success';
            } else {
                $mensaje = 'No se pudo actualizar la contraseña. Intenta de nuevo.';
                $tipo = 'danger';
            }

            $update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Cambiar contraseña</h3>

                    <?php if ($mensaje !== ''): ?>
                        <div class="alert alert-<?php echo $tipo; ?>">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="password_actual" class="form-label">Contraseña actual</label>
                            <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_nuevo" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="password_nuevo" name="password_nuevo" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmar" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required>
                        </div>

                        <div class="text-end">
                            <a href="interfaz_usuario.php" class="btn btn-secondary me-2">Volver</a>
                            <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
