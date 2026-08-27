<?php
/**
 * Admin Panel & Instructor review dashboard for Academia Tectonica
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'save_settings'));
        add_action('admin_init', array($this, 'handle_enrollment_actions'));
    }

    public function add_admin_menu() {
        // Main Menu -> Dashboard de Mentoría y Profesionales
        add_menu_page(
            __('Academia Tectónica', 'academia-tectonica'),
            __('Academia Tectónica', 'academia-tectonica'),
            'academia_review_deliverables',
            'academia-tectonica',
            array($this, 'render_mentor_dashboard_page'),
            'dashicons-welcome-learn-more',
            25
        );

        // Submenu: Dashboard Docente & Finanzas (Default)
        add_submenu_page(
            'academia-tectonica',
            __('Panel Docente & Finanzas', 'academia-tectonica'),
            __('Panel Docente & Finanzas', 'academia-tectonica'),
            'academia_review_deliverables',
            'academia-tectonica',
            array($this, 'render_mentor_dashboard_page')
        );

        // Submenu: Revisión de Entregables
        add_submenu_page(
            'academia-tectonica',
            __('Bandeja de Entregables', 'academia-tectonica'),
            __('Entregables y Tareas', 'academia-tectonica'),
            'academia_review_deliverables',
            'academia-submissions',
            array($this, 'render_submissions_page')
        );

        // Submenu: Cursos y Módulos
        add_submenu_page(
            'academia-tectonica',
            __('Cursos y Módulos', 'academia-tectonica'),
            __('Cursos y Contenidos', 'academia-tectonica'),
            'manage_options',
            'academia-courses',
            array($this, 'render_courses_page')
        );

        // Submenu: Matrículas de Alumnos
        add_submenu_page(
            'academia-tectonica',
            __('Matrículas de Alumnos', 'academia-tectonica'),
            __('Alumnos y Matrículas', 'academia-tectonica'),
            'academia_view_students',
            'academia-enrollments',
            array($this, 'render_enrollments_page')
        );

        // Submenu: Ajustes
        add_submenu_page(
            'academia-tectonica',
            __('Ajustes de la Academia', 'academia-tectonica'),
            __('Ajustes', 'academia-tectonica'),
            'manage_options',
            'academia-settings',
            array($this, 'render_settings_page')
        );
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'academia') === false) {
            return;
        }

        // Standard Admin CSS / JS
        wp_enqueue_style('academia-admin-css', ACADEMIA_PLUGIN_URL . 'admin/css/academia-admin.css', array(), ACADEMIA_VERSION . '.' . time());
        wp_enqueue_script('academia-admin-js', ACADEMIA_PLUGIN_URL . 'admin/js/academia-admin.js', array('jquery'), ACADEMIA_VERSION . '.' . time(), true);

        // Mentor Dashboard CSS / JS (Slate-950 Dark Theme)
        wp_enqueue_style('academia-mentor-dashboard-css', ACADEMIA_PLUGIN_URL . 'admin/css/academia-mentor-dashboard.css', array(), ACADEMIA_VERSION . '.' . time());
        wp_enqueue_script('academia-mentor-dashboard-js', ACADEMIA_PLUGIN_URL . 'admin/js/academia-mentor-dashboard.js', array('jquery'), ACADEMIA_VERSION . '.' . time(), true);

        wp_localize_script('academia-admin-js', 'AcademiaAdminData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('academia_nonce'),
        ));
    }

    public function render_mentor_dashboard_page() {
        include ACADEMIA_PLUGIN_DIR . 'admin/views/mentor-dashboard.php';
    }

    public function render_submissions_page() {
        include ACADEMIA_PLUGIN_DIR . 'admin/views/submissions.php';
    }

    public function render_courses_page() {
        include ACADEMIA_PLUGIN_DIR . 'admin/views/courses.php';
    }

    public function render_enrollments_page() {
        include ACADEMIA_PLUGIN_DIR . 'admin/views/enrollments.php';
    }

    public function render_settings_page() {
        include ACADEMIA_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public function save_settings() {
        if (!isset($_POST['academia_save_settings_nonce']) || !wp_verify_nonce($_POST['academia_save_settings_nonce'], 'academia_save_settings')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['mentor_name'])) {
            update_option('academia_mentor_name', sanitize_text_field($_POST['mentor_name']));
        }
        if (isset($_POST['mentor_role'])) {
            update_option('academia_mentor_role', sanitize_text_field($_POST['mentor_role']));
        }
        if (isset($_POST['mentor_avatar'])) {
            update_option('academia_mentor_avatar', sanitize_text_field($_POST['mentor_avatar']));
        }
        if (isset($_POST['mentor_avatar_url'])) {
            update_option('academia_mentor_avatar_url', esc_url_raw($_POST['mentor_avatar_url']));
        }
        if (isset($_POST['mentor_call_price'])) {
            update_option('academia_mentor_call_price', sanitize_text_field($_POST['mentor_call_price']));
        }
        if (isset($_POST['fluent_booking_url'])) {
            update_option('academia_fluent_booking_url', esc_url_raw($_POST['fluent_booking_url']));
        }
        if (isset($_POST['fluent_booking_shortcode'])) {
            update_option('academia_fluent_booking_shortcode', sanitize_text_field($_POST['fluent_booking_shortcode']));
        }

        // Save WC product IDs mappings
        if (isset($_POST['wc_mapping']) && is_array($_POST['wc_mapping'])) {
            global $wpdb;
            $table = Academia_DB::get_table('courses');
            foreach ($_POST['wc_mapping'] as $course_id => $wc_id) {
                $wpdb->update($table, array('wc_product_id' => intval($wc_id)), array('id' => intval($course_id)));
            }
        }

        wp_redirect(add_query_arg(array('page' => 'academia-settings', 'updated' => '1'), admin_url('admin.php')));
        exit;
    }

    public function handle_enrollment_actions() {
        if (!isset($_POST['academia_enrollment_nonce']) || !wp_verify_nonce($_POST['academia_enrollment_nonce'], 'academia_enrollment_action')) {
            return;
        }

        if (!current_user_can('academia_manage_all') && !current_user_can('manage_options')) {
            return;
        }

        $user_id   = intval($_POST['user_id']);
        $course_id = intval($_POST['course_id']);
        $action    = sanitize_text_field($_POST['enroll_action']);

        $db = new Academia_DB();
        global $wpdb;
        $table = Academia_DB::get_table('enrollments');

        if ($action === 'enroll' && $user_id && $course_id) {
            $db->enroll_user($user_id, $course_id);
        } elseif ($action === 'revoke' && $user_id && $course_id) {
            $wpdb->delete($table, array('user_id' => $user_id, 'course_id' => $course_id));
        }

        wp_redirect(add_query_arg(array('page' => 'academia-enrollments', 'msg' => 'success'), admin_url('admin.php')));
        exit;
    }
}
