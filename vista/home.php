<?php
// Redireccionar si ya tiene sesión activa
if(isset($_SESSION['usuario']) && isset($_SESSION['rol'])){
    $redirects = [
        'paciente'      => 'panel/paciente',
        'medico'        => 'panel/medico',
        'asistente'     => 'panel/asistente',
        'administrador' => 'panel/administrador'
    ];
    if(isset($redirects[$_SESSION['rol']])){
        header('Location: ' . APP_URL . '/' . $redirects[$_SESSION['rol']]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioVital - Consultoría Médica Inteligente</title>
    <meta name="description" content="BioVital - Tu salud es nuestra prioridad. Ecosistema médico digital con especialidades en cardiología, neumonología, psicología y pediatría.">
    <meta name="keywords" content="consultoría médica, salud, cardiología, neumonología, psicología, pediatría, BioVital">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/home.css">
</head>
<body>

<!-- ============ HEADER NAVIGATION ============ -->
<header class="header" id="header">
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-container" onclick="scrollToSection('home')">
                <div class="logo-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                <div class="logo-text">Bio<span>vital</span></div>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li class="nav-item"><a href="#especializaciones" class="nav-link">Especialidades</a></li>
                <li class="nav-item"><a href="#nosotros" class="nav-link">Sobre Nosotros</a></li>
                <li class="nav-item"><a href="#ubicacion" class="nav-link">Ubicación</a></li>
                <li class="nav-item"><a href="<?php echo APP_URL; ?>/registro/paciente" class="nav-link btn-nav">Registrarse</a></li>
                <li class="nav-item"><a href="javascript:void(0)" onclick="scrollToSection('acceso')" class="nav-link btn-nav btn-nav-accent">Iniciar Sesión</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>
</header>

<!-- ============ IMAGE CAROUSEL ============ -->
<section class="image-carousel" id="carousel">
    <div class="carousel-container">
        <div class="carousel-wrapper">
            <div class="carousel-track">
                <div class="carousel-slide">
                    <img src="<?php echo APP_URL; ?>/img/banner.jpeg.png" alt="Instalaciones Modernas" class="carousel-img">
                    <div class="carousel-caption">
                        <h3>Instalaciones Modernas</h3>
                        <p>Tecnología de última generación al servicio de tu salud</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="<?php echo APP_URL; ?>/img/Doctoraaaa.jpeg.png" alt="Equipo Médico" class="carousel-img">
                    <div class="carousel-caption">
                        <h3>Equipo Médico Especializado</h3>
                        <p>Profesionales altamente calificados comprometidos contigo</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="<?php echo APP_URL; ?>/img/locacion.jpeg.png" alt="Atención Personalizada" class="carousel-img">
                    <div class="carousel-caption">
                        <h3>Atención Personalizada</h3>
                        <p>Cuidado individual adaptado a cada paciente</p>
                    </div>
                </div>
            </div>
            <button class="carousel-btn carousel-btn-prev" id="prevBtn" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-btn carousel-btn-next" id="nextBtn" aria-label="Siguiente"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="carousel-indicators">
            <button class="indicator active" data-slide="0" aria-label="Slide 1"></button>
            <button class="indicator" data-slide="1" aria-label="Slide 2"></button>
            <button class="indicator" data-slide="2" aria-label="Slide 3"></button>
        </div>
    </div>
</section>

<!-- ============ HERO SECTION ============ -->
<section class="hero" id="home">
    <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="hero-content">
        <div class="hero-text">
            <h1 class="hero-title">Tu Salud es Nuestra <span class="gradient-text">Prioridad</span></h1>
            <p class="hero-subtitle">Ecosistema médico digital de última generación. Accede a consultas, especialidades y seguimiento desde una sola plataforma.</p>
        </div>
        <div class="specialties-grid" id="especializaciones">
            <div class="specialty-card" data-specialty="general">
                <div class="specialty-icon"><i class="fas fa-stethoscope"></i></div>
                <span class="specialty-name">GENERAL</span>
            </div>
            <div class="specialty-card" data-specialty="cardiologia">
                <div class="specialty-icon"><i class="fas fa-heartbeat"></i></div>
                <span class="specialty-name">CARDIOLOGÍA</span>
            </div>
            <div class="specialty-card" data-specialty="neumonologia">
                <div class="specialty-icon"><i class="fas fa-lungs"></i></div>
                <span class="specialty-name">NEUMOLOGÍA</span>
            </div>
            <div class="specialty-card" data-specialty="psicologia">
                <div class="specialty-icon"><i class="fas fa-brain"></i></div>
                <span class="specialty-name">PSICOLOGÍA</span>
            </div>
            <div class="specialty-card" data-specialty="pediatria">
                <div class="specialty-icon"><i class="fas fa-baby"></i></div>
                <span class="specialty-name">PEDIATRÍA</span>
            </div>
        </div>
    </div>
</section>

<!-- ============ ABOUT US SECTION ============ -->
<section class="about" id="nosotros">
    <div class="about-container">
        <div class="about-image">
            <img src="<?php echo APP_URL; ?>/img/Doctoraaaa.jpeg.png" alt="Profesional de la salud" class="about-img">
            <div class="about-image-badge"><i class="fas fa-award"></i><span>Certificados</span></div>
        </div>
        <div class="about-content">
            <span class="section-tag"><i class="fas fa-info-circle"></i> Conócenos</span>
            <h2 class="about-title">Excelencia Médica con Tecnología de Vanguardia</h2>
            <p class="about-text">En BioVital, nos comprometemos a ofrecer servicios médicos de la más alta calidad con un equipo de profesionales altamente calificados y experimentados. Nuestra misión es brindar atención médica integral y personalizada.</p>
            <p class="about-text">Contamos con tecnología de última generación e instalaciones modernas para asegurar diagnósticos precisos y tratamientos efectivos en todas nuestras especialidades.</p>
            <div class="about-stats">
                <div class="stat-item">
                    <span class="stat-number" data-count="15">0</span><span class="stat-suffix">+</span>
                    <span class="stat-label">Años de Experiencia</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="50">0</span><span class="stat-suffix">+</span>
                    <span class="stat-label">Médicos Especializados</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="10">0</span><span class="stat-suffix">K+</span>
                    <span class="stat-label">Pacientes Atendidos</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ROLE ACCESS SECTION ============ -->
<section class="access-section" id="acceso">
    <div class="access-container">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-lock-open"></i> Portal de Acceso</span>
            <h2 class="section-title">Ecosistema Digital <span class="gradient-text">Biovital</span></h2>
            <p class="section-subtitle">Selecciona tu perfil para acceder a la plataforma</p>
        </div>

        <!-- HOME VIEW - Role Cards -->
        <div id="home-view" class="view-section active">
            <div class="roles-grid">
                <div class="role-card" onclick="openLogin('paciente', 'fa-user-injured')">
                    <div class="role-icon-wrapper"><i class="fa-solid fa-user-injured"></i></div>
                    <h3>Paciente</h3>
                    <p>Accede a tu historial clínico, agenda citas y descarga recetas.</p>
                </div>
                <div class="role-card" onclick="openLogin('medico', 'fa-user-md')">
                    <div class="role-icon-wrapper"><i class="fa-solid fa-user-md"></i></div>
                    <h3>Médico</h3>
                    <p>Gestiona pacientes, genera diagnósticos y controla horarios.</p>
                </div>
                <div class="role-card" onclick="openLogin('asistente', 'fa-clipboard-list')">
                    <div class="role-icon-wrapper"><i class="fa-solid fa-clipboard-list"></i></div>
                    <h3>Asistente</h3>
                    <p>Organiza flujos de la clínica y agendas de especialistas.</p>
                </div>
                <div class="role-card" onclick="openLogin('administrador', 'fa-sliders')">
                    <div class="role-icon-wrapper"><i class="fa-solid fa-sliders"></i></div>
                    <h3>Administrador</h3>
                    <p>Supervisa métricas operativas, gestiona accesos y seguridad.</p>
                </div>
            </div>
        </div>

        <!-- LOGIN VIEW -->
        <div id="login-view" class="view-section">
            <div class="form-wrapper">
                <button class="back-btn" onclick="showView('home-view')"><i class="fa-solid fa-arrow-left"></i> Volver</button>
                <h2>¡Hola de nuevo!</h2>
                <div id="login-badge" class="role-badge"></div>
                <div id="login-error" class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="login-error-msg"></span>
                </div>
                <form id="form-login">
                    <input type="hidden" name="rol" id="login-rol" value="">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <input type="text" name="user" id="login-user" placeholder="Cédula de identidad" required>
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <input type="password" name="pass" id="login-pass" placeholder="Contraseña de seguridad" required>
                            <i class="fa-solid fa-lock"></i>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn" id="login-submit-btn">
                        <i class="fas fa-sign-in-alt"></i> Autenticar Entrada
                    </button>
                </form>
                <div class="form-footer" id="signup-redirect"></div>
            </div>
        </div>
    </div>
</section>

<!-- ============ LOCATIONS SECTION ============ -->
<section class="locations" id="ubicacion">
    <div class="locations-container">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-map-marker-alt"></i> Encuéntranos</span>
            <h2 class="section-title">Nuestras <span class="gradient-text">Sucursales</span></h2>
        </div>
        <div class="locations-grid">
            <div class="location-card">
                <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3 class="location-name">CARACAS (Principal)</h3>
                <p class="location-address">Av. Principal con Calle Secundaria, Edificio Médico, Piso 3</p>
                <p class="location-phone"><i class="fas fa-phone"></i> +58 212-1234567</p>
                <p class="location-email"><i class="fas fa-envelope"></i> caracas@biovital.com</p>
                <button class="location-btn" onclick="showMap('caracas')"><i class="fas fa-directions"></i> Ver en mapa</button>
            </div>
            <div class="location-card">
                <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3 class="location-name">CARACAS (Este)</h3>
                <p class="location-address">Av. Este con Calle Norte, Centro Médico Este, Piso 2</p>
                <p class="location-phone"><i class="fas fa-phone"></i> +58 212-7654321</p>
                <p class="location-email"><i class="fas fa-envelope"></i> este@biovital.com</p>
                <button class="location-btn" onclick="showMap('caracas-este')"><i class="fas fa-directions"></i> Ver en mapa</button>
            </div>
            <div class="location-card">
                <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3 class="location-name">MARACAIBO (Zulia)</h3>
                <p class="location-address">Av. del Lago con Calle 15, Hospital Zulia, Piso 1</p>
                <p class="location-phone"><i class="fas fa-phone"></i> +58 261-9876543</p>
                <p class="location-email"><i class="fas fa-envelope"></i> maracaibo@biovital.com</p>
                <button class="location-btn" onclick="showMap('maracaibo')"><i class="fas fa-directions"></i> Ver en mapa</button>
            </div>
        </div>
    </div>
</section>


<!-- ============ FOOTER ============ -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo-container">
                    <div class="logo-icon footer-logo-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div class="logo-text">Bio<span>vital</span></div>
                </div>
                <p class="footer-description">Plataforma tecnológica hospitalaria certificada. Tu bienestar, nuestra misión.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <div class="footer-section">
                    <h4>Especialidades</h4>
                    <ul>
                        <li><a href="#especializaciones">Medicina General</a></li>
                        <li><a href="#especializaciones">Cardiología</a></li>
                        <li><a href="#especializaciones">Neumología</a></li>
                        <li><a href="#especializaciones">Psicología</a></li>
                        <li><a href="#especializaciones">Pediatría</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Sucursales</h4>
                    <ul>
                        <li><a href="#ubicacion">Caracas (Principal)</a></li>
                        <li><a href="#ubicacion">Caracas (Este)</a></li>
                        <li><a href="#ubicacion">Maracaibo (Zulia)</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <ul>
                        <li><i class="fas fa-phone"></i> <a href="tel:+582121234567">+58 212-1234567</a></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:info@biovital.com">info@biovital.com</a></li>
                        <li><i class="fas fa-clock"></i> Lun-Vie: 8AM - 6PM</li>
                        <li><i class="fas fa-calendar"></i> Sáb: 9AM - 1PM</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 BioVital. Todos los derechos reservados. Plataforma Tecnológica Hospitalaria.</p>
        </div>
    </div>
</footer>

<!-- ============ SPECIALTY MODAL ============ -->
<div class="modal" id="specialtyModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeSpecialtyModal()">×</span>
        <div id="specialtyModalBody"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>const APP_URL = '<?php echo APP_URL; ?>';</script>
<script src="<?php echo APP_URL; ?>/js/home.js"></script>
</body>
</html>
