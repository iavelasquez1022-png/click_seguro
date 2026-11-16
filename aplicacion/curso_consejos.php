<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'conexion.php';
include 'course_data.php';

$courseId = trim($_GET['curso'] ?? '');
if (!$courseId || !isset($courses[$courseId])) {
    header('Location: interfaz_usuario.php');
    exit();
}

$course = $courses[$courseId];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consejos - <?php echo htmlspecialchars($course['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f2f4f7; }
        .page-header { background: #fff; border-bottom: 1px solid #ddd; padding: 20px 24px; margin-bottom: 24px; }
        .advice-card { border-radius: 18px; border: 1px solid #eaeaea; background: #fff; padding: 24px; }
        .advice-item { margin-bottom: 24px; }
        .advice-item:last-child { margin-bottom: 0; }
        .advice-item h5 { margin-bottom: 0.5rem; }
        .page-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h1 class="h3 mb-1">Consejos para <?php echo htmlspecialchars($course['title']); ?></h1>
            <p class="text-muted mb-0">Siete consejos prácticos relacionados con las áreas de seguridad del curso.</p>
        </div>
        <div class="page-actions">
            <a href="curso.php?curso=<?php echo urlencode($courseId); ?>" class="btn btn-outline-secondary">Volver al curso</a>
            <a href="interfaz_usuario.php" class="btn btn-outline-secondary">Volver a cursos</a>
        </div>
    </div>

    <div class="advice-card">
        <div class="advice-item">
            <h5>Evita</h5>
            <p>Evita caer en mensajes o sitios web que pidan información urgente sin verificar su origen. En <?php echo htmlspecialchars($course['title']); ?>, una revisión pausada te protege frente a trampas comunes.</p>
        </div>
        <div class="advice-item">
            <h5>Verifica</h5>
            <p>Verifica siempre el remitente real antes de responder o hacer clic. Un mensaje sospechoso en el contexto de <?php echo htmlspecialchars($course['title']); ?> puede ser la señal de un fraude en ciernes.</p>
        </div>
        <div class="advice-item">
            <h5>Revisa</h5>
            <p>Revisa los enlaces y la ortografía antes de compartir datos. En los materiales de <?php echo htmlspecialchars($course['title']); ?>, aprenderás a distinguir detalladamente los indicios de una estafa.</p>
        </div>
        <div class="advice-item">
            <h5>Mi contraseña</h5>
            <p>Mantén contraseñas únicas y seguras para cada cuenta. En <?php echo htmlspecialchars($course['title']); ?>, la protección de tus credenciales es clave para evitar pérdidas graves.</p>
        </div>
        <div class="advice-item">
            <h5>Seguridad</h5>
            <p>Activa mecanismos de seguridad adicionales como autenticación en dos pasos cuando sea posible. Esto refuerza lo que aprendes en <?php echo htmlspecialchars($course['title']); ?> y reduce el riesgo de acceso no autorizado.</p>
        </div>
        <div class="advice-item">
            <h5>Actualiza</h5>
            <p>Actualiza tu software y dispositivos para corregir vulnerabilidades conocidas. En el contexto de <?php echo htmlspecialchars($course['title']); ?>, una base actualizada es una defensa efectiva contra ataques modernos.</p>
        </div>
        <div class="advice-item">
            <h5>Reporta</h5>
            <p>Reporta cualquier actividad sospechosa a tu área de seguridad o proveedor. Los informes tempranos son valiosos; en <?php echo htmlspecialchars($course['title']); ?> aprenderás cómo convertirte en un usuario más consciente y protector.</p>
        </div>
    </div>
</div>
</body>
</html>
