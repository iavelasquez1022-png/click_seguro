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
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $avatar = trim($_POST['avatar']);

    if ($nombres === '' || $apellidos === '' || $email === '' || $telefono === '' || $avatar === '') {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo = 'danger';
    } else {
        $check = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE (email = ? OR telefono = ?) AND codigo_usuario != ?");
        $check->bind_param('sss', $email, $telefono, $codigo_usuario);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensaje = 'El correo o teléfono ya están registrados por otro usuario.';
            $tipo = 'danger';
        } else {
            $stmt = $conexion->prepare("UPDATE usuarios SET nombres = ?, apellidos = ?, email = ?, telefono = ?, avatar = ? WHERE codigo_usuario = ?");
            $stmt->bind_param('ssssss', $nombres, $apellidos, $email, $telefono, $avatar, $codigo_usuario);

            if ($stmt->execute()) {
                $mensaje = 'Perfil actualizado correctamente.';
                $tipo = 'success';
                $_SESSION['avatar'] = $avatar;
            } else {
                $mensaje = 'No se pudo guardar el perfil. Intenta de nuevo.';
                $tipo = 'danger';
            }

            $stmt->close();
        }

        $check->close();
    }
}

$stmt = $conexion->prepare('SELECT id_usuario, nombres, apellidos, email, telefono, avatar FROM usuarios WHERE codigo_usuario = ?');
$stmt->bind_param('s', $codigo_usuario);
$stmt->execute();
$stmt->bind_result($id_usuario, $nombres, $apellidos, $email, $telefono, $avatar);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Actualizar perfil</h3>

                    <?php if ($mensaje !== ''): ?>
                        <div class="alert alert-<?php echo $tipo; ?>">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($nombres); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Avatar</label>
                                <div class="row g-2">
                                    <?php
                                    $avatars = ['avatar1.jpeg' => 'Cyber Fox', 'avatar2.jpeg' => 'Shield Bot', 'avatar3.jpeg' => 'Safe Ninja'];
                                    foreach ($avatars as $file => $label):
                                    ?>
                                        <div class="col-4 text-center">
                                            <label class="border rounded p-2 d-block" style="cursor:pointer;<?php echo $avatar === $file ? 'border-color:#0d6efd;' : ''; ?>">
                                                <input type="radio" name="avatar" value="<?php echo $file; ?>" <?php echo $avatar === $file ? 'checked' : ''; ?> hidden>
                                                <img src="img/<?php echo $file; ?>" class="img-fluid mb-2" style="height:100px; object-fit:cover;">
                                                <div><?php echo $label; ?></div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <a href="interfaz_usuario.php" class="btn btn-secondary me-2">Volver</a>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
