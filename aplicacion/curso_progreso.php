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
$progressItems = [
    ['id' => 'concepto', 'label' => 'Concepto', 'done' => true],
    ['id' => 'tipos', 'label' => 'Tipos', 'done' => true],
    ['id' => 'reconocer', 'label' => 'Reconocer', 'done' => true],
    ['id' => 'emociones', 'label' => 'Emociones', 'done' => true],
    ['id' => 'que_hacer', 'label' => '¿Qué hacer?', 'done' => false],
    ['id' => 'politicas', 'label' => 'Políticas y herramientas', 'done' => false],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso - <?php echo htmlspecialchars($course['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f2f4f7; }
        .page-header { background: #fff; border-bottom: 1px solid #ddd; padding: 20px 24px; margin-bottom: 24px; }
        .progress-card { border-radius: 18px; border: 1px solid #eaeaea; background: #fff; padding: 24px; }
        .progress-item { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px; border: 1px solid #e9ecef; border-radius: 12px; margin-bottom: 12px; }
        .progress-item.done { background: #e9f7ef; border-color: #c7eed2; }
        .progress-item.pending { background: #fff8db; border-color: #fae3a0; }
        .status-badge { min-width: 96px; text-align: center; }
        .page-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h1 class="h3 mb-1"><?php echo htmlspecialchars($course['title']); ?></h1>
            <p class="text-muted mb-0">Progreso del curso: marca lo hecho y revisa lo que falta.</p>
        </div>
        <div class="page-actions">
            <a href="curso.php?curso=<?php echo urlencode($courseId); ?>" class="btn btn-outline-secondary">Volver al curso</a>
            <a href="interfaz_usuario.php" class="btn btn-outline-secondary">Volver a cursos</a>
        </div>
    </div>

    <div class="progress-card">
        <h4 class="mb-3">Estado de avance</h4>
        <p class="text-muted">Los ítems con <strong>*</strong> están marcados como hechos. Los que faltan están pendientes.</p>

        <?php foreach ($progressItems as $item): ?>
            <div class="progress-item <?php echo $item['done'] ? 'done' : 'pending'; ?>" data-item="<?php echo htmlspecialchars($item['id']); ?>">
                <div>
                    <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                    <?php if ($item['done']): ?>
                        <span class="text-success d-block">Hecho *</span>
                    <?php else: ?>
                        <span class="text-warning d-block">Por hacer</span>
                    <?php endif; ?>
                </div>
                <div class="text-end">
                    <span class="badge status-badge <?php echo $item['done'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
                        <?php echo $item['done'] ? 'Hecho' : 'Pendiente'; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="alert alert-light mt-4">
            <strong>Consejo:</strong> al avanzar sobre los temas pendientes, refuerzas tu aprendizaje sobre <?php echo htmlspecialchars($course['title']); ?> y mejoras tu seguridad en línea.
        </div>
    </div>
</div>

</body>
</html>
