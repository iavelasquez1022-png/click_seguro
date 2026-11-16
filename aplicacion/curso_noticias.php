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
$newsItems = [
    ['title' => 'IA', 'summary' => 'La inteligencia artificial sigue transformando la seguridad digital y la detección de fraudes en línea.', 'query' => 'Inteligencia artificial seguridad digital'],
    ['title' => 'La nube', 'summary' => 'Las empresas migran más servicios a la nube, lo que exige mejores controles de acceso y protección de datos.', 'query' => 'nube seguridad datos'],
    ['title' => 'Energía sostenible', 'summary' => 'La sostenibilidad impulsa nuevas soluciones tecnológicas, incluyendo centros de datos más eficientes y verdes.', 'query' => 'energía sostenible tecnología noticias'],
    ['title' => 'Realidad aumentada', 'summary' => 'La realidad aumentada se expande en educación y comercio, y también plantea nuevos retos de privacidad.', 'query' => 'realidad aumentada privacidad noticias'],
    ['title' => 'Smartphone', 'summary' => 'Los smartphones son puerta de entrada a datos personales críticos, por lo que su seguridad es cada vez más importante.', 'query' => 'smartphone seguridad noticias'],
    ['title' => 'Desarrolladores', 'summary' => 'Los desarrolladores están en el centro de la ciberseguridad, creando aplicaciones más seguras para usuarios y empresas.', 'query' => 'desarrolladores ciberseguridad noticias'],
    ['title' => 'IoT', 'summary' => 'El internet de las cosas crece con nuevos dispositivos conectados, y también con más vectores de ataque que debemos proteger.', 'query' => 'IoT seguridad noticias'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - <?php echo htmlspecialchars($course['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f2f4f7; }
        .page-header { background: #fff; border-bottom: 1px solid #ddd; padding: 20px 24px; margin-bottom: 24px; }
        .news-card { border-radius: 18px; border: 1px solid #eaeaea; background: #fff; padding: 24px; }
        .news-item { margin-bottom: 24px; }
        .news-item:last-child { margin-bottom: 0; }
        .news-item h5 { margin-bottom: 0.5rem; }
        .news-link { text-decoration: none; }
        .page-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h1 class="h3 mb-1">Noticias relacionadas</h1>
            <p class="text-muted mb-0">Resumenes de noticias basados en los temas mencionados en el curso.</p>
        </div>
        <div class="page-actions">
            <a href="curso.php?curso=<?php echo urlencode($courseId); ?>" class="btn btn-outline-secondary">Volver al curso</a>
            <a href="interfaz_usuario.php" class="btn btn-outline-secondary">Volver a cursos</a>
        </div>
    </div>

    <div class="news-card">
        <?php foreach ($newsItems as $item): ?>
            <div class="news-item">
                <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                <p><?php echo htmlspecialchars($item['summary']); ?></p>
                <a class="news-link" href="https://news.google.com/search?q=<?php echo urlencode($item['query']); ?>" target="_blank" rel="noopener">Ver más en Google Noticias</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
