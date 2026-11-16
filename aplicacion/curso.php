<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'conexion.php';
include 'course_data.php';

$codigo_usuario = $_SESSION['codigo_usuario'];
$stmt = $conexion->prepare('SELECT id_usuario, nombres, apellidos, email, telefono, avatar FROM usuarios WHERE codigo_usuario = ?');
$stmt->bind_param('s', $codigo_usuario);
$stmt->execute();
$stmt->bind_result($id_usuario, $nombres, $apellidos, $email, $telefono, $avatar);
$stmt->fetch();
$stmt->close();

$avatar_path = 'img/' . trim($avatar);
$courseId = trim($_GET['curso'] ?? '');
if (!$courseId || !isset($courses[$courseId])) {
    header('Location: interfaz_usuario.php');
    exit();
}

$course = $courses[$courseId];

$communityUsers = [];
$stmt = $conexion->prepare('SELECT nombres, apellidos, avatar FROM usuarios WHERE codigo_usuario <> ? LIMIT 6');
$stmt->bind_param('s', $codigo_usuario);
$stmt->execute();
$stmt->bind_result($communityNombre, $communityApellido, $communityAvatar);
while ($stmt->fetch()) {
    $communityUsers[] = [
        'name' => trim($communityNombre . ' ' . $communityApellido),
        'avatar' => 'img/' . trim($communityAvatar),
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { background-color: #f2f4f7; }
        .header-page { padding: 20px 24px; background: #fff; border-bottom: 1px solid #ddd; }
        .course-hero { padding: 36px 24px; background: #fff; border-radius: 18px; box-shadow: 0 8px 22px rgba(0,0,0,.06); margin-bottom: 24px; }
        .course-hero h1 { font-size: 2.8rem; margin-bottom: 0.3rem; }
        .course-hero p.subtitle { color: #6c757d; margin-bottom: 1rem; }
        .course-content { display: grid; gap: 24px; grid-template-columns: 1.5fr 1fr; }
        .course-card { border-radius: 18px; border: 1px solid #eaeaea; background: #fff; padding: 24px; }
        .course-card h5 { margin-bottom: 0.75rem; }
        .activity-box { background: #f8f9fa; border-radius: 14px; padding: 20px; }
        .btn-back { min-width: 120px; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .top-nav .nav-links { display: flex; gap: 10px; flex-wrap: wrap; }
        .top-nav .nav-links a { color: #212529; text-decoration: none; font-weight: 600; }
        .top-nav .nav-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .progress-label { color: #495057; }
        /* Guide search styles */
        #guideSearchContainer { display: none; margin-bottom: 12px; }
        #guideSearchResults .list-group-item { cursor: pointer; }
        #guideSearchResults .course-result { display: flex; align-items: center; gap: 12px; }
        #guideSearchResults .course-result img { width: 56px; height: 40px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="header-page mb-4 py-3 px-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <a href="#" id="btnCourses" class="fs-3 fw-bold text-decoration-none text-secondary" style="letter-spacing: -0.03em;">Cursos</a>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap" style="justify-content: center;">
                <button id="btnGuide" class="btn btn-sm btn-outline-secondary" style="border-radius: 999px;">Guía</button>
                <a href="curso_consejos.php?curso=<?php echo urlencode($courseId); ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 999px;">Consejos</a>
                <a href="#communitySection" class="btn btn-sm btn-outline-secondary" style="border-radius: 999px;">Comunidad</a>
                <a href="curso_noticias.php?curso=<?php echo urlencode($courseId); ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 999px;">Noticias</a>
                <a href="curso_progreso.php?curso=<?php echo urlencode($courseId); ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 999px;">Progreso</a>
                <a href="#contactSection" class="btn btn-sm btn-outline-secondary" style="border-radius: 999px;">Contacto</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="interfaz_usuario.php" class="btn btn-sm btn-outline-secondary">Atrás</a>
                <button id="btnSafeMode" class="btn btn-sm btn-dark">✔ Click Seguro</button>
            </div>
        </div>
    </div>

    <div id="guideSearchContainer" class="container py-2" style="display:none;">
        <div class="input-group">
            <input id="guideSearchInput" type="search" class="form-control" placeholder="Buscar cursos por nombre..." aria-label="Buscar">
            <button id="guideSearchClear" class="btn btn-outline-secondary" type="button">✕</button>
        </div>
        <div id="guideSearchResults" class="list-group mt-2"></div>
    </div>

    <div id="modalOverlay" class="modal-overlay" style="position: fixed; inset: 0; background: rgba(0,0,0,.55); display: none; align-items: center; justify-content: center; z-index: 2000;">
        <div class="modal-box" style="background: #fff; border-radius: 14px; width: min(600px, 90%); max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,.2); padding: 24px; position: relative;">
            <button class="modal-close" id="closeModal" style="position: absolute; right: 18px; top: 18px; border: none; background: transparent; font-size: 1.2rem; cursor: pointer;">×</button>
            <div id="modalContent"></div>
        </div>
    </div>

    <div class="course-hero text-center py-5 mb-4" style="background: #f8f9f9; border-radius: 18px;">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($course['title']); ?></h1>
        <p class="subtitle" style="font-size: 1.1rem; color: #8a8a8a; margin-bottom: 1.5rem;"><?php echo htmlspecialchars($course['subtitle']); ?></p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="<?php echo htmlspecialchars(preg_replace('/^Fuente:\s*/i','', $course['videoSource'])); ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary px-4">Recursos</a>
            <a href="<?php echo htmlspecialchars($course['videoUrl']); ?>" target="_blank" rel="noopener" class="btn btn-dark px-4">Disponibles</a>
        </div>
    </div>

    <div class="course-content">
        <div>
            <div class="course-card mb-4" id="available">
                <h5>Teoría y Contenido</h5>
                <p><?php echo htmlspecialchars($course['details']); ?></p>
                <div class="ratio ratio-16x9 mt-3">
                    <iframe src="<?php echo htmlspecialchars($course['videoUrl']); ?>" title="Video de <?php echo htmlspecialchars($course['title']); ?>" allowfullscreen></iframe>
                </div>
                <p id="resources" class="mt-2 text-muted"><?php echo htmlspecialchars($course['videoSource']); ?></p>    
            </div>

            <div class="course-card activity-box" id="activitySection"></div>
        </div>

        <aside class="course-card">
            <h5>Estado del Curso</h5>
            <p class="progress-label">Progreso actual</p>
            <div class="progress mb-3Email" style="height: 18px;">
                <div id="courseProgressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p><strong id="progressText">0%</strong></p>
            <p id="levelText" class="text-muted">Nivel inicial</p>
            <div class="mt-4">
                <a href="interfaz_usuario.php" class="btn btn-outline-primary w-100 mb-3">Volver a Cursos</a>
                <div class="text-center">
                    <strong class="d-block mb-3">Síguenos</strong>
                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <a href="https://twitter.com" target="_blank" title="Twitter" style="font-size: 24px; text-decoration: none;">𝕏</a>
                        <a href="https://youtube.com" target="_blank" title="YouTube" style="font-size: 24px; text-decoration: none;">▶</a>
                        <a href="https://instagram.com" target="_blank" title="Instagram" style="font-size: 24px; text-decoration: none;">📷</a>
                        <a href="https://linkedin.com" target="_blank" title="LinkedIn" style="font-size: 24px; text-decoration: none;">in</a>
                    </div>
                </div>
                    <div class="mt-4 pt-4 border-top" id="contactSection">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong>Contactos</strong>
                    </div>
                    <div style="display: grid; gap: 10px;">
                        <button type="button" class="btn btn-outline-primary contact-btn-course text-start" onclick="showContactCourse('whatsapp')">WhatsApp</button>
                        <button type="button" class="btn btn-outline-primary contact-btn-course text-start" onclick="showContactCourse('email')">Email</button>
                        <button type="button" class="btn btn-outline-primary contact-btn-course text-start" onclick="showContactCourse('callcenter')">Call Center</button>
                    </div>
                    <div id="contactInfoCourse" class="mt-3 p-2" style="background: #f8f9fa; border-radius: 8px; display: none;">
                        <div id="contactWhatsappInfoCourse" style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #dee2e6; display: none;">
                            <small style="color: #6c757d;"><strong>WhatsApp:</strong></small><br>
                            <small style="word-break: break-all;">+57 313 363 7818</small>
                        </div>
                        <div id="contactEmailInfoCourse" style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #dee2e6; display: none;">
                            <small style="color: #6c757d;"><strong>Email:</strong></small><br>
                            <small style="word-break: break-all;">1010078559@teinco.edu.co</small>
                        </div>
                        <div id="contactCallcenterInfoCourse" style="margin-bottom: 0; display: none;">
                            <small style="color: #6c757d;"><strong>Call Center:</strong></small><br>
                            <small style="word-break: break-all;">+57 350 6017348</small>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-top" id="communitySection">
                    <div class="mb-3">
                        <strong>Comunidad</strong>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px;">
                        <?php foreach ($communityUsers as $user): ?>
                            <div style="background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; padding: 18px 14px; text-align: center;">
                                <div style="font-weight: 700; font-size: 1rem; margin-bottom: 8px;"><?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?></div>
                                <div style="color: #f5b301; letter-spacing: 0.05em;">★★★★★</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-top" id="progressSection">
                    <div class="row text-start">
                        <div class="col-12 mb-3">
                            <strong>Progress</strong>
                        </div>
                        <div class="col-4">
                            <p class="mb-1"><strong>Progreso</strong></p>
                            <p class="mb-1">Concepto *</p>
                            <p class="mb-1">Tipos *</p>
                            <p class="mb-1">Reconocer *</p>
                            <p class="mb-1">Emociones *</p>
                            <p class="mb-1">¿Qué hacer?</p>
                            <p class="mb-1">Políticas y herramientas</p>
                        </div>
                        <div class="col-4" id="adviceSection">
                            <p class="mb-1"><strong>Consejos</strong></p>
                            <p class="mb-1">Evita</p>
                            <p class="mb-1">Verifica</p>
                            <p class="mb-1">Revisa</p>
                            <p class="mb-1">Mi contraseña</p>
                            <p class="mb-1">Seguridad</p>
                            <p class="mb-1">Actualiza</p>
                            <p class="mb-1">Reporta</p>
                        </div>
                        <div class="col-4" id="newsSection">
                            <p class="mb-1"><strong>Noticias</strong></p>
                            <p class="mb-1">IA</p>
                            <p class="mb-1">La nube</p>
                            <p class="mb-1">Energía sostenible</p>
                            <p class="mb-1">Realidad aumentada</p>
                            <p class="mb-1">Smartphone</p>
                            <p class="mb-1">Desarrolladores</p>
                            <p class="mb-1">IoT</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
const course = <?php echo json_encode($course, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const progressBar = document.getElementById('courseProgressBar');
const progressText = document.getElementById('progressText');
const levelText = document.getElementById('levelText');
const modalOverlay = document.getElementById('modalOverlay');
const modalContent = document.getElementById('modalContent');
const btnCourses = document.getElementById('btnCourses');
const btnSafeMode = document.getElementById('btnSafeMode');
const closeModal = document.getElementById('closeModal');
const activitySection = document.getElementById('activitySection');
console.log('curso page script loaded');
const guideSearchContainer = document.getElementById('guideSearchContainer');
const guideSearchInput = document.getElementById('guideSearchInput');
const guideSearchResults = document.getElementById('guideSearchResults');
const guideSearchClear = document.getElementById('guideSearchClear');
const allCourses = <?php echo json_encode(array_values($courses), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

if (guideSearchContainer && guideSearchInput && guideSearchResults && guideSearchClear) {
    document.getElementById('btnGuide').addEventListener('click', () => {
        const isVisible = guideSearchContainer.style.display === 'block';
        guideSearchContainer.style.display = isVisible ? 'none' : 'block';
        if (!isVisible) {
            guideSearchInput.focus();
            renderGuideSearch('');
        }
    });
    guideSearchClear.addEventListener('click', () => { guideSearchInput.value = ''; renderGuideSearch(''); guideSearchInput.focus(); });
    guideSearchInput.addEventListener('input', () => renderGuideSearch(guideSearchInput.value));
}

function renderGuideSearch(query) {
    const q = (query || '').trim().toLowerCase();
    guideSearchResults.innerHTML = '';
    const filtered = q ? allCourses.filter(c => (c.title || '').toLowerCase().includes(q)) : allCourses;
    if (!filtered.length) {
        guideSearchResults.innerHTML = '<div class="list-group-item">No se encontraron cursos.</div>';
        return;
    }
    filtered.forEach(c => {
        const item = document.createElement('a');
        item.className = 'list-group-item list-group-item-action';
        item.href = 'curso.php?curso=' + encodeURIComponent(c.id);
        item.innerHTML = '<strong>' + c.title + '</strong><div class="text-muted small">' + (c.description || '') + '</div>';
        guideSearchResults.appendChild(item);
    });
}

const activitySets = {
    phishing: [
        course.activity,
        {
            title: 'Actividad avanzada de phishing',
            instructions: 'Resuelve este segundo conjunto de pistas para seguir avanzando.',
            clues: [
                { id: 'x1', clue: 'Correo que parece legítimo pero no lo es', answer: 'phishing' },
                { id: 'x2', clue: 'Página falsa que copia a la original', answer: 'fraude' },
                { id: 'x3', clue: 'Información de acceso secreta', answer: 'password' }
            ]
        }
    ],
    smishing: [
        course.activity,
        {
            title: 'Actividad avanzada de smishing',
            instructions: 'Continúa con otro reto para reforzar tu aprendizaje.',
            clues: [
                { id: 'x1', clue: 'Mensaje falso que llegó al teléfono', answer: 'sms' },
                { id: 'x2', clue: 'Solicita tus datos bancarios', answer: 'fraude' },
                { id: 'x3', clue: 'No abras enlaces sospechosos', answer: 'cuidado' }
            ]
        }
    ],
    vishing: [
        course.activity,
        {
            title: 'Actividad avanzada de vishing',
            instructions: 'Identifica estas señales durante una llamada sospechosa.',
            clues: [
                { id: 'x1', clue: 'Llamada que pide datos personales', answer: 'vishing' },
                { id: 'x2', clue: 'Imita a una empresa real', answer: 'suplantacion' },
                { id: 'x3', clue: 'Pide tu contraseña o PIN', answer: 'clave' }
            ]
        }
    ],
    shopping: [
        course.activity,
        {
            title: 'Actividad avanzada de compras',
            instructions: 'Resuelve este nuevo conjunto para mejorar tu seguridad en compras.',
            clues: [
                { id: 'x1', clue: 'Sitio debe tener este protocolo seguro', answer: 'https' },
                { id: 'x2', clue: 'Método de pago protegido', answer: 'tarjeta' },
                { id: 'x3', clue: 'Es mejor comprar en una tienda con este sello', answer: 'certificado' }
            ]
        }
    ],
    ransomware: [
        course.activity,
        {
            title: 'Actividad avanzada de ransomware',
            instructions: 'Sigue con otra prueba sobre cómo reaccionar ante un ataque.',
            clues: [
                { id: 'x1', clue: 'Bloquea archivos para pedir rescate', answer: 'ransomware' },
                { id: 'x2', clue: 'No pagues, busca esta alternativa', answer: 'backup' },
                { id: 'x3', clue: 'Software que protege del ataque', answer: 'antivirus' }
            ]
        }
    ],
    simulators: [
        course.activity,
        {
            title: 'Actividad avanzada de simuladores',
            instructions: 'Responde este siguiente reto para completar el ejercicio.',
            clues: [
                { id: 'x1', clue: 'Entrena con un modelo de ataque', answer: 'simulador' },
                { id: 'x2', clue: 'Prueba tus defensas en un entorno', answer: 'ensayo' },
                { id: 'x3', clue: 'Herramienta para aprender sin riesgo', answer: 'practica' }
            ]
        }
    ]
};

let currentActivityIndex = 0;

function getCurrentActivity() {
    const set = activitySets[course.id];
    return set && set[currentActivityIndex] ? set[currentActivityIndex] : course.activity;
}

function saveActivityState() {
    const saved = localStorage.getItem('courseActivityState');
    const state = saved ? JSON.parse(saved) : {};
    state[course.id] = currentActivityIndex;
    localStorage.setItem('courseActivityState', JSON.stringify(state));
}

function restoreActivityState() {
    const saved = localStorage.getItem('courseActivityState');
    if (!saved) return;
    const state = JSON.parse(saved);
    if (typeof state[course.id] === 'number') {
        currentActivityIndex = state[course.id];
    }
}

function renderActivitySection() {
    const activity = getCurrentActivity();
    activitySection.innerHTML = `
        <h5>${activity.title}</h5>
        <p>${activity.instructions}</p>
        <form id="activityForm" onsubmit="return false;">
            ${activity.clues.map((clue, index) => `
                <div class="mb-3">
                    <label class="form-label"><strong>${index + 1}. ${clue.clue}</strong></label>
                    <input type="text" id="activity-${clue.id}" class="form-control" maxlength="20" placeholder="Escribe la palabra" autocomplete="off">
                </div>
            `).join('')}
            <button id="btnCheckActivity" class="btn btn-success">Comprobar Actividad</button>
        </form>
    `;
    document.getElementById('btnCheckActivity').addEventListener('click', handleActivitySubmit);
}

function handleActivitySubmit() {
    const activity = getCurrentActivity();
    const answers = activity.clues.map(clue => {
        const input = document.getElementById(`activity-${clue.id}`);
        return input ? input.value.trim().toLowerCase() : '';
    });
    const allCorrect = activity.clues.every((clue, index) => answers[index] === clue.answer.toLowerCase());
    if (!allCorrect) {
        alert('Algunas respuestas no son correctas. Revisa las pistas y vuelve a intentarlo.');
        return;
    }
    course.progress = Math.min(100, course.progress + 20);
    saveProgress();
    setProgress(course.progress);
    if (activitySets[course.id] && currentActivityIndex < activitySets[course.id].length - 1) {
        currentActivityIndex += 1;
        saveActivityState();
        renderActivitySection();
        alert('¡Actividad completada! Has desbloqueado la siguiente actividad.');
        return;
    }
    alert('¡Actividad completada! Curso finalizado. Continúa practicando.');
}

function getLevelLabel(progress) {
    if (progress >= 100) return 'Nivel completado';
    if (progress >= 75) return 'Avanzado';
    if (progress >= 40) return 'Intermedio';
    return 'Inicial';
}

function setProgress(progress) {
    const value = Math.min(100, Math.max(0, progress));
    progressBar.style.width = `${value}%`;
    progressBar.setAttribute('aria-valuenow', value);
    progressText.textContent = `${value}%`;
    levelText.textContent = getLevelLabel(value);
}

function saveProgress() {
    const saved = localStorage.getItem('courseProgressState');
    const state = saved ? JSON.parse(saved) : {};
    state[course.id] = course.progress;
    localStorage.setItem('courseProgressState', JSON.stringify(state));
}

function restoreProgress() {
    const saved = localStorage.getItem('courseProgressState');
    if (!saved) return;
    const state = JSON.parse(saved);
    if (typeof state[course.id] === 'number') {
        course.progress = state[course.id];
    }
}

function openModal(html) {
    modalContent.innerHTML = html;
    modalOverlay.style.display = 'flex';
}

function closeModalFunc() {
    modalOverlay.style.display = 'none';
    modalContent.innerHTML = '';
}

const coursesList = <?php echo json_encode(array_map(function($c) { return ['id' => $c['id'], 'title' => $c['title'], 'progress' => $c['progress']]; }, array_values($courses)), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

btnCourses.addEventListener('click', (e) => {
    e.preventDefault();
    const html = `
        <h5>Sección de Cursos</h5>
        <p>Haz clic en cualquier curso para acceder a la página correspondiente.</p>
        <ul style="list-style: none; padding: 0;">
            ${coursesList.map(c => `<li style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>${c.title}</strong> — ${c.progress}% completado <a href="curso.php?curso=${c.id}" style="float: right;">Ir →</a></li>`).join('')}
        </ul>
        <button class="btn btn-secondary mt-3" onclick="document.getElementById('closeModal').click()">Cerrar</button>
    `;
    openModal(html);
});

btnSafeMode.addEventListener('click', () => {
    window.location.href = 'interfaz_usuario.php';
});

modalOverlay.addEventListener('click', event => {
    if (event.target === modalOverlay) closeModalFunc();
});

closeModal.addEventListener('click', closeModalFunc);

// Contact display handler for course page (button-driven)
function showContactCourse(type) {
    const map = {
        whatsapp: 'contactWhatsappInfoCourse',
        email: 'contactEmailInfoCourse',
        callcenter: 'contactCallcenterInfoCourse'
    };
    const container = document.getElementById('contactInfoCourse');
    if (!container) return;
    const current = container.dataset.current;
    const id = map[type];
    if (!id) return;
    if (current === type) {
        container.style.display = 'none';
        delete container.dataset.current;
        return;
    }
    const ids = ['contactWhatsappInfoCourse','contactEmailInfoCourse','contactCallcenterInfoCourse'];
    ids.forEach(i => {
        const el = document.getElementById(i);
        if (el) el.style.display = 'none';
    });
    const el = document.getElementById(id);
    if (el) el.style.display = 'block';
    container.dataset.current = type;
    container.style.display = 'block';
}

restoreProgress();
restoreActivityState();
renderActivitySection();
// ensure contact panel hidden initially
const contactCourseEl = document.getElementById('contactInfoCourse');
if (contactCourseEl) contactCourseEl.style.display = 'none';
setProgress(course.progress);
</script>
</body>
</html>
