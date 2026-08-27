<?php
/**
 * Admin / Mentor Dashboard View: Panel de Control & Mentoría Tectónica
 * Matches 100% of panel_de_control_y_mentor_a_tect_nica.tsx
 */

if (!defined('ABSPATH')) {
    exit;
}

$db = new Academia_DB();
$current_wp_user = wp_get_current_user();
$is_admin_user = current_user_can('manage_options') || in_array('administrator', (array)$current_wp_user->roles);

// Get all courses from DB
$db_courses = $db->get_courses();

$courses_catalog = array(
    'm0' => array('id' => 'm0', 'code' => 'MÓDULO 0', 'shortName' => '0 · Iniciación', 'name' => '0 · INICIACIÓN - DEL CAOS A LA ESTRUCTURA', 'icon' => '🌱', 'color' => '#10B981', 'price' => 147, 'students' => 142, 'revenue' => 20874),
    'c1' => array('id' => 'c1', 'code' => 'MÓDULO 1', 'shortName' => '1 · Cuerpo', 'name' => '1 · DEL CAOS AL BIENESTAR', 'icon' => '🔋', 'color' => '#0D9488', 'price' => 197, 'students' => 98, 'revenue' => 19306),
    'c2' => array('id' => 'c2', 'code' => 'MÓDULO 2', 'shortName' => '2 · Mente', 'name' => '2 · DEL CAOS A LA IDENTIDAD', 'icon' => '🧠', 'color' => '#5B4FBE', 'price' => 247, 'students' => 164, 'revenue' => 40508),
    'c3' => array('id' => 'c3', 'code' => 'MÓDULO 3', 'shortName' => '3 · Dinero', 'name' => '3 · DEL CAOS FINANCIERO A LA LIBERTAD', 'icon' => '📈', 'color' => '#0284C7', 'price' => 247, 'students' => 110, 'revenue' => 27170),
    'c4' => array('id' => 'c4', 'code' => 'MÓDULO 4', 'shortName' => '4 · Ventas', 'name' => '4 · DEL CAOS A LA VENTA', 'icon' => '📣', 'color' => '#D97706', 'price' => 247, 'students' => 76, 'revenue' => 18772),
    'c5' => array('id' => 'c5', 'code' => 'MÓDULO 5', 'shortName' => '5 · Sistemas', 'name' => '5 · DEL CAOS DIGITAL AL SISTEMA AUTOMATIZADO', 'icon' => '⚙️', 'color' => '#059669', 'price' => 247, 'students' => 89, 'revenue' => 21983),
    'c6' => array('id' => 'c6', 'code' => 'MÓDULO 6', 'shortName' => '6 · Liderazgo', 'name' => '6 · DEL CAOS AL LIDERAZGO', 'icon' => '🤝', 'color' => '#7C3AED', 'price' => 247, 'students' => 64, 'revenue' => 15808),
    'c7' => array('id' => 'c7', 'code' => 'MÓDULO 7', 'shortName' => '7 · Legado', 'name' => '7 · DE LA ESTRUCTURA AL LEGADO', 'icon' => '🚀', 'color' => '#DC2626', 'price' => 297, 'students' => 45, 'revenue' => 13365),
    'master' => array('id' => 'master', 'code' => 'MÁSTER 360', 'shortName' => '🏆 · Máster 360°', 'name' => '🏆 MÁSTER TECTÓNICO COMPLETO', 'icon' => '🎓', 'color' => '#B45309', 'price' => 1497, 'students' => 38, 'revenue' => 56886),
);

// Map DB courses to complement catalog
foreach ($db_courses as $dbc) {
    $slug = $dbc['slug'];
    if (isset($courses_catalog[$slug])) {
        $stats = $db->get_course_stats($dbc['id']);
        if ($stats['students'] > 0) {
            $courses_catalog[$slug]['students'] = $stats['students'];
            $courses_catalog[$slug]['revenue']  = $stats['students'] * $courses_catalog[$slug]['price'];
        }
        $courses_catalog[$slug]['db_id'] = $dbc['id'];
    }
}

// User Profiles Definition
$user_profiles = array(
    'admin' => array(
        'id' => 'admin',
        'name' => 'Administración Central Tectónica',
        'role' => 'Director General & Administrador Global',
        'avatar' => '👑',
        'isAdmin' => true,
        'assignedCourses' => array('m0', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'master'),
        'payoutIban' => 'ES76 2100 0418 4502 0005 1122',
        'totalPaidOut' => 64200,
        'withdrawableBalance' => 42850,
        'escrowBalance' => 12400,
    ),
    'erika' => array(
        'id' => 'erika',
        'name' => 'Dra. Erika Tatiana Parra',
        'role' => 'Directora de Evaluación y Mentora de Estructuras ALMA',
        'avatar' => '👩‍🏫',
        'isAdmin' => false,
        'assignedCourses' => array('m0', 'c2', 'master'),
        'payoutIban' => 'ES91 0182 2370 4502 0184 9901',
        'totalPaidOut' => 24500,
        'withdrawableBalance' => 14820,
        'escrowBalance' => 3200,
    ),
    'marcos' => array(
        'id' => 'marcos',
        'name' => 'Dr. Marcos Vega',
        'role' => 'Director de Finanzas & Arquitectura de Precios',
        'avatar' => '👨‍🔬',
        'isAdmin' => false,
        'assignedCourses' => array('c3', 'c4'),
        'payoutIban' => 'ES34 0049 1500 0512 3456 7890',
        'totalPaidOut' => 11200,
        'withdrawableBalance' => 8640,
        'escrowBalance' => 1850,
    ),
    'ismael' => array(
        'id' => 'ismael',
        'name' => 'Ismael Tectónico',
        'role' => 'Director de Sistemas, Escala & Expansión',
        'avatar' => '👨‍💼',
        'isAdmin' => false,
        'assignedCourses' => array('c5', 'c6', 'c7', 'master'),
        'payoutIban' => 'ES12 2038 5778 9830 0076 5432',
        'totalPaidOut' => 28900,
        'withdrawableBalance' => 19350,
        'escrowBalance' => 4100,
    ),
    'carlos' => array(
        'id' => 'carlos',
        'name' => 'Lic. Carlos Méndez',
        'role' => 'Mentor de Bienestar, Cuerpo & Energía Ejecutiva',
        'avatar' => '🔋',
        'isAdmin' => false,
        'assignedCourses' => array('c1'),
        'payoutIban' => 'ES55 1465 0100 7220 3040 5060',
        'totalPaidOut' => 4800,
        'withdrawableBalance' => 4210,
        'escrowBalance' => 980,
    ),
);

// Determine initial active user based on login
$initial_user_id = 'admin';
if (!$is_admin_user) {
    $assigned = $db->get_user_assigned_courses($current_wp_user->ID);
    $initial_user_id = 'custom_' . $current_wp_user->ID;
    $user_profiles[$initial_user_id] = array(
        'id' => $initial_user_id,
        'name' => $current_wp_user->display_name,
        'role' => 'Mentor Asignado de Academia Tectónica',
        'avatar' => '👨‍🏫',
        'isAdmin' => false,
        'assignedCourses' => $assigned,
        'payoutIban' => get_user_meta($current_wp_user->ID, 'academia_payout_iban', true) ?: 'ES80 0000 0000 0000 0000 0000',
        'totalPaidOut' => intval(get_user_meta($current_wp_user->ID, 'academia_total_paid_out', true)) ?: 3400,
        'withdrawableBalance' => intval(get_user_meta($current_wp_user->ID, 'academia_withdrawable_balance', true)) ?: 2850,
        'escrowBalance' => intval(get_user_meta($current_wp_user->ID, 'academia_escrow_balance', true)) ?: 850,
    );
}

// Initial Audits Data
$initial_audits = array(
    array(
        'id' => 'aud-101',
        'studentName' => 'Alejandro Silva',
        'studentEmail' => 'alejandro.silva@empresa.com',
        'courseId' => 'c2',
        'courseName' => '2 · DEL CAOS A LA IDENTIDAD',
        'submittedAt' => 'Hoy a las 09:30',
        'round' => 1,
        'maxRounds' => 2,
        'isExtraPaid' => false,
        'status' => 'pending',
        'deliverables' => array(
            'matrix' => array('name' => 'Matriz de Decisiones 2x2', 'link' => 'https://canva.com/design/DA-ALMA-matrix-ejemplo', 'status' => 'complete', 'notes' => '5 decisiones clasificadas'),
            'systemMap' => array('name' => 'Mapa de Sistemas (7 Nodos)', 'link' => 'https://miro.com/app/board/uXjVO128=', 'status' => 'complete', 'notes' => 'Cuello de botella detectado en delegación'),
            'plan90' => array('name' => 'Plan ALMA 90 Días', 'link' => 'https://notion.so/tectonica/plan-90d-silva', 'status' => 'complete', 'notes' => '1 página táctica lista'),
            'neuroretos' => array('name' => 'Tracker de Neuroretos', 'value' => '8 / 12 Retos', 'status' => 'complete', 'notes' => 'Supera mínimo requerido'),
            'caseStudy' => array('name' => 'Caso Antes/Después & Test', 'value' => 'Encuesta OK + 180 palabras', 'status' => 'complete', 'notes' => 'Explicación sólida del cambio'),
        ),
        'mentorNotes' => '',
    ),
    array(
        'id' => 'aud-102',
        'studentName' => 'María Gómez',
        'studentEmail' => 'mgomez@logistica-valencia.es',
        'courseId' => 'c2',
        'courseName' => '2 · DEL CAOS A LA IDENTIDAD',
        'submittedAt' => 'Ayer a las 18:15',
        'round' => 2,
        'maxRounds' => 2,
        'isExtraPaid' => false,
        'status' => 'needs_changes',
        'deliverables' => array(
            'matrix' => array('name' => 'Matriz de Decisiones 2x2', 'link' => 'https://canva.com/design/mg-matriz', 'status' => 'complete', 'notes' => 'Bien estructurada'),
            'systemMap' => array('name' => 'Mapa de Sistemas (7 Nodos)', 'link' => '', 'status' => 'missing', 'notes' => 'Falta el enlace público de Miro'),
            'plan90' => array('name' => 'Plan ALMA 90 Días', 'link' => 'https://notion.so/mg-plan', 'status' => 'complete', 'notes' => 'Completo'),
            'neuroretos' => array('name' => 'Tracker de Neuroretos', 'value' => '5 / 12 Retos', 'status' => 'incomplete', 'notes' => 'Faltan 3 para el mínimo de 8'),
            'caseStudy' => array('name' => 'Caso Antes/Después & Test', 'value' => 'Encuesta OK + Caso breve', 'status' => 'complete', 'notes' => 'Aceptable'),
        ),
        'mentorNotes' => 'Falta corregir el acceso a Miro y completar al menos 3 neuroretos adicionales antes de aprobar.',
    ),
    array(
        'id' => 'aud-103',
        'studentName' => 'Rodrigo Peña',
        'studentEmail' => 'rodrigo@penaconsulting.com',
        'courseId' => 'c3',
        'courseName' => '3 · DEL CAOS FINANCIERO A LA LIBERTAD',
        'submittedAt' => 'Hace 2 días',
        'round' => 1,
        'maxRounds' => 2,
        'isExtraPaid' => false,
        'status' => 'approved',
        'deliverables' => array(
            'matrix' => array('name' => 'Estructura de Costos Fijos', 'link' => 'https://drive.google.com/sheet-rodrigo', 'status' => 'complete', 'notes' => 'Margen de contribución claro'),
            'systemMap' => array('name' => 'Flujo de Caja Proyectado', 'link' => 'https://drive.google.com/caja-90d', 'status' => 'complete', 'notes' => 'Proyección impecable'),
            'plan90' => array('name' => 'Plan Financiero ALMA', 'link' => 'https://notion.so/plan-fin-rodrigo', 'status' => 'complete', 'notes' => 'Objetivo de reserva 6 meses'),
            'neuroretos' => array('name' => 'Hábitos de Auditoría Diaria', 'value' => '10 / 12 Retos', 'status' => 'complete', 'notes' => 'Consistencia demostrada'),
            'caseStudy' => array('name' => 'Reducción de Gastos Fantasma', 'value' => 'Ahorro del 18% mensual', 'status' => 'complete', 'notes' => 'Impacto inmediato'),
        ),
        'mentorNotes' => 'Portafolio excelente. Aprobado con mención especial.',
    ),
    array(
        'id' => 'aud-104',
        'studentName' => 'Carla Benítez',
        'studentEmail' => 'c.benitez@clinica-salud.com',
        'courseId' => 'master',
        'courseName' => '🏆 MÁSTER TECTÓNICO COMPLETO',
        'submittedAt' => 'Hoy a las 11:45',
        'round' => 3,
        'maxRounds' => 2,
        'isExtraPaid' => true,
        'status' => 'reviewing',
        'deliverables' => array(
            'matrix' => array('name' => 'Portafolio Global 8 Módulos', 'link' => 'https://notion.so/master-carla-full', 'status' => 'complete', 'notes' => 'Documentación 360°'),
            'systemMap' => array('name' => 'Arquitectura Empresarial Tectónica', 'link' => 'https://miro.com/carla-master', 'status' => 'complete', 'notes' => '7 sistemas autónomos integrados'),
            'plan90' => array('name' => 'Plan de Trascendencia y Legado', 'link' => 'https://drive.google.com/carla-legado', 'status' => 'complete', 'notes' => 'Hoja de ruta a 12 meses'),
            'neuroretos' => array('name' => 'Consolidación de Hábitos', 'value' => '12 / 12 Retos', 'status' => 'complete', 'notes' => '100% completado'),
            'caseStudy' => array('name' => 'Defensa Máster Antes/Después', 'value' => 'Auditoría integral lista', 'status' => 'complete', 'notes' => 'Lista para titulación magna'),
        ),
        'mentorNotes' => 'Revisión extraordinaria (47€ abonados). Comprobando coherencia del nodo de gobernanza.',
    ),
);

// Initial 1a1 Sessions
$initial_1a1_sessions = array(
    array(
        'id' => 'call-01',
        'studentName' => 'Kike Castillo',
        'studentEmail' => 'kike@exhalaconsultores.es',
        'courseId' => 'c2',
        'courseName' => '2 · Mente (Identidad)',
        'mentorId' => 'erika',
        'date' => current_time('Y-m-d'),
        'time' => '10:00 AM',
        'amountPaid' => 97,
        'status' => 'confirmed',
        'meetUrl' => 'https://meet.google.com/tectonica-kike-01',
        'objective' => 'Revisar la delegación de operaciones bajo el marco de Proceso Justo y eliminar cuellos de botella.',
        'notes' => 'Tiene autoescuela y consultora en paralelo. Foco en no regalar tiempo.',
    ),
    array(
        'id' => 'call-02',
        'studentName' => 'Alexander Parra',
        'studentEmail' => 'alexander@denovapharma.com',
        'courseId' => 'c5',
        'courseName' => '5 · Sistemas y Automatización',
        'mentorId' => 'ismael',
        'date' => current_time('Y-m-d'),
        'time' => '12:00 PM',
        'amountPaid' => 97,
        'status' => 'confirmed',
        'meetUrl' => 'https://meet.google.com/tectonica-alexander-02',
        'objective' => 'Optimización de nodos técnicos en laboratorio y estandarización de procesos para liberar al CEO.',
        'notes' => 'Negocio en crecimiento activo. Foco en conexiones estructurales.',
    ),
    array(
        'id' => 'call-03',
        'studentName' => 'Alicia de la Puerta',
        'studentEmail' => 'alicia@watermemory.academy',
        'courseId' => 'c4',
        'courseName' => '4 · Ventas y Monetización',
        'mentorId' => 'marcos',
        'date' => date('Y-m-d', strtotime('+1 day')),
        'time' => '04:00 PM',
        'amountPaid' => 97,
        'status' => 'confirmed',
        'meetUrl' => 'https://meet.google.com/tectonica-alicia-03',
        'objective' => 'Estructurar la transición definitiva de consulta física en cabina a oferta formativa digital High-Ticket.',
        'notes' => 'Ya cuenta con estudiantes iniciales. Foco en empaquetado de alto valor.',
    ),
    array(
        'id' => 'call-04',
        'studentName' => 'Marcos Santana',
        'studentEmail' => 'marcos@santanagroup.com',
        'courseId' => 'c3',
        'courseName' => '3 · Dinero (Finanzas)',
        'mentorId' => 'marcos',
        'date' => date('Y-m-d', strtotime('+2 days')),
        'time' => '11:00 AM',
        'amountPaid' => 97,
        'status' => 'confirmed',
        'meetUrl' => 'https://meet.google.com/tectonica-marcos-04',
        'objective' => 'Auditoría de fuga de márgenes y estructuración de flujo de caja libre.',
        'notes' => 'Facturación alta pero rentabilidad neta comprometida por costos fijos.',
    ),
    array(
        'id' => 'call-05',
        'studentName' => 'Laura Riquelme',
        'studentEmail' => 'laura@riquelmebienestar.com',
        'courseId' => 'c1',
        'courseName' => '1 · Cuerpo (Bienestar)',
        'mentorId' => 'carlos',
        'date' => date('Y-m-d', strtotime('+3 days')),
        'time' => '09:30 AM',
        'amountPaid' => 97,
        'status' => 'confirmed',
        'meetUrl' => 'https://meet.google.com/tectonica-laura-05',
        'objective' => 'Protocolo de recuperación biológica y reducción de cortisol en picos de lanzamiento.',
        'notes' => 'Directora de clínica con sobrecarga física y estrés crónico.',
    ),
);

// Initial Post Sale Services
$initial_post_sale_services = array(
    array(
        'id' => 'srv-01',
        'name' => 'La Cúspide · Máster Empresarial de Alto Nivel',
        'badge' => 'ALTA DIRECCIÓN VIP',
        'price' => 7000,
        'billingType' => 'Experiencia 5 Días (Penthouse VIP)',
        'targetAudience' => 'Fundadores, CEOs y Graduados del Máster 360',
        'description' => 'Inmersión empresarial ultra-exclusiva de 5 días conviviendo y trabajando en un penthouse de lujo. Máster de alto nivel para redefinir el modelo de negocio, escala y gobernanza de tu compañía.',
        'activeClients' => 4,
        'assignedMentorId' => 'erika',
    ),
    array(
        'id' => 'srv-02',
        'name' => 'Sesión Psicológica Individual (Estructuras de Identidad)',
        'badge' => 'ACOMPAÑAMIENTO CLÍNICO 1 A 1',
        'price' => 197,
        'billingType' => 'Sesión Individual 60 min',
        'targetAudience' => 'Líderes y Alumnos en proceso de titulación',
        'description' => 'Sesión psicológica individual orientada a desarmar bloqueos neurocognitivos, regular el cortisol directivo y construir estructuras sólidas de identidad ejecutiva.',
        'activeClients' => 26,
        'assignedMentorId' => 'erika',
    ),
    array(
        'id' => 'srv-03',
        'name' => 'Programa Intensivo de Identidad (Paquete de 7 Sesiones)',
        'badge' => 'TRANSFORMACIÓN COMPLETA',
        'price' => 947,
        'billingType' => 'Pack 7 Sesiones (Ahorro 432€)',
        'targetAudience' => 'Directivos con necesidad de reestructuración profunda',
        'description' => 'Proceso continuado de 7 sesiones individuales de reestructuración psicológica y neurocognitiva. Acompañamiento semanal con auditoría de hábitos y anclaje de identidad.',
        'activeClients' => 12,
        'assignedMentorId' => 'erika',
    ),
    array(
        'id' => 'srv-04',
        'name' => 'Auditoría In-Company de Sistemas & Procesos Justos',
        'badge' => 'HIGH-TICKET IMPLEMENTACIÓN',
        'price' => 3800,
        'billingType' => 'Pago Único',
        'targetAudience' => 'Empresas con equipos de más de 8 personas',
        'description' => 'Intervención de 2 semanas donde el equipo de mentores diseña y documenta los 7 nodos operacionales directamente en la empresa.',
        'activeClients' => 6,
        'assignedMentorId' => 'ismael',
    ),
);

// Initial Payout History
$initial_payout_history = array(
    array('id' => 'pay-101', 'date' => '2026-08-15', 'amount' => 4850, 'iban' => 'ES91 **** 9901', 'recipient' => 'Dra. Erika Tatiana Parra', 'status' => 'completed', 'receiptId' => 'TEC-REC-8841'),
    array('id' => 'pay-102', 'date' => '2026-08-01', 'amount' => 6200, 'iban' => 'ES12 **** 5432', 'recipient' => 'Ismael Tectónico', 'status' => 'completed', 'receiptId' => 'TEC-REC-8799'),
    array('id' => 'pay-103', 'date' => '2026-07-28', 'amount' => 3100, 'iban' => 'ES34 **** 7890', 'recipient' => 'Dr. Marcos Vega', 'status' => 'completed', 'receiptId' => 'TEC-REC-8750'),
);

// Package data for JS hydration
$mentor_dashboard_data = array(
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('academia_nonce'),
    'initialUserId' => $initial_user_id,
    'isAdminUser' => $is_admin_user,
    'userProfiles' => array_values($user_profiles),
    'coursesCatalog' => array_values($courses_catalog),
    'audits' => $initial_audits,
    'sessions1a1' => $initial_1a1_sessions,
    'postSaleServices' => $initial_post_sale_services,
    'payoutHistory' => $initial_payout_history,
);
?>

<div id="academia-mentor-app" class="mentor-app-root">
    <!-- Toast Notification -->
    <div id="mentor-toast" class="mentor-toast-box" style="display:none;">
        <span class="toast-icon">⚡</span>
        <span id="mentor-toast-text" class="toast-msg"></span>
    </div>

    <!-- HEADER PRINCIPAL CON SELECTOR DE ROL Y CURSOS -->
    <header class="mentor-header">
        <div class="mentor-header-inner">
            <div class="mentor-header-left">
                <!-- User Avatar Badge -->
                <div class="user-avatar-wrap">
                    <div class="user-avatar-inner" id="header-user-avatar">
                        <?php echo esc_html($user_profiles[$initial_user_id]['avatar']); ?>
                    </div>
                </div>

                <div class="user-info-text">
                    <div class="role-badge-row">
                        <span id="header-role-badge" class="role-badge <?php echo $user_profiles[$initial_user_id]['isAdmin'] ? 'admin-badge' : 'pro-badge'; ?>">
                            <?php echo $user_profiles[$initial_user_id]['isAdmin'] ? '👑 PANEL ADMINISTRADOR GLOBAL' : '👨‍🏫 PANEL DEL PROFESIONAL'; ?>
                        </span>
                        <span class="online-indicator">
                            <span class="pulse-dot"></span> En línea
                        </span>
                    </div>
                    <h1 class="user-name-title" id="header-user-name">
                        <?php echo esc_html($user_profiles[$initial_user_id]['name']); ?>
                    </h1>
                    <p class="user-role-sub" id="header-user-sub">
                        <?php echo esc_html($user_profiles[$initial_user_id]['role']); ?> · <strong id="header-courses-count" class="highlight-text"><?php echo count($user_profiles[$initial_user_id]['assignedCourses']); ?> cursos asignados</strong>
                    </p>
                </div>
            </div>

            <!-- Selectores de Perfil y Alcance de Curso -->
            <div class="mentor-header-controls">
                <?php if ($is_admin_user) : ?>
                    <div class="control-group">
                        <span class="control-label">Usuario:</span>
                        <select id="select-active-user" class="mentor-select">
                            <?php foreach ($user_profiles as $prof) : ?>
                                <option value="<?php echo esc_attr($prof['id']); ?>" <?php selected($prof['id'], $initial_user_id); ?>>
                                    <?php echo esc_html($prof['avatar'] . ' ' . $prof['name'] . ($prof['isAdmin'] ? ' (Admin)' : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="control-divider"></div>
                <?php endif; ?>

                <div class="control-group">
                    <span class="control-label">Curso:</span>
                    <select id="select-course-filter" class="mentor-select select-amber">
                        <option value="all">🌐 Todos mis cursos asignados</option>
                        <?php foreach ($courses_catalog as $c) : ?>
                            <option value="<?php echo esc_attr($c['id']); ?>">
                                <?php echo esc_html($c['icon'] . ' ' . $c['shortName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </header>

    <!-- SECTION DE LIQUIDACIÓN Y CÁLCULO FINANCIERO -->
    <section class="mentor-finance-bar">
        <div class="finance-bar-inner">
            <!-- BANNER DE FORMULA DE REPARTO TRANSPARENTE -->
            <div class="formula-banner">
                <div class="formula-left">
                    <div class="formula-icon">⚖️</div>
                    <div>
                        <span class="formula-tag">MODELO DE LIQUIDACIÓN TECTÓNICA</span>
                        <span class="formula-desc">
                            Reparto: 100% Bruto → <strong>-20% Gastos/Pasarela</strong> → Base Neta (80%) → <strong>40% Academia</strong> / <strong>60% Profesional</strong>
                        </span>
                    </div>
                </div>

                <div class="formula-stats font-mono">
                    <span class="stat-pill">Alumnos: <strong id="stat-total-students" class="text-white">0</strong></span>
                    <span class="stat-pill">Auditorías Pendientes: <strong id="stat-pending-audits" class="text-rose">0</strong></span>
                </div>
            </div>

            <!-- TARJETAS DE CÁLCULO FINANCIERO -->
            <div class="finance-cards-grid">
                <!-- 1. Ventas Totales Brutas -->
                <div class="finance-card">
                    <span class="f-card-label">1. Ventas Brutas (100%)</span>
                    <span class="f-card-val text-white" id="val-gross-revenue">0 €</span>
                    <span class="f-card-sub">Facturación íntegra</span>
                </div>

                <!-- 2. Deducción de Gastos 20% -->
                <div class="finance-card card-rose">
                    <span class="f-card-label text-rose">2. Gastos & Pasarela (20%)</span>
                    <span class="f-card-val text-rose" id="val-op-costs">-0 €</span>
                    <span class="f-card-sub">Stripe, hosting, soporte</span>
                </div>

                <!-- 3. Base Neta Repartible (80%) -->
                <div class="finance-card card-indigo">
                    <span class="f-card-label text-indigo">3. Base Neta (80%)</span>
                    <span class="f-card-val text-indigo" id="val-net-pool">0 €</span>
                    <span class="f-card-sub">Pool para liquidación</span>
                </div>

                <!-- 4. Canon Academia 40% -->
                <div class="finance-card card-purple">
                    <span class="f-card-label text-purple">4. Ecosistema Academia (40%)</span>
                    <span class="f-card-val text-purple" id="val-academy-share">0 €</span>
                    <span class="f-card-sub">Infraestructura & Marketing</span>
                </div>

                <!-- 5. Ganancia Neta Profesional 60% -->
                <div class="finance-card card-emerald">
                    <span class="f-card-label text-emerald">5. Tu Ganancia Neta (60%)</span>
                    <span class="f-card-val text-emerald" id="val-professional-share">0 €</span>
                    <span class="f-card-sub text-emerald-light">Liquidación del Mentor</span>
                </div>

                <!-- 6. Saldo Disponible en Billetera -->
                <div class="finance-card card-wallet">
                    <span class="f-card-label text-amber">SALDO DISPONIBLE</span>
                    <span class="f-card-val text-white" id="val-withdrawable-balance">0 €</span>
                    <span class="f-card-sub">Custodiado en SEPA</span>
                </div>
            </div>
        </div>
    </section>

    <!-- NAVEGACIÓN STICKY DE PESTAÑAS -->
    <nav class="mentor-nav-bar">
        <div class="nav-bar-inner">
            <button type="button" class="nav-tab-btn active" data-tab="finance">
                <span>📊 Finanzas & Liquidación (Split 20/40/60)</span>
            </button>
            <button type="button" class="nav-tab-btn" data-tab="audits">
                <span>🛎️ Cola de Auditorías</span>
                <span class="tab-count-badge badge-rose" id="nav-audits-count">0</span>
            </button>
            <button type="button" class="nav-tab-btn" data-tab="calls">
                <span>📞 Videollamadas 1 a 1 (97€/h)</span>
                <span class="tab-count-badge badge-indigo" id="nav-calls-count">0</span>
            </button>
            <button type="button" class="nav-tab-btn" data-tab="cms">
                <span>📚 Gestor de Contenidos (CMS)</span>
            </button>
            <button type="button" class="nav-tab-btn" data-tab="upsells">
                <span>🚀 Servicios Post-Venta</span>
                <span class="tab-count-badge badge-amber">High-Ticket</span>
            </button>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="mentor-main-container">

        <!-- TAB 1: FINANZAS & LIQUIDACIÓN -->
        <div id="tab-pane-finance" class="mentor-tab-pane active">
            <div class="pane-stack">
                <!-- Header de Evolución Comercial -->
                <div class="evolution-box">
                    <div class="evolution-text">
                        <div class="tag-row">
                            <span class="evo-tag">📈 RENDIMIENTO & EVOLUCIÓN COMERCIAL</span>
                            <span class="evo-date font-mono">Agosto 2026</span>
                        </div>
                        <h2 class="evo-title" id="evo-user-name">Actividad Financiera</h2>
                        <p class="evo-desc">Comparativa de facturación mensual directa de tus cursos y servicios asignados.</p>
                    </div>

                    <!-- Bloques Estadísticos -->
                    <div class="evo-stats-grid">
                        <div class="evo-stat-col">
                            <span class="evo-lbl">Ventas Último Mes</span>
                            <strong class="evo-num text-white" id="evo-sales-month">0 €</strong>
                            <span class="evo-sub">Facturación bruta 30d</span>
                        </div>
                        <div class="evo-stat-col">
                            <span class="evo-lbl text-emerald">Aumento vs. Mes Anterior</span>
                            <div class="evo-growth-row">
                                <strong class="evo-num text-emerald">+24,8%</strong>
                                <span class="growth-arrow">▲</span>
                            </div>
                            <span class="evo-sub text-emerald" id="evo-growth-net">+0 € netos</span>
                        </div>
                        <div class="evo-stat-col no-border">
                            <span class="evo-lbl text-indigo">Mes Anterior (Julio)</span>
                            <strong class="evo-num text-slate" id="evo-prev-month">0 €</strong>
                            <span class="evo-sub">Base de referencia</span>
                        </div>
                    </div>
                </div>

                <!-- Tabla Cascada Waterfall -->
                <div class="waterfall-box">
                    <h3 class="waterfall-title">
                        <span>🧮</span> Cascada de Liquidación por Curso (<span id="waterfall-courses-count">0</span> cursos en esta vista)
                    </h3>
                    <div class="table-responsive">
                        <table class="waterfall-table">
                            <thead>
                                <tr>
                                    <th>Curso / Módulo</th>
                                    <th>Alumnos</th>
                                    <th>1. Ventas Brutas (100%)</th>
                                    <th class="text-rose">2. Gastos Op. (20%)</th>
                                    <th class="text-indigo">3. Base Neta (80%)</th>
                                    <th class="text-purple">4. Academia (40%)</th>
                                    <th class="text-emerald">5. Tu Ganancia Neta (60%)</th>
                                </tr>
                            </thead>
                            <tbody id="waterfall-table-body" class="font-mono">
                                <!-- Rendered via JS -->
                            </tbody>
                            <tfoot id="waterfall-table-foot" class="font-mono">
                                <!-- Rendered via JS -->
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Billetera y Resumen de Transferencias -->
                <div class="wallet-grid">
                    <!-- Estado de Billetera -->
                    <div class="wallet-box">
                        <span class="wallet-tag">ESTADO DE TU BILLETERA</span>
                        <div class="wallet-details font-mono">
                            <div class="w-row">
                                <span>IBAN de Cobro Vinculado:</span>
                                <strong class="text-white" id="w-iban">ES00 ...</strong>
                            </div>
                            <div class="w-row">
                                <span>Total Retirado Histórico:</span>
                                <strong class="text-emerald" id="w-paidout">0 €</strong>
                            </div>
                            <div class="w-row">
                                <span>Saldo en Garantía (14d):</span>
                                <strong class="text-amber" id="w-escrow">0 €</strong>
                            </div>
                        </div>
                        <button type="button" class="btn-primary-emerald" id="btn-open-withdraw-modal">
                            <span>💳</span> Solicitar Transferencia a mi Banco
                        </button>
                    </div>

                    <!-- Historial de Transferencias -->
                    <div class="payouts-box">
                        <div class="payouts-header">
                            <h3 class="payouts-title">
                                <span>📄</span> Historial de Liquidaciones & Facturas Emitidas
                            </h3>
                            <span class="font-mono text-slate text-xs" id="payouts-count-label">3 Transferencias SEPA</span>
                        </div>
                        <div class="payouts-list" id="payouts-list-container">
                            <!-- Rendered via JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: COLA DE AUDITORÍAS -->
        <div id="tab-pane-audits" class="mentor-tab-pane" style="display:none;">
            <div class="pane-stack">
                <div class="section-banner">
                    <div>
                        <span class="banner-tag">REVISIÓN OFICIAL DE PORTAFOLIOS</span>
                        <h2 class="banner-title" id="audits-header-user">Cola de Auditoría</h2>
                        <p class="banner-desc">Supervisa los 5 entregables de los alumnos inscritos en tus cursos asignados. Emite dictamen y añade feedback estructurado que llegará a su app.</p>
                    </div>
                    <span class="banner-pill font-mono" id="audits-count-pill">0 Expedientes de tus cursos</span>
                </div>

                <div class="audits-list-stack" id="audits-list-container">
                    <!-- Rendered via JS -->
                </div>
            </div>
        </div>

        <!-- TAB 3: VIDEOLLAMADAS 1 A 1 -->
        <div id="tab-pane-calls" class="mentor-tab-pane" style="display:none;">
            <div class="pane-stack">
                <div class="section-banner">
                    <div>
                        <span class="banner-tag">SESIONES INDIVIDUALES (97€ / HORA)</span>
                        <h2 class="banner-title" id="calls-header-user">Agenda de Videollamadas 1 a 1</h2>
                        <p class="banner-desc">Acceso directo a las salas de Google Meet, notas de preparación estratégica y control de asistencia de tus alumnos.</p>
                    </div>
                    <span class="banner-pill badge-emerald font-mono font-bold" id="calls-revenue-pill">0 € Facturados en Asesorías</span>
                </div>

                <div class="calls-grid" id="calls-list-container">
                    <!-- Rendered via JS -->
                </div>
            </div>
        </div>

        <!-- TAB 4: GESTOR DE CONTENIDOS (CMS) -->
        <div id="tab-pane-cms" class="mentor-tab-pane" style="display:none;">
            <div class="pane-stack">
                <div class="section-banner">
                    <div>
                        <span class="banner-tag">GESTOR DE LECCIONES & VÍDEOS (CMS)</span>
                        <h2 class="banner-title">Currículum en Tiempo Real de tus Cursos Asignados</h2>
                        <p class="banner-desc">Modifica títulos de lecciones, enlaces de vídeo HD (Bunny / Vimeo / YouTube) y criterios de entregables asociados en la app del alumno.</p>
                    </div>
                    <span class="banner-pill badge-indigo font-bold" id="cms-courses-count-pill">0 Cursos Activos</span>
                </div>

                <div class="cms-courses-grid" id="cms-courses-container">
                    <!-- Rendered via JS -->
                </div>
            </div>
        </div>

        <!-- TAB 5: SERVICIOS POST-VENTA (HIGH-TICKET) -->
        <div id="tab-pane-upsells" class="mentor-tab-pane" style="display:none;">
            <div class="pane-stack">
                <div class="section-banner">
                    <div>
                        <span class="banner-tag">MONETIZACIÓN POST-GRADUACIÓN</span>
                        <h2 class="banner-title">Catálogo de Servicios Adicionales & High-Ticket</h2>
                        <p class="banner-desc">Configura ofertas de acompañamiento continuo, masterminds cerrados y auditorías in-company ofrecidas exclusivamente a los alumnos graduados.</p>
                    </div>
                    <button type="button" class="btn-primary-amber" id="btn-open-new-service-modal">
                        <span>+</span> Crear Nueva Oferta Post-Venta
                    </button>
                </div>

                <div class="upsells-grid" id="upsells-list-container">
                    <!-- Rendered via JS -->
                </div>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="mentor-footer">
        <p class="footer-title">Academia Tectónica · Panel de Control Docente, Liquidación & Mentoría de Alto Rendimiento</p>
        <p class="footer-sub">Fórmula de Reparto: 20% Costos Operativos → 40% Canon Academia → 60% Liquidación Profesional. © 2026 Academia Tectónica.</p>
    </footer>

    <!-- MODAL 1: RETIRO DE FONDOS / TRANSFERENCIA SEPA -->
    <div id="modal-withdraw" class="mentor-modal-overlay" style="display:none;">
        <div class="mentor-modal-card">
            <div class="modal-card-header">
                <div class="modal-title-row">
                    <div class="modal-header-icon bg-emerald">💳</div>
                    <div>
                        <span class="modal-header-tag text-emerald">BILLETERA DE LIQUIDACIÓN</span>
                        <h3 class="modal-header-h3">Solicitar Transferencia de Fondos</h3>
                    </div>
                </div>
                <button type="button" class="modal-close-x" data-close-modal="withdraw">&times;</button>
            </div>

            <form id="form-withdraw-funds" class="modal-form">
                <div class="modal-summary-box font-mono">
                    <div class="w-row">
                        <span class="text-slate">Beneficiario:</span>
                        <strong class="text-white" id="modal-w-beneficiary">...</strong>
                    </div>
                    <div class="w-row">
                        <span class="text-slate">Saldo Disponible Retirable:</span>
                        <strong class="text-emerald" id="modal-w-balance">0 €</strong>
                    </div>
                </div>

                <div class="form-field">
                    <label class="field-label">Monto a Retirar (€)</label>
                    <input type="number" id="input-withdraw-amount" min="50" class="field-input font-mono font-bold text-emerald" required />
                </div>

                <div class="form-field">
                    <label class="field-label">IBAN / Cuenta Bancaria SEPA</label>
                    <input type="text" id="input-withdraw-iban" class="field-input font-mono" required />
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="field-label">CIF / NIF Facturación</label>
                        <input type="text" id="input-withdraw-taxid" value="B-88392104" class="field-input font-mono" required />
                    </div>
                    <div class="form-field">
                        <label class="field-label">Método de Pago</label>
                        <select class="field-select">
                            <option value="SEPA">Transferencia SEPA (24h)</option>
                            <option value="Stripe">Stripe Connect Instantáneo</option>
                        </select>
                    </div>
                </div>

                <div class="form-field">
                    <label class="field-label">Concepto / Notas de Factura</label>
                    <input type="text" id="input-withdraw-notes" placeholder="Ej. Liquidación Docente Módulos ALMA - Agosto 2026" class="field-input" />
                </div>

                <div class="modal-actions-row">
                    <button type="button" class="btn-cancel" data-close-modal="withdraw">Cancelar</button>
                    <button type="submit" class="btn-confirm-emerald">Confirmar Transferencia SEPA</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: AUDITORÍA DE ENTREGABLES -->
    <div id="modal-audit" class="mentor-modal-overlay" style="display:none;">
        <div class="mentor-modal-card card-wide">
            <div class="modal-card-header">
                <div>
                    <span class="modal-header-tag text-amber">EXPEDIENTE DE AUDITORÍA · EVALUACIÓN OFICIAL</span>
                    <h3 class="modal-header-h3" id="audit-modal-student-name">Evaluando Alumno</h3>
                    <span class="text-indigo font-bold text-xs" id="audit-modal-course-name">Curso</span>
                </div>
                <button type="button" class="modal-close-x" data-close-modal="audit">&times;</button>
            </div>

            <div class="audit-modal-body">
                <h4 class="field-label text-slate">Verificación de Enlaces y Evidencias del Alumno:</h4>
                <div class="deliverables-checklist" id="audit-modal-deliverables-list">
                    <!-- Rendered via JS -->
                </div>

                <div class="audit-decision-section">
                    <label class="field-label text-amber">Dictamen Oficial del Mentor:</label>
                    <div class="decision-toggle-grid">
                        <button type="button" class="btn-decision-choice active" data-decision="approved">
                            <span>🎉</span> Aprobar Graduación
                        </button>
                        <button type="button" class="btn-decision-choice" data-decision="needs_changes">
                            <span>⚠️</span> Solicitar Correcciones
                        </button>
                    </div>

                    <div class="form-field">
                        <label class="field-label text-slate">Feedback Personalizado (Llegará directamente a la app del alumno):</label>
                        <textarea id="audit-modal-feedback-input" rows="4" class="field-textarea" placeholder="Escribe aquí las observaciones estratégicas, felicitaciones o puntos concretos que debe corregir el alumno..."></textarea>
                    </div>

                    <div class="modal-actions-row">
                        <button type="button" class="btn-cancel" data-close-modal="audit">Cancelar</button>
                        <button type="button" class="btn-confirm-amber" id="btn-save-audit-decision">
                            Firmar y Emitir Dictamen Oficial
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: EDITOR CMS DE LECCIÓN -->
    <div id="modal-lesson-cms" class="mentor-modal-overlay" style="display:none;">
        <div class="mentor-modal-card card-wide">
            <div class="modal-card-header">
                <div>
                    <span class="modal-header-tag text-indigo">GESTOR DE CONTENIDOS (CMS)</span>
                    <h3 class="modal-header-h3" id="cms-modal-title">Editar Lección & Vídeo</h3>
                </div>
                <button type="button" class="modal-close-x" data-close-modal="lesson-cms">&times;</button>
            </div>

            <form id="form-edit-lesson-cms" class="modal-form">
                <input type="hidden" id="cms-lesson-id" />
                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="field-label">Título de la Lección</label>
                        <input type="text" id="cms-lesson-title" class="field-input" required />
                    </div>
                    <div class="form-field">
                        <label class="field-label">Duración Estimada (Ej. 15 min)</label>
                        <input type="text" id="cms-lesson-duration" class="field-input" />
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="field-label">Título del Vídeo en Reproductor</label>
                        <input type="text" id="cms-lesson-video-title" class="field-input" />
                    </div>
                    <div class="form-field">
                        <label class="field-label">URL del Vídeo HD (Bunny Stream / Vimeo / YouTube)</label>
                        <input type="text" id="cms-lesson-video-url" class="field-input font-mono text-emerald" placeholder="https://iframe.mediadelivery.net/... o https://youtu.be/..." />
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="field-label">Qué aprenderás en esta clase</label>
                        <textarea id="cms-lesson-what-learn" rows="2" class="field-textarea"></textarea>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Para qué te sirve en tu negocio</label>
                        <textarea id="cms-lesson-utility" rows="2" class="field-textarea"></textarea>
                    </div>
                </div>

                <div class="form-field">
                    <label class="field-label">Texto de Lectura Profunda Complementaria</label>
                    <textarea id="cms-lesson-reading" rows="3" class="field-textarea font-serif"></textarea>
                </div>

                <div class="modal-actions-row">
                    <button type="button" class="btn-cancel" data-close-modal="lesson-cms">Cancelar</button>
                    <button type="submit" class="btn-confirm-amber">Guardar Cambios en Vivo</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    window.MentorDashboardData = <?php echo wp_json_encode($mentor_dashboard_data); ?>;
</script>
