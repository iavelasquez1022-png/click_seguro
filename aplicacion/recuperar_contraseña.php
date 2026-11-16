<?php
include 'conexion.php';
$mensaje = '';
$tipo = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if ($email === '') {
        $mensaje = 'Ingresa un correo electrónico válido.';
        $tipo = 'danger';
    } else {
        $stmt = $conexion->prepare('SELECT codigo_usuario FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = 'Hemos enviado un enlace para recuperar tu contraseña al correo: ' . htmlspecialchars($email) . '. Comprueba tu bandeja de entrada.';
            $tipo = 'success';
        } else {
            $mensaje = 'No encontramos un usuario con ese correo electrónico.';
            $tipo = 'danger';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Recuperar contraseña</h3>

                    <?php if ($mensaje !== ''): ?>
                        <div class="alert alert-<?php echo $tipo; ?>">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico asociado</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="login.php" class="btn btn-secondary">Volver</a>
                            <button type="submit" class="btn btn-primary">Enviar enlace</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
