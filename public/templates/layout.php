<?php
/**
 * Master Layout Template for Academia Tectonica (Matching React Mockup Visuals)
 */

if (!defined('ABSPATH')) {
    exit;
}

$active_course_obj = $current_course;
$stats = isset($stats) ? $stats : array(
    'modulesCount'         => count($modules_data),
    'actionDone'           => 3,
    'actionTotal'          => 12,
    'actionPercentage'     => 25,
    'deliverablesDone'     => 1,
    'deliverablesTotal'    => 5,
    'graduationPercentage' => 20
);
?>

<div class="academia-tectonica-app" id="academia-app-root">
    <!-- Toast Notification Container -->
    <div id="academia-toast" class="aca-toast" style="display:none;">
        <span class="aca-toast-icon">⚡</span>
        <span class="aca-toast-msg" id="academia-toast-text">Acción realizada</span>
    </div>

    <!-- Header Principal con Selector de Cursos y Ruta 360° -->
    <header class="aca-header-main" style="background: linear-gradient(135deg, #2e1065 0%, #312e81 40%, #155e75 100%);">
        <div class="aca-header-inner">
            
            <!-- SELECTOR SIMPLIFICADO DE CURSOS -->
            <div class="aca-course-selector-bar">
                <div class="aca-course-brand">
                    <span class="aca-brand-badge-icon"><?php echo esc_html($active_course_obj['icon']); ?></span>
                    <div>
                        <span class="aca-brand-tag">RUTA FORMATIVA · ACADEMIA TECTÓNICA</span>
                        <span class="aca-brand-sub">Navegación entre Módulos y Cursos</span>
                    </div>
                </div>

                <div class="aca-course-dropdown-wrap">
                    <select id="academia-course-switcher" class="aca-course-select">
                        <?php foreach ($courses_data as $c) : 
                            $is_selected = ($c['id'] == $active_course_obj['id']);
                            $is_accessible = $is_admin || $c['isEnrolled'];
                            $label_prefix = $is_accessible ? $c['icon'] : '🔒 ' . $c['icon'];
                        ?>
                            <option value="<?php echo esc_attr($c['slug']); ?>" 
                                    data-url="<?php echo esc_url(add_query_arg('course', $c['slug'])); ?>"
                                    data-enrolled="<?php echo $is_accessible ? '1' : '0'; ?>"
                                    data-purchase="<?php echo esc_url($c['purchaseUrl']); ?>"
                                    <?php selected($is_selected, true); ?>>
                                <?php echo esc_html($label_prefix . ' ' . $c['shortName']); ?><?php echo !$is_accessible ? ' (Bloqueado)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Banner Info del Curso & Métricas de Avance -->
            <div class="aca-course-hero">
                <div class="aca-hero-text">
                    <span class="aca-hero-pill">
                        AFINACIÓN · LIDERAZGO · ALMA
                    </span>
                    <h1 class="aca-hero-title">
                        <?php echo esc_html($active_course_obj['title']); ?>
                    </h1>
                    <p class="aca-hero-desc">
                        <?php echo esc_html($active_course_obj['description']); ?>
                    </p>
                </div>

                <!-- Panel de Métricas de Avance Global -->
                <div class="aca-metrics-panel">
                    <div class="aca-metric-cell">
                        <span class="aca-metric-val"><?php echo intval($stats['modulesCount']); ?></span>
                        <span class="aca-metric-lbl">Módulos</span>
                    </div>
                    <div class="aca-metric-cell aca-border-l">
                        <span class="aca-metric-val"><?php echo intval($stats['actionPercentage']); ?>%</span>
                        <span class="aca-metric-lbl">Acción (<?php echo intval($stats['actionDone']); ?>/<?php echo intval($stats['actionTotal']); ?>)</span>
                    </div>
                    <div class="aca-metric-cell aca-border-l">
                        <span class="aca-metric-val aca-text-amber"><?php echo intval($stats['deliverablesDone']); ?>/<?php echo intval($stats['deliverablesTotal']); ?></span>
                        <span class="aca-metric-lbl">Entregables</span>
                    </div>
                    <div class="aca-metric-cell aca-border-l">
                        <span class="aca-metric-val aca-text-emerald"><?php echo intval($stats['graduationPercentage']); ?>%</span>
                        <span class="aca-metric-lbl">Graduación</span>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <?php if (!$has_access && !$is_admin) : ?>
        <!-- Pantalla de Curso Bloqueado -->
        <div class="aca-locked-container">
            <div class="aca-locked-icon">🔒</div>
            <h2>Curso Bloqueado: <?php echo esc_html($active_course_obj['title']); ?></h2>
            <p>
                <?php echo esc_html($active_course_obj['description']); ?>
            </p>
            <p style="font-weight:700; color:#1e293b; margin-top:16px;">
                Adquiere este curso a través de la tienda para acceder a todas las lecciones, laboratorios del Studio y certificación oficial.
            </p>
            <a href="<?php echo esc_url($active_course_obj['wc_product_id'] ? Academia_WC::get_course_purchase_url($active_course_obj['wc_product_id']) : '#'); ?>" class="aca-btn-unlock">
                🛒 Adquirir y Desbloquear Curso
            </a>
        </div>
    <?php else : ?>
        <!-- Barra de Navegación y Controles Sticky -->
        <div class="aca-sticky-nav-bar">
            <div class="aca-nav-inner">
                <nav class="aca-tab-pills">
                    <button type="button" class="aca-tab-btn active" data-tab="modules">
                        <span>🧠</span> Conocimiento
                    </button>
                    <button type="button" class="aca-tab-btn" data-tab="tools">
                        <span>⚡</span> Acción (<?php echo intval($stats['actionDone']); ?>/<?php echo intval($stats['actionTotal']); ?>)
                    </button>
                    <button type="button" class="aca-tab-btn" data-tab="studio">
                        <span>🧪</span> Studio
                    </button>
                    <button type="button" class="aca-tab-btn" data-tab="graduation">
                        <span>🎓</span> Graduación
                        <span class="aca-pill-badge"><?php echo intval($stats['deliverablesDone']); ?>/<?php echo intval($stats['deliverablesTotal']); ?></span>
                    </button>
                    <button type="button" class="aca-tab-btn" data-tab="impact">
                        <span>💎</span> Impacto
                    </button>
                </nav>
            </div>
        </div>

        <!-- Contenedor Principal de Pestañas -->
        <main class="aca-main-container">
            <!-- Pestaña 1: Conocimiento / Módulos -->
            <div id="tab-content-modules" class="aca-tab-pane active">
                <?php include ACADEMIA_PLUGIN_DIR . 'public/templates/tab-modules.php'; ?>
            </div>

            <!-- Pestaña 2: Acción / Herramientas -->
            <div id="tab-content-tools" class="aca-tab-pane" style="display:none;">
                <?php include ACADEMIA_PLUGIN_DIR . 'public/templates/tab-tools.php'; ?>
            </div>

            <!-- Pestaña 3: Studio -->
            <div id="tab-content-studio" class="aca-tab-pane" style="display:none;">
                <?php include ACADEMIA_PLUGIN_DIR . 'public/templates/tab-studio.php'; ?>
            </div>

            <!-- Pestaña 4: Graduación -->
            <div id="tab-content-graduation" class="aca-tab-pane" style="display:none;">
                <?php include ACADEMIA_PLUGIN_DIR . 'public/templates/tab-graduation.php'; ?>
            </div>

            <!-- Pestaña 5: Impacto -->
            <div id="tab-content-impact" class="aca-tab-pane" style="display:none;">
                <?php include ACADEMIA_PLUGIN_DIR . 'public/templates/tab-impact.php'; ?>
            </div>
        </main>
    <?php endif; ?>

    <!-- Modal Mentor 1 a 1 (FluentBooking) -->
    <div id="academia-mentor-modal" class="aca-modal-overlay" style="display:none;">
        <div class="aca-modal-card" style="max-width:840px; max-height:90vh; overflow-y:auto;">
            <div class="aca-modal-header" style="position:sticky; top:0; background:#ffffff; z-index:10; padding-top:4px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e2e8f0; padding-bottom:16px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:52px; height:52px; border-radius:16px; overflow:hidden; flex-shrink:0; background:#f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.12); display:flex; align-items:center; justify-content:center; border:2px solid #ffffff;">
                        <img src="<?php echo esc_url($mentor_info['avatarUrl']); ?>" alt="<?php echo esc_attr($mentor_info['name']); ?>" style="width:100%; height:100%; object-fit:cover; display:block;" onerror="this.style.display='none'; this.parentElement.innerText='👩‍🏫';" />
                    </div>
                    <div>
                        <h3 style="margin:0; font-size:18px; font-weight:800; color:#0f172a;"><?php echo esc_html($mentor_info['name']); ?></h3>
                        <small style="color:#64748b; font-size:12px; display:block; margin-top:2px;"><?php echo esc_html($mentor_info['role']); ?></small>
                    </div>
                </div>
                <button type="button" class="aca-modal-close-btn" style="background:transparent !important; border:none !important; color:#64748b !important; font-size:28px !important; line-height:1 !important; padding:4px 8px !important; cursor:pointer !important; box-shadow:none !important; min-width:unset !important; min-height:unset !important; border-radius:8px !important;">&times;</button>
            </div>
            <div class="aca-modal-body">
                <p style="font-size:14px; color:#334155; line-height:1.6; margin-bottom:16px;">
                    Reserva tu sesión estratégica 1 a 1 de 45 minutos para auditar tu arquitectura de negocio, desbloquear cuellos de botella y acelerar la aprobación de tus entregables de graduación.
                </p>
                
                <div class="aca-pricing-box" style="margin-bottom:20px;">
                    <div>
                        <strong>Sesión de Mentoría 1 a 1</strong><br>
                        <small style="color:#64748b;">45 minutos · Vía Zoom / Google Meet</small>
                    </div>
                    <div class="aca-price-tag">
                        <?php echo esc_html($mentor_info['price']); ?>
                    </div>
                </div>

                <!-- FluentBooking Embed Directo -->
                <div class="fluent-booking-embed-box" style="background:#ffffff; border-radius:12px; min-height:400px;">
                    <?php echo do_shortcode($mentor_info['bookingShortcode']); ?>
                </div>
            </div>
        </div>
    </div>
</div>
