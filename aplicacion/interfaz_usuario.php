<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$codigo_usuario = $_SESSION['codigo_usuario'];
$stmt = $conexion->prepare("SELECT id_usuario, nombres, apellidos, email, telefono, avatar FROM usuarios WHERE codigo_usuario = ?");
$stmt->bind_param("s", $codigo_usuario);
$stmt->execute();
$stmt->bind_result($id_usuario, $nombres, $apellidos, $email, $telefono, $avatar);
$stmt->fetch();
$stmt->close();

$avatar_path = 'img/' . trim($avatar);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Usuario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f5f5f5;
}
.sidebar {
    width: 280px;
    min-height: 100vh;
    position: fixed;
    background: #fff;
    padding: 22px;
    border-right: 1px solid #ddd;
}
.content {
    margin-left: 300px;
    padding: 24px;
}
.card-img-top {
    height: 220px;
    object-fit: cover;
}
.profile-box {
    text-align: center;
}
.profile-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    margin: auto;
    border: 3px solid #0d6efd;
    display: block;
}
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.55);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}
.modal-box {
    background: #fff;
    border-radius: 14px;
    width: min(600px, 90%);
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 60px rgba(0,0,0,.2);
    padding: 24px;
}
.modal-box h5 {
    margin-bottom: 16px;
}
.modal-close {
    position: absolute;
    right: 18px;
    top: 18px;
    border: none;
    background: transparent;
    font-size: 1.2rem;
    cursor: pointer;
}
.course-card {
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
}
.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(0,0,0,.12);
}
.small-note {
    color: #6c757d;
}
.toast-notification {
    position: fixed;
    right: 24px;
    bottom: 24px;
    background: #212529;
    color: #fff;
    padding: 14px 18px;
    border-radius: 10px;
    opacity: 0;
    transition: opacity .25s ease;
    z-index: 2100;
}
.toast-notification.show {
    opacity: 1;
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="profile-box mb-4">
        <img src="<?php echo htmlspecialchars($avatar_path); ?>"
             class="profile-img rounded-circle mb-2" alt="Avatar">

        <strong><?php echo htmlspecialchars($_SESSION['codigo_usuario']); ?></strong>
    </div>

    <button id="btnUpdate" class="btn btn-outline-primary w-100 mb-2">Actualizar perfil</button>
    <button id="btnPassword" class="btn btn-outline-secondary w-100 mb-3">Cambiar contraseña</button>

    <button id="btnCourses" class="btn btn-light w-100 text-start mb-2">Cursos<br><small class="small-note">Ver lista y detalles</small></button>
    <button id="btnConfig" class="btn btn-light w-100 text-start mb-3">Configuración<br><small class="small-note">Ajusta tus preferencias</small></button>
  

    

    <div id="progressSection" class="mt-4">
        <strong>Progreso general</strong>
        <div class="progress mt-3" style="height: 10px;">
            <div id="overallProgress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <small id="currentProgressValue" class="d-block mt-2">0%</small>
    </div>

    <div id="contactSection" class="mt-4">
        <div class="d-flex align-items-center justify-content-between">
            <strong>Contactos</strong>
        </div>
        <div class="d-grid gap-2 mt-2">
            <button type="button" class="btn btn-outline-primary contact-btn text-start" onclick="showContact('whatsapp')">Whatsapp</button>
            <button type="button" class="btn btn-outline-primary contact-btn text-start" onclick="showContact('email')">Email</button>
            <button type="button" class="btn btn-outline-primary contact-btn text-start" onclick="showContact('callcenter')">Call Center</button>
        </div>
        <div id="contactInfo" class="mt-3 p-2" style="background: #f8f9fa; border-radius: 8px; display: none;"></div>
    </div>

    <div class="mt-4">
        <strong>Compartir</strong>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="shareLevel">
            <label class="form-check-label" for="shareLevel">Level</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="shareCertificates">
            <label class="form-check-label" for="shareCertificates">Certificates</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="shareProgress">
            <label class="form-check-label" for="shareProgress">Progreso</label>
        </div>
    </div>

    <button id="savePreferences" class="btn btn-success w-100 mt-4">Guardar preferencias</button>
    <a href="logout.php" class="btn btn-outline-danger w-100 mt-2">Cerrar sesión</a>
</div>

<div class="content">
    <div class="d-flex flex-wrap gap-2 mb-3">

    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <input type="text" id="searchCourses" class="form-control w-100 w-md-50 d-none" placeholder="Buscar curso...">

        <div class="d-flex gap-2 flex-wrap">
            <button id="btnSafeMode" class="btn btn-dark">✔ Click Seguro</button>
            <button id="btnExplore" class="btn btn-light">Explorar</button>
            <a href="logout.php" class="btn btn-light">Cerrar sesión</a>
        </div>
    </div>

    <div id="explorePanel" class="alert alert-info d-none">
        Bienvenido a la zona de exploración. Haz clic en cualquier tarjeta para ver más detalles del curso y ajustar tu progreso.
    </div>

    <div id="guideSection">
        <div class="row g-4" id="cardsContainer">
        <?php
        $courses = [
            ['id' => 'phishing', 'title' => 'Phishing', 'progress' => 0, 'image' => 'img/phishing.jpeg', 'description' => 'Aprende a reconocer correos y sitios falsos.'],
            ['id' => 'smishing', 'title' => 'Smishing', 'progress' => 15, 'image' => 'img/smishing.jpg', 'description' => 'Protege tu teléfono de fraudes por SMS.'],
            ['id' => 'vishing', 'title' => 'Vishing', 'progress' => 79, 'image' => 'img/vishing.jpg', 'description' => 'Detecta llamadas fraudulentas y protege tu voz.' ],
            ['id' => 'shopping', 'title' => 'Online Shopping', 'progress' => 0, 'image' => 'img/shop.jpg','description' => 'Compras seguras en línea y cómo evitar estafas.' ],
            ['id' => 'ransomware', 'title' => 'Ransomware', 'progress' => 85, 'image' => 'img/ramso.jpeg','description' => 'Entiende el ransomware y cómo proteger tu equipo.' ],
            ['id' => 'simulators', 'title' => 'Simulators', 'progress' => 33, 'image' => 'img/simula.webp','description' => 'Practica con simuladores de ataques y defensas.' ],
        ];

        foreach ($courses as $course): ?>
            <div class="col-md-4">
                <div class="card course-card" data-course-id="<?php echo $course['id']; ?>" data-course-title="<?php echo htmlspecialchars(strtolower($course['title'])); ?>" data-course-progress="<?php echo $course['progress']; ?>" onclick="window.location.href='curso.php?curso=<?php echo $course['id']; ?>';" style="cursor:pointer;">
                    <img src="<?php echo $course['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>">
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                        <p class="course-percentage mb-2"><?php echo $course['progress']; ?>%</p>
                        <button class="btn btn-sm btn-primary btn-detail" onclick="event.stopPropagation(); window.location.href='curso.php?curso=<?php echo $course['id']; ?>';">Ver detalle</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <div class="row g-4 mt-4">
        <div class="col-lg-8" id="adviceSection">
            <div class="card p-4">
                <h5>Consejos</h5>
                <p>Encuentra ideas y recomendaciones para mejorar tu seguridad digital y aprender sobre fraudes comunes.</p>
                <ul>
                    <li>Revisa siempre la dirección del remitente antes de abrir un enlace.</li>
                    <li>No compartas contraseñas ni códigos de verificación.</li>
                    <li>Activa la verificación en dos pasos cuando esté disponible.</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4" id="newsSection">
            <div class="card p-4">
                <h5>Noticias</h5>
                <p>Actualizaciones recientes y tendencias de seguridad para mantenerte informado.</p>
                <ul>
                    <li>Nuevo ataque de phishing dirigido a empresas locales.</li>
                    <li>Aumento de fraudes por WhatsApp en 2026.</li>
                    <li>Recomendaciones para proteger tus cuentas en redes sociales.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="modalOverlay" class="modal-overlay">
    <div class="modal-box position-relative">
        <button class="modal-close" id="closeModal" aria-label="Cerrar">×</button>
        <div id="modalContent"></div>
    </div>
</div>

<div id="toast" class="toast-notification"></div>

<script>
const courses = [
    {
        id: 'phishing',
        title: 'Phishing',
        description: 'Aprende a reconocer correos y sitios falsos.',
        progress: 0,
        details: 'Este curso profundiza en las técnicas más comunes de phishing, cómo identificarlas y cómo proteger tu información personal y profesional.',
        videoUrl: 'https://www.youtube.com/embed/A1QYl0KMVk0',
        videoSource: 'Fuente: https://es.wikipedia.org/wiki/Phishing',
        activity: {
            title: 'Crucigrama de términos de seguridad',
            instructions: 'Completa las palabras clave relacionadas con la seguridad digital. Al terminar, tu progreso se actualizará automáticamente.',
            clues: [
                { id: 'w1', clue: 'Técnica usada para robar credenciales mediante correos falsos', answer: 'phishing' },
                { id: 'w2', clue: 'Falso mensaje por SMS para engañar al usuario', answer: 'smishing' },
                { id: 'w3', clue: 'Software malicioso que secuestra archivos para pedir rescate', answer: 'ransomware' }
            ]
        }
    },
    {
        id: 'smishing',
        title: 'Smishing',
        description: 'Protege tu teléfono de fraudes por SMS.',
        progress: 15,
        details: 'En este curso conocerás señales de smishing y cómo evitar enlaces maliciosos en mensajes de texto.',
        videoUrl: 'https://www.youtube.com/embed/UdAf2D2t2Ik',
        videoSource: 'Fuente: https://es.wikipedia.org/wiki/Smishing',
        activity: {
            title: 'Cuestionario interactivo',
            instructions: 'Responde las preguntas y completa las palabras a continuación para avanzar en el curso.',
            clues: [
                { id: 'w1', clue: 'Mensaje corto que busca engañarte', answer: 'sms' },
                { id: 'w2', clue: 'Uso de mensajes falsos para robar datos', answer: 'phishing' },
                { id: 'w3', clue: 'Protege tu teléfono con este tipo de código', answer: 'pin' }
            ]
        }
    },
    {
        id: 'vishing',
        title: 'Vishing',
        description: 'Detecta llamadas fraudulentas y protege tu voz.',
        progress: 79,
        details: 'Aprende a identificar llamadas de vishing y a mantener tus datos seguros frente a ataques telefónicos.',
        videoUrl: 'https://www.youtube.com/embed/tD5qZ8DL2cY',
        videoSource: 'Fuente: https://es.wikipedia.org/wiki/Vishing',
        activity: {
            title: 'Actividad de reconocimiento',
            instructions: 'Completa las pistas sobre cómo detectar una llamada fraudulenta.',
            clues: [
                { id: 'w1', clue: 'Llamadas que buscan tu contraseña o datos bancarios', answer: 'vishing' },
                { id: 'w2', clue: 'Confirma siempre la identidad antes de dar datos', answer: 'verificar' },
                { id: 'w3', clue: 'No compartas tu número de tarjeta ni este dato', answer: 'clave' }
            ]
        }
    },
    {
        id: 'shopping',
        title: 'Online Shopping',
        description: 'Compras seguras en línea y cómo evitar estafas.',
        progress: 0,
        details: 'Con este curso aprenderás a verificar tiendas, métodos de pago seguros y prácticas para comprar sin riesgos.',
        videoUrl: 'https://www.youtube.com/embed/0sJlczT9ZkQ',
        videoSource: 'Fuente: https://es.wikipedia.org/wiki/Comercio_electr%C3%B3nico',
        activity: {
            title: 'Crucigrama de comercio seguro',
            instructions: 'Escribe las palabras correctas de seguridad que te ayudarán a hacer compras confiables.',
            clues: [
                { id: 'w1', clue: 'Protocolo seguro en URLs', answer: 'https' },
                { id: 'w2', clue: 'Sitio con candado en el navegador', answer: 'seguro' },
                { id: 'w3', clue: 'Evita pagar en una web sin esta señal', answer: 'certificado' }
            ]
        }
    },
    {
        id: 'ransomware',
        title: 'Ransomware',
        description: 'Entiende el ransomware y cómo proteger tu equipo.',
        progress: 85,
        details: 'Descubre cómo funciona el ransomware, qué medidas tomar antes y después del ataque y cómo recuperar tu información.',
        videoUrl: 'https://www.youtube.com/embed/5KLbnZJZBUA',
        videoSource: 'Fuente: https://es.wikipedia.org/wiki/Ransomware',
        activity: {
            title: 'Puzzle de seguridad',
            instructions: 'Resuelve las pistas para reforzar tu conocimiento sobre ransomware.',
            clues: [
                { id: 'w1', clue: 'Archivo que queda bloqueado por malware', answer: 'archivo' },
                { id: 'w2', clue: 'Pago que solicita el atacante', answer: 'rescate' },
                { id: 'w3', clue: 'Haz copias de estos para no perder datos', answer: 'backup' }
            ]
        }
    },
    {
        id: 'simulators',
        title: 'Simulators',
        description: 'Practica con simuladores de ataques y defensas.',
        progress: 33,
        details: 'Este curso te permite practicar escenarios reales de ataques y ver en tiempo real cómo responder ante ellos.',
        videoUrl: 'https://www.youtube.com/embed/2G8f4cP7ein',
        videoSource: 'Fuente: https://es.wikipedia.org/wiki/Simulador',
        activity: {
            title: 'Ejercicio práctico',
            instructions: 'Completa la actividad y comprueba tu nivel de aprendizaje en seguridad digital.',
            clues: [
                { id: 'w1', clue: 'Simula un ataque para entrenar defensas', answer: 'simulacion' },
                { id: 'w2', clue: 'Práctica antes de una situación real', answer: 'ensayo' },
                { id: 'w3', clue: 'Termina este curso y sube tu ...', answer: 'nivel' }
            ]
        }
    }
];

let selectedCourseId = 'phishing';
const cardsContainer = document.getElementById('cardsContainer');
const searchInput = document.getElementById('searchCourses');
const modalOverlay = document.getElementById('modalOverlay');
const modalContent = document.getElementById('modalContent');
const toast = document.getElementById('toast');
const explorePanel = document.getElementById('explorePanel');
console.log('interfaz_usuario script loaded');

function showToast(message) {
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2600);
}

function openModal(html) {
    modalContent.innerHTML = html;
    modalOverlay.style.display = 'flex';
}

function closeModal() {
    modalOverlay.style.display = 'none';
    modalContent.innerHTML = '';
}

function getCourseById(id) {
    return courses.find(course => course.id === id);
}

function saveCourseProgress() {
    const progressState = courses.reduce((acc, course) => {
        acc[course.id] = course.progress;
        return acc;
    }, {});
    localStorage.setItem('courseProgressState', JSON.stringify(progressState));
}

function restoreCourseProgress() {
    const saved = localStorage.getItem('courseProgressState');
    if (!saved) return;
    const state = JSON.parse(saved);
    courses.forEach(course => {
        if (typeof state[course.id] === 'number') {
            course.progress = state[course.id];
        }
    });
}

function updateCourseCardProgress(course) {
    const card = document.querySelector(`.course-card[data-course-id="${course.id}"]`);
    if (!card) return;
    card.querySelector('.course-percentage').textContent = `${course.progress}%`;
    card.dataset.courseProgress = course.progress;
}

function getLevelLabel(progress) {
    if (progress >= 100) return 'Nivel completado';
    if (progress >= 75) return 'Avanzado';
    if (progress >= 40) return 'Intermedio';
    return 'Inicial';
}

function renderActivitySection(course) {
    const cluesHtml = course.activity.clues.map((clue, index) => {
        return `
            <div class="mb-3">
                <label class="form-label"><strong>${index + 1}. ${clue.clue}</strong></label>
                <input type="text" id="activity-${course.id}-${clue.id}" class="form-control" maxlength="20" placeholder="Escribe la palabra" />
            </div>
        `;
    }).join('');

    return `
        <div class="mt-4 p-3 border rounded bg-light">
            <h6>${course.activity.title}</h6>
            <p>${course.activity.instructions}</p>
            ${cluesHtml}
            <button class="btn btn-success" onclick="checkActivity('${course.id}')">Revisar actividad</button>
        </div>
    `;
}

function loadCourseDetails(id) {
    window.location.href = `curso.php?curso=${encodeURIComponent(id)}`;
}

function checkActivity(id) {
    const course = getCourseById(id);
    if (!course) return;

    const answers = course.activity.clues.map(clue => {
        const input = document.getElementById(`activity-${course.id}-${clue.id}`);
        return input ? input.value.trim().toLowerCase() : '';
    });

    const allCorrect = course.activity.clues.every((clue, index) => answers[index] === clue.answer.toLowerCase());
    if (!allCorrect) {
        showToast('Algunas respuestas no son correctas. Revisa las pistas y vuelve a intentarlo.');
        return;
    }

    if (course.progress >= 100) {
        showToast('Ya completaste este curso.');
        return;
    }

    course.progress = Math.min(100, course.progress + 25);
    updateCourseCardProgress(course);
    saveCourseProgress();
    showToast(`Actividad completada. Progreso actualizado a ${course.progress}%`);
    loadCourseDetails(id);
}

function selectCourse(id) {
    selectedCourseId = id;
    const card = document.querySelector(`.course-card[data-course-id="${id}"]`);
    const label = document.querySelector('#selectedCourseLabel');
    if (card) {
        label.textContent = `Curso seleccionado: ${getCourseById(id).title}`;
        card.scrollIntoView({behavior:'smooth', block:'center'});
    }
    closeModal();
    showToast('Curso seleccionado: ' + getCourseById(id).title);
}

function filterCourses() {
    const filter = searchInput.value.trim().toLowerCase();
    document.querySelectorAll('.course-card').forEach(card => {
        const title = card.dataset.courseTitle;
        card.closest('.col-md-4').style.display = title.includes(filter) ? '' : 'none';
    });
}

function savePreferences() {
    const state = {
        shareLevel: document.getElementById('shareLevel').checked,
        shareCertificates: document.getElementById('shareCertificates').checked,
        shareProgress: document.getElementById('shareProgress').checked,
    };
    localStorage.setItem('userDashboardPreferences', JSON.stringify(state));
    showToast('Preferencias guardadas');
}

function restorePreferences() {
    const saved = localStorage.getItem('userDashboardPreferences');
    if (!saved) return;
    const state = JSON.parse(saved);
    ['shareLevel','shareCertificates','shareProgress'].forEach(k => {
        const el = document.getElementById(k);
        if (el) el.checked = !!state[k];
    });
}

function toggleSafeMode() {
    document.body.classList.toggle('bg-dark');
    document.body.classList.toggle('text-light');
    explorePanel.classList.toggle('alert-dark');
    explorePanel.classList.toggle('alert-info');
    showToast('Modo seguro activado');
}

function updateOverallProgress() {
    const avgProgress = courses.reduce((sum, course) => sum + course.progress, 0) / courses.length;
    const progressBar = document.getElementById('overallProgress');
    const progressValue = document.getElementById('currentProgressValue');
    if (progressBar && progressValue) {
        progressBar.style.width = `${avgProgress}%`;
        progressBar.setAttribute('aria-valuenow', Math.round(avgProgress));
        progressValue.textContent = `${Math.round(avgProgress)}%`;
    }
}

function showExplore() {
    explorePanel.classList.remove('d-none');
    showToast('Zona de exploración abierta');
}

function scrollToSection(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.scrollIntoView({behavior: 'smooth', block: 'start'});
}

function showGuide() {
    const search = document.getElementById('searchCourses');
    if (search) search.classList.remove('d-none');
    scrollToSection('guideSection');
}

function setupNavigationButtons() {
    const guideBtn = document.getElementById('btnGuide');
    const adviceBtn = document.getElementById('btnAdvice');
    const communityBtn = document.getElementById('btnCommunity');
    const newsBtn = document.getElementById('btnNews');
    const progressBtn = document.getElementById('btnProgressSection');
    const contactBtn = document.getElementById('btnContactSection');
    if (guideBtn) guideBtn.addEventListener('click', showGuide);
    if (adviceBtn) adviceBtn.addEventListener('click', () => scrollToSection('adviceSection'));
    if (communityBtn) communityBtn.addEventListener('click', () => scrollToSection('communitySection'));
    if (newsBtn) newsBtn.addEventListener('click', () => scrollToSection('newsSection'));
    if (progressBtn) progressBtn.addEventListener('click', () => scrollToSection('progressSection'));
    if (contactBtn) contactBtn.addEventListener('click', () => scrollToSection('contactSection'));
}

searchInput.addEventListener('input', filterCourses);
modalOverlay.addEventListener('click', event => {
    if (event.target === modalOverlay) closeModal();
});

// Contact display handler (button-driven)
function showContact(type) {
    const map = {
        whatsapp: {label: 'WhatsApp', value: '+57 313 363 7818'},
        email: {label: 'Email', value: '1010078559@teinco.edu.co'},
        callcenter: {label: 'Call Center', value: '+57 350 601 7348'}
    };
    const contact = map[type];
    if (!contact) return;
    const container = document.getElementById('contactInfo');
    if (!container) return;
    const current = container.dataset.current;
    if (current === type) {
        container.style.display = 'none';
        container.innerHTML = '';
        delete container.dataset.current;
        return;
    }
    container.dataset.current = type;
    container.innerHTML = `
        <div class="p-3 border rounded bg-white shadow-sm">
            <small class="text-muted">Show</small>
            <div class="mt-2">
                <strong>${contact.label}</strong>
                <div>${contact.value}</div>
            </div>
        </div>
    `;
    container.style.display = 'block';
}

document.getElementById('closeModal').addEventListener('click', closeModal);
document.getElementById('savePreferences').addEventListener('click', savePreferences);
document.getElementById('btnUpdate').addEventListener('click', () => { window.location.href = 'actualizar_usuario.php'; });
document.getElementById('btnPassword').addEventListener('click', () => { window.location.href = 'cambiar_password.php'; });
document.getElementById('btnCourses').addEventListener('click', () => {
    const html = `
        <h5>Sección de Cursos</h5>
        <p>Haz clic en cualquier tarjeta para acceder al curso con teoría, video y actividades interactivas.</p>
        <ul>
            ${courses.map(course => `<li><strong>${course.title}</strong> — ${course.progress}% completado</li>`).join('')}
        </ul>
        <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
    `;
    openModal(html);
});
document.getElementById('btnConfig').addEventListener('click', () => {
    const html = `
        <h5>Configuración</h5>
        <p>Desde aquí puedes ajustar tus preferencias de contacto y compartir tus progresos.</p>
        <p><strong>Consejo:</strong> guarda tus preferencias para que se mantengan en tu navegador.</p>
        <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
    `;
    openModal(html);
});
document.getElementById('btnSafeMode').addEventListener('click', () => {
    window.location.href = 'interfaz_usuario.php';
});
document.getElementById('btnExplore').addEventListener('click', showExplore);

document.querySelectorAll('.course-card').forEach(card => {
    card.querySelector('.btn-detail').addEventListener('click', event => {
        event.stopPropagation();
        loadCourseDetails(card.dataset.courseId);
    });
});

restoreCourseProgress();
courses.forEach(updateCourseCardProgress);
restorePreferences();
setupNavigationButtons();
// ensure contact panel reflects default hidden state after restore
const contactInfoEl = document.getElementById('contactInfo');
if (contactInfoEl) {
    contactInfoEl.style.display = 'none';
    delete contactInfoEl.dataset.current;
}
updateOverallProgress();
</script>

</body>
</html>
