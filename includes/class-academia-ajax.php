<?php
/**
 * AJAX Handlers for Academia Tectonica
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_AJAX {

    public function __construct() {
        // Student endpoints
        add_action('wp_ajax_academia_save_lesson_progress', array($this, 'save_lesson_progress'));
        add_action('wp_ajax_academia_save_studio_matrix', array($this, 'save_studio_matrix'));
        add_action('wp_ajax_academia_save_studio_caos', array($this, 'save_studio_caos'));
        add_action('wp_ajax_academia_submit_deliverable', array($this, 'submit_deliverable'));
        add_action('wp_ajax_academia_review_deliverable', array($this, 'review_deliverable'));
        add_action('wp_ajax_academia_save_impact_survey', array($this, 'save_impact_survey'));
        add_action('wp_ajax_academia_request_certification', array($this, 'request_certification'));

        // Mentor & Admin Dashboard endpoints
        add_action('wp_ajax_academia_mentor_save_audit', array($this, 'mentor_save_audit'));
        add_action('wp_ajax_academia_mentor_request_withdrawal', array($this, 'mentor_request_withdrawal'));
        add_action('wp_ajax_academia_mentor_update_lesson_cms', array($this, 'mentor_update_lesson_cms'));
        add_action('wp_ajax_academia_mentor_get_lesson_cms', array($this, 'mentor_get_lesson_cms'));
    }

    /**
     * Check nonce and logged in user
     */
    private function verify_request() {
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Debes iniciar sesión.', 'academia-tectonica')));
        }
        check_ajax_referer('academia_nonce', 'nonce');
    }

    /**
     * Save video watched / text read progress
     */
    public function save_lesson_progress() {
        $this->verify_request();

        $user_id   = get_current_user_id();
        $lesson_id = intval($_POST['lesson_id']);
        $type      = sanitize_text_field($_POST['type']); // 'watched' or 'read'
        $completed = !empty($_POST['completed']) ? true : false;
        $course_id = intval($_POST['course_id']);

        if (!$lesson_id || !in_array($type, array('watched', 'read'))) {
            wp_send_json_error(array('message' => __('Parámetros inválidos.', 'academia-tectonica')));
        }

        $db = new Academia_DB();
        $db->set_lesson_progress($user_id, $lesson_id, $type, $completed);
        $updated_progress = $db->get_user_progress($user_id, $course_id);

        wp_send_json_success(array(
            'message'  => __('Progreso guardado.', 'academia-tectonica'),
            'progress' => $updated_progress
        ));
    }

    /**
     * Save Matriz 2x2 items
     */
    public function save_studio_matrix() {
        $this->verify_request();

        $user_id   = get_current_user_id();
        $course_id = intval($_POST['course_id']);
        $decisions = isset($_POST['decisions']) ? json_decode(stripslashes($_POST['decisions']), true) : array();

        $clean_decisions = array();
        if (is_array($decisions)) {
            foreach ($decisions as $item) {
                $clean_decisions[] = array(
                    'id'     => intval($item['id']),
                    'text'   => sanitize_text_field($item['text']),
                    'impact' => sanitize_text_field($item['impact']),
                    'effort' => sanitize_text_field($item['effort'])
                );
            }
        }

        $db = new Academia_DB();
        $db->save_studio_data($user_id, $course_id, 'matrix_2x2', $clean_decisions);

        wp_send_json_success(array(
            'message'   => __('Matriz 2x2 guardada exitosamente.', 'academia-tectonica'),
            'decisions' => $clean_decisions
        ));
    }

    /**
     * Save Protocolo 3-2-1 (Filtro del Caos)
     */
    public function save_studio_caos() {
        $this->verify_request();

        $user_id   = get_current_user_id();
        $course_id = intval($_POST['course_id']);

        $payload = array(
            'caos1'      => sanitize_textarea_field($_POST['caos1']),
            'caos2'      => sanitize_textarea_field($_POST['caos2']),
            'caos3'      => sanitize_textarea_field($_POST['caos3']),
            'control1'   => sanitize_textarea_field($_POST['control1']),
            'control2'   => sanitize_textarea_field($_POST['control2']),
            'nextAction' => sanitize_textarea_field($_POST['nextAction']),
            'saved_at'   => current_time('mysql')
        );

        $db = new Academia_DB();
        $db->save_studio_data($user_id, $course_id, 'filter_321', $payload);

        wp_send_json_success(array(
            'message' => __('Filtro del Caos guardado con éxito.', 'academia-tectonica'),
            'data'    => $payload
        ));
    }

    /**
     * Submit a Deliverable (File or URL link)
     */
    public function submit_deliverable() {
        $this->verify_request();

        $user_id          = get_current_user_id();
        $course_id        = intval($_POST['course_id']);
        $module_id        = intval($_POST['module_id']);
        $deliverable_name = sanitize_text_field($_POST['deliverable_name']);
        $submission_type  = sanitize_text_field($_POST['submission_type']); // 'link' or 'file'
        $submission_url   = isset($_POST['submission_url']) ? esc_url_raw($_POST['submission_url']) : '';
        $file_path        = '';

        // Handle file upload if present
        if ($submission_type === 'file' && !empty($_FILES['deliverable_file'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $uploaded = wp_handle_upload($_FILES['deliverable_file'], array('test_form' => false));
            if (isset($uploaded['error'])) {
                wp_send_json_error(array('message' => $uploaded['error']));
            }
            $submission_url = $uploaded['url'];
            $file_path      = $uploaded['file'];
        }

        if (empty($submission_url)) {
            wp_send_json_error(array('message' => __('Debes proporcionar un enlace o subir un archivo.', 'academia-tectonica')));
        }

        $db = new Academia_DB();
        $deliverable_id = $db->save_deliverable(array(
            'user_id'          => $user_id,
            'course_id'        => $course_id,
            'module_id'        => $module_id,
            'deliverable_name' => $deliverable_name,
            'submission_type'  => $submission_type,
            'submission_url'   => $submission_url,
            'file_path'        => $file_path
        ));

        wp_send_json_success(array(
            'message'        => __('Entregable enviado a revisión.', 'academia-tectonica'),
            'deliverable_id' => $deliverable_id,
            'url'            => $submission_url,
            'status'         => 'pending'
        ));
    }

    /**
     * Instructor / Admin Review Deliverable
     */
    public function review_deliverable() {
        if (!current_user_can('academia_review_deliverables') && !current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permiso denegado.', 'academia-tectonica')));
        }
        check_ajax_referer('academia_nonce', 'nonce');

        $deliverable_id = intval($_POST['deliverable_id']);
        $status         = sanitize_text_field($_POST['status']); // 'approved' or 'needs_changes'
        $feedback       = sanitize_textarea_field($_POST['feedback']);
        $reviewer_id    = get_current_user_id();

        if (!$deliverable_id || !in_array($status, array('approved', 'needs_changes'))) {
            wp_send_json_error(array('message' => __('Datos de revisión inválidos.', 'academia-tectonica')));
        }

        $db = new Academia_DB();
        $db->review_deliverable($deliverable_id, $status, $feedback, $reviewer_id);

        wp_send_json_success(array(
            'message' => __('Evaluación guardada exitosamente.', 'academia-tectonica'),
            'status'  => $status
        ));
    }

    /**
     * Save Impact Survey
     */
    public function save_impact_survey() {
        $this->verify_request();

        $user_id   = get_current_user_id();
        $course_id = intval($_POST['course_id']);
        $responses = isset($_POST['responses']) ? json_decode(stripslashes($_POST['responses']), true) : array();

        global $wpdb;
        $table = Academia_DB::get_table('impact_surveys');

        $wpdb->insert($table, array(
            'user_id'        => $user_id,
            'course_id'      => $course_id,
            'responses_json' => wp_json_encode($responses),
            'created_at'     => current_time('mysql')
        ));

        wp_send_json_success(array(
            'message' => __('Encuesta de impacto enviada. ¡Gracias por tu retroalimentación!', 'academia-tectonica')
        ));
    }

    /**
     * Request Certification
     */
    public function request_certification() {
        $this->verify_request();

        $user_id   = get_current_user_id();
        $course_id = intval($_POST['course_id']);

        $db = new Academia_DB();
        $modules = $db->get_modules($course_id);
        $deliverables = $db->get_user_deliverables($user_id, $course_id);

        // Check if all modules have approved deliverables
        $all_approved = true;
        foreach ($modules as $m) {
            $mod_id = $m['id'];
            if (empty($deliverables[$mod_id]) || $deliverables[$mod_id]['status'] !== 'approved') {
                $all_approved = false;
                break;
            }
        }

        if (!$all_approved) {
            wp_send_json_error(array(
                'message' => __('Aún tienes entregables pendientes de aprobación por tu mentor antes de solicitar la certificación.', 'academia-tectonica')
            ));
        }

        // Mark enrollment as completed
        global $wpdb;
        $table = Academia_DB::get_table('enrollments');
        $wpdb->update($table, array('status' => 'completed'), array('user_id' => $user_id, 'course_id' => $course_id));

        wp_send_json_success(array(
            'message' => __('¡Felicidades! Tu solicitud de certificación ha sido aprobada y procesada.', 'academia-tectonica'),
            'certificate_ready' => true
        ));
    }

    /**
     * Mentor: Save audit decision (Approve / Request Changes)
     */
    public function mentor_save_audit() {
        $this->verify_request();

        if (!current_user_can('manage_options') && !current_user_can('academia_review_deliverables') && !current_user_can('academia_manage_all')) {
            wp_send_json_error(array('message' => __('No tienes permisos de evaluación docente.', 'academia-tectonica')));
        }

        $audit_id = sanitize_text_field($_POST['audit_id']);
        $decision = sanitize_text_field($_POST['decision']); // 'approved' or 'needs_changes'
        $feedback = sanitize_textarea_field($_POST['feedback']);

        if (empty($audit_id) || !in_array($decision, array('approved', 'needs_changes'))) {
            wp_send_json_error(array('message' => __('Datos de auditoría no válidos.', 'academia-tectonica')));
        }

        global $wpdb;
        $table = Academia_DB::get_table('deliverables');

        // If numeric deliverable ID in DB, update it
        if (is_numeric($audit_id)) {
            $wpdb->update($table, array(
                'status' => $decision,
                'feedback' => $feedback,
                'reviewed_by' => get_current_user_id(),
                'reviewed_at' => current_time('mysql')
            ), array('id' => intval($audit_id)));
        }

        wp_send_json_success(array(
            'message' => __('Dictamen oficial emitido con éxito.', 'academia-tectonica'),
            'status' => $decision,
            'feedback' => $feedback
        ));
    }

    /**
     * Mentor: Request Payout / Withdrawal
     */
    public function mentor_request_withdrawal() {
        $this->verify_request();

        $amount = floatval($_POST['amount']);
        $iban = sanitize_text_field($_POST['iban']);
        $tax_id = sanitize_text_field($_POST['tax_id']);
        $notes = sanitize_text_field($_POST['notes']);

        if ($amount <= 0 || empty($iban)) {
            wp_send_json_error(array('message' => __('Por favor ingresa un monto e IBAN válidos.', 'academia-tectonica')));
        }

        $receipt_id = 'TEC-REC-' . wp_rand(1000, 9999);
        $user = wp_get_current_user();

        // Save payout request in user meta history
        $history = get_user_meta($user->ID, 'academia_payout_history', true);
        if (!is_array($history)) {
            $history = array();
        }

        $new_payout = array(
            'id' => 'pay-' . time(),
            'date' => current_time('Y-m-d'),
            'amount' => $amount,
            'iban' => $iban,
            'recipient' => $user->display_name,
            'taxId' => $tax_id,
            'notes' => $notes,
            'status' => 'completed',
            'receiptId' => $receipt_id
        );

        array_unshift($history, $new_payout);
        update_user_meta($user->ID, 'academia_payout_history', $history);

        wp_send_json_success(array(
            'message' => sprintf(__('Transferencia SEPA de %s € solicitada y en trámite.', 'academia-tectonica'), number_format($amount, 2, ',', '.')),
            'payout' => $new_payout
        ));
    }

    /**
     * Mentor: Get Lesson data for CMS
     */
    public function mentor_get_lesson_cms() {
        $this->verify_request();

        $lesson_id = intval($_GET['lesson_id']);
        global $wpdb;
        $table = Academia_DB::get_table('lessons');
        $lesson = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $lesson_id), ARRAY_A);

        if (!$lesson) {
            wp_send_json_error(array('message' => __('Lección no encontrada.', 'academia-tectonica')));
        }

        wp_send_json_success(array('lesson' => $lesson));
    }

    /**
     * Mentor: Update Lesson in CMS
     */
    public function mentor_update_lesson_cms() {
        $this->verify_request();

        if (!current_user_can('manage_options') && !current_user_can('academia_edit_courses') && !current_user_can('academia_manage_all')) {
            wp_send_json_error(array('message' => __('No tienes permisos para editar contenidos.', 'academia-tectonica')));
        }

        $lesson_id = intval($_POST['lesson_id']);
        $title = sanitize_text_field($_POST['title']);
        $video_title = sanitize_text_field($_POST['video_title']);
        $video_url = sanitize_text_field($_POST['video_url']);
        $duration = sanitize_text_field($_POST['duration']);
        $what_learn = sanitize_textarea_field($_POST['what_you_will_learn']);
        $utility = sanitize_textarea_field($_POST['business_utility']);
        $reading = sanitize_textarea_field($_POST['reading_text']);

        if (!$lesson_id || empty($title)) {
            wp_send_json_error(array('message' => __('Datos requeridos incompletos.', 'academia-tectonica')));
        }

        global $wpdb;
        $table = Academia_DB::get_table('lessons');

        $wpdb->update($table, array(
            'title' => $title,
            'video_title' => $video_title,
            'video_url' => $video_url,
            'duration' => $duration,
            'what_you_will_learn' => $what_learn,
            'business_utility' => $utility,
            'reading_text' => $reading
        ), array('id' => $lesson_id));

        wp_send_json_success(array(
            'message' => __('Lección y vídeo actualizados en tiempo real.', 'academia-tectonica')
        ));
    }
}
