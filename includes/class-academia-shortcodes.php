<?php
/**
 * Shortcodes registration for Academia Tectonica
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_Shortcodes {

    public function __construct() {
        add_shortcode('academia_tectonica', array($this, 'render_academy'));
        add_shortcode('academia_app', array($this, 'render_academy'));
        add_shortcode('academia_mentor_dashboard', array($this, 'render_mentor_dashboard'));
    }

    public function render_mentor_dashboard() {
        if (!is_user_logged_in()) {
            return $this->render_guest_view();
        }

        if (!current_user_can('manage_options') && !current_user_can('academia_review_deliverables') && !current_user_can('academia_manage_all')) {
            return '<div style="background:#0f172a; color:#fda4af; padding:24px; border-radius:16px; text-align:center; max-width:600px; margin:40px auto; border:1px solid rgba(225,29,72,0.4); font-family:sans-serif;">⛔ Este panel está reservado exclusivamente para mentores, instructores y administradores de la Academia Tectónica.</div>';
        }

        // Enqueue Mentor Dashboard CSS / JS
        wp_enqueue_style('academia-mentor-dashboard-css', ACADEMIA_PLUGIN_URL . 'admin/css/academia-mentor-dashboard.css', array(), ACADEMIA_VERSION . '.' . time());
        wp_enqueue_script('academia-mentor-dashboard-js', ACADEMIA_PLUGIN_URL . 'admin/js/academia-mentor-dashboard.js', array('jquery'), ACADEMIA_VERSION . '.' . time(), true);

        ob_start();
        include ACADEMIA_PLUGIN_DIR . 'admin/views/mentor-dashboard.php';
        return ob_get_clean();
    }

    public function render_academy($atts) {
        $atts = shortcode_atts(array(
            'course' => 'c2', // Default course slug: Módulo 2 Del Caos a la Identidad
        ), $atts, 'academia_tectonica');

        // Check if user is logged in
        if (!is_user_logged_in()) {
            return $this->render_guest_view();
        }

        $user_id = get_current_user_id();
        $is_admin = current_user_can('manage_options') || current_user_can('academia_manage_all') || current_user_can('administrator');
        $db = new Academia_DB();

        // Enqueue Assets
        wp_enqueue_style('academia-public-css', ACADEMIA_PLUGIN_URL . 'public/css/academia-public.css', array(), ACADEMIA_VERSION . '.' . time());
        wp_enqueue_script('academia-core-js', ACADEMIA_PLUGIN_URL . 'public/js/academia-core.js', array('jquery'), ACADEMIA_VERSION . '.' . time(), true);
        wp_enqueue_script('academia-studio-js', ACADEMIA_PLUGIN_URL . 'public/js/academia-studio.js', array('jquery', 'academia-core-js'), ACADEMIA_VERSION . '.' . time(), true);
        wp_enqueue_script('academia-graduation-js', ACADEMIA_PLUGIN_URL . 'public/js/academia-graduation.js', array('jquery', 'academia-core-js'), ACADEMIA_VERSION . '.' . time(), true);

        // Fetch courses list
        $all_courses = $db->get_courses();
        $courses_data = array();
        $active_course_slug = isset($_GET['course']) ? sanitize_text_field($_GET['course']) : $atts['course'];
        $current_course = null;

        foreach ($all_courses as $c) {
            // Admin sees all courses unlocked. Registered users have m0 free, others via enrollment
            $is_enrolled = $is_admin || $db->is_user_enrolled($user_id, $c['id']);
            
            $c_item = array(
                'id'          => $c['id'],
                'slug'        => $c['slug'],
                'code'        => $c['code'],
                'shortName'   => $c['short_name'],
                'name'        => $c['title'],
                'icon'        => $c['icon'],
                'color'       => $c['color'],
                'desc'        => $c['description'],
                'isEnrolled'  => $is_enrolled,
                'purchaseUrl' => Academia_WC::get_course_purchase_url($c['wc_product_id'])
            );
            $courses_data[] = $c_item;

            if ($c['slug'] === $active_course_slug) {
                $current_course = $c;
            }
        }

        // Default to course 2 (c2) or first available
        if (!$current_course) {
            foreach ($all_courses as $c) {
                if ($c['slug'] === 'c2') {
                    $current_course = $c;
                    break;
                }
            }
            if (!$current_course && !empty($all_courses)) {
                $current_course = $all_courses[0];
            }
        }

        // Access check
        $has_access = $is_admin || $db->is_user_enrolled($user_id, $current_course['id']);

        // Fetch current course modules, lessons, activities, tools
        $modules = $db->get_modules($current_course['id']);
        $modules_data = array();

        foreach ($modules as $m) {
            $lessons = $db->get_lessons($m['id']);
            $activities_raw = $db->get_activities($m['id']);
            $activities_list = array();
            if (!empty($activities_raw)) {
                foreach ($activities_raw as $act) {
                    $activities_list[] = $act['title'];
                }
            } else {
                $activities_list = array(
                    'Ejercicio de autodiagnóstico ALMA',
                    'Filtra el Caos en tiempo real',
                    'Compromiso de cambio mental en comunidad'
                );
            }

            // Parse or fallback tools and materials
            $tools_for_mod = array(
                'Plantilla de Trabajo (PDF editable)',
                'Hoja de Cálculo Tectónica (Google Sheets)'
            );
            $materials_for_mod = array(
                'Glosario Metodológico ALMA (PDF)',
                'Infografía de Arquitectura de Negocio'
            );

            $modules_data[] = array(
                'id'                => intval($m['id']),
                'title'             => $m['title'],
                'tag'               => $m['tag'],
                'color'             => $m['color'],
                'bgLight'           => $m['bg_light'],
                'sortOrder'         => intval($m['sort_order']),
                'estimatedTime'     => $m['estimated_time'],
                'difficulty'        => $m['difficulty'],
                'summary'           => $m['summary'],
                'targetDeliverable' => $m['target_deliverable'] ? $m['target_deliverable'] : 'Portafolio de Estructuración',
                'targetTool'        => $m['target_tool'] ? $m['target_tool'] : 'Studio Interactivo',
                'lessonsCount'      => count($lessons),
                'lessonsList'       => $lessons,
                'activities'        => $activities_list,
                'tools'             => $tools_for_mod,
                'newMaterials'      => $materials_for_mod
            );
        }

        $tools = $db->get_tools($current_course['id']);
        $progress = $db->get_user_progress($user_id, $current_course['id']);
        $deliverables = $db->get_user_deliverables($user_id, $current_course['id']);
        $matrix_data = $db->get_studio_data($user_id, $current_course['id'], 'matrix_2x2');
        $caos_data = $db->get_studio_data($user_id, $current_course['id'], 'filter_321');

        $booking_shortcode = get_option('academia_fluent_booking_shortcode');
        if (empty($booking_shortcode)) {
            $booking_shortcode = '[fluent_booking id="3"]';
        }

        $mentor_avatar_url = get_option('academia_mentor_avatar_url');
        if (empty($mentor_avatar_url)) {
            $mentor_avatar_url = 'https://studioalvarodiaz.es/wp-content/uploads/2026/08/WhatsApp-Image-2026-08-27-at-5.38.59-PM.jpeg';
        }

        $mentor_info = array(
            'name'             => get_option('academia_mentor_name', 'Dra. Erika Tatiana Parra'),
            'role'             => get_option('academia_mentor_role', 'Directora de Evaluación y mentora de estructuras ALMA'),
            'avatar'           => get_option('academia_mentor_avatar', '👩‍🏫'),
            'avatarUrl'        => $mentor_avatar_url,
            'price'            => get_option('academia_mentor_call_price', '97€'),
            'bookingUrl'       => get_option('academia_fluent_booking_url', 'https://alvarodiaz.com/reserva-mentoria'),
            'bookingShortcode' => $booking_shortcode
        );

        $current_user = wp_get_current_user();

        // Calculate Deliverables and Tools stats
        $total_deliverables = max(count($modules_data), 1);
        $completed_deliverables = 0;
        foreach ($modules_data as $mod) {
            if (isset($deliverables[$mod['id']]) && $deliverables[$mod['id']]['status'] === 'approved') {
                $completed_deliverables++;
            }
        }
        $graduation_percentage = round(($completed_deliverables / $total_deliverables) * 100);

        $total_tools = max(count($tools), 1);
        $done_tools = 0;
        foreach ($tools as $t) {
            if ($t['status'] === 'done') {
                $done_tools++;
            }
        }
        $action_percentage = round(($done_tools / $total_tools) * 100);

        // Localize Script
        wp_localize_script('academia-core-js', 'AcademiaData', array(
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('academia_nonce'),
            'userId'        => $user_id,
            'isAdmin'       => $is_admin,
            'userName'      => $current_user->display_name ? $current_user->display_name : $current_user->user_login,
            'userEmail'     => $current_user->user_email,
            'courses'       => $courses_data,
            'currentCourse' => $current_course,
            'hasAccess'     => $has_access,
            'modules'       => $modules_data,
            'tools'         => $tools,
            'progress'      => $progress,
            'deliverables'  => $deliverables,
            'studioMatrix'  => $matrix_data,
            'studioCaos'    => $caos_data,
            'mentor'        => $mentor_info,
            'stats'         => array(
                'modulesCount'        => count($modules_data),
                'actionDone'          => $done_tools,
                'actionTotal'         => count($tools),
                'actionPercentage'    => $action_percentage,
                'deliverablesDone'    => $completed_deliverables,
                'deliverablesTotal'   => count($modules_data),
                'graduationPercentage'=> $graduation_percentage
            )
        ));

        // Render template buffer
        ob_start();
        include ACADEMIA_PLUGIN_DIR . 'public/templates/layout.php';
        return ob_get_clean();
    }

    private function render_guest_view() {
        ob_start();
        ?>
        <div class="academia-guest-container" style="background:#0b0f19; color:#f3f4f6; border-radius:24px; padding:60px 24px; text-align:center; max-width:680px; margin:40px auto; border:1px solid #1f293d; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); font-family:system-ui, -apple-system, sans-serif;">
            <div style="font-size:56px; margin-bottom:16px;">🏛️</div>
            <h2 style="color:#ffffff; font-size:32px; font-weight:900; margin-bottom:12px; letter-spacing:-0.03em;">Academia Tectónica</h2>
            <p style="color:#94a3b8; font-size:16px; line-height:1.6; margin-bottom:32px; max-width:520px; margin-left:auto; margin-right:auto;">
                Metodología de Estructuración y Arquitectura de Negocios de Álvaro Díaz. Inicia sesión con tu cuenta de alumno para acceder al aula virtual, laboratorio interactivo y graduación.
            </p>
            <div style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
                <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" style="display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:#ffffff; font-weight:800; font-size:15px; padding:14px 32px; border-radius:12px; text-decoration:none; box-shadow:0 10px 25px -5px rgba(99,102,241,0.4); transition:all 0.2s ease;">
                    🔑 Iniciar Sesión en la Academia
                </a>
                <?php if (get_option('users_can_register')) : ?>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" style="display:inline-flex; align-items:center; justify-content:center; background:#1e293b; color:#cbd5e1; font-weight:700; font-size:15px; padding:14px 28px; border-radius:12px; text-decoration:none; border:1px solid #334155;">
                        Registrarme
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
