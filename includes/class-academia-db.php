<?php
/**
 * Database helper class for Academia Tectonica
 */

if (!defined('ABSPATH')) {
    exit;
}

class Academia_DB {

    public static function get_table($name) {
        global $wpdb;
        return $wpdb->prefix . 'academia_' . $name;
    }

    /**
     * Get all courses
     */
    public function get_courses() {
        global $wpdb;
        $table = self::get_table('courses');
        return $wpdb->get_results("SELECT * FROM {$table} ORDER BY sort_order ASC", ARRAY_A);
    }

    /**
     * Get course by ID or slug
     */
    public function get_course($id_or_slug) {
        global $wpdb;
        $table = self::get_table('courses');
        if (is_numeric($id_or_slug)) {
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id_or_slug), ARRAY_A);
        }
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE slug = %s", $id_or_slug), ARRAY_A);
    }

    /**
     * Get modules for a course
     */
    public function get_modules($course_id) {
        global $wpdb;
        $table = self::get_table('modules');
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE course_id = %d ORDER BY sort_order ASC", $course_id), ARRAY_A);
    }

    /**
     * Get lessons for a module
     */
    public function get_lessons($module_id) {
        global $wpdb;
        $table = self::get_table('lessons');
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE module_id = %d ORDER BY sort_order ASC", $module_id), ARRAY_A);
    }

    /**
     * Get activities for a module or lesson
     */
    public function get_activities($module_id, $lesson_id = null) {
        global $wpdb;
        $table = self::get_table('activities');
        if ($lesson_id) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE lesson_id = %d ORDER BY id ASC", $lesson_id), ARRAY_A);
        }
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE module_id = %d ORDER BY id ASC", $module_id), ARRAY_A);
    }

    /**
     * Get tools for a course
     */
    public function get_tools($course_id) {
        global $wpdb;
        $table = self::get_table('tools');
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE course_id = %d ORDER BY id ASC", $course_id), ARRAY_A);
    }

    /**
     * Check if user is enrolled in course
     */
    public function is_user_enrolled($user_id, $course_id) {
        if (!$user_id) {
            return false;
        }

        // Course 0 is free and available to all registered users by default
        $course = $this->get_course($course_id);
        if ($course && (!empty($course['is_free_default']) || $course['slug'] === 'm0' || $course['id'] == 1)) {
            return true;
        }

        // Administrators and instructors have access to all
        if (current_user_can('manage_options') || current_user_can('academia_manage_all')) {
            return true;
        }

        global $wpdb;
        $table = self::get_table('enrollments');
        $enrolled = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND course_id = %d AND status = 'active'", $user_id, $course_id));

        return ($enrolled > 0);
    }

    /**
     * Enroll user in course
     */
    public function enroll_user($user_id, $course_id, $wc_order_id = null) {
        global $wpdb;
        $table = self::get_table('enrollments');
        
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE user_id = %d AND course_id = %d", $user_id, $course_id));
        if ($exists) {
            return $wpdb->update($table, array(
                'status' => 'active',
                'wc_order_id' => $wc_order_id,
                'enrolled_at' => current_time('mysql')
            ), array('id' => $exists));
        }

        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'course_id' => $course_id,
            'wc_order_id' => $wc_order_id,
            'status' => 'active',
            'enrolled_at' => current_time('mysql')
        ));
    }

    /**
     * Get user progress for a course
     */
    public function get_user_progress($user_id, $course_id) {
        global $wpdb;
        $progress_table = self::get_table('user_progress');
        $lessons_table  = self::get_table('lessons');
        $modules_table  = self::get_table('modules');

        $rows = $wpdb->get_results($wpdb->prepare("
            SELECT p.*, l.id as lesson_id, l.lesson_code, l.module_id 
            FROM {$progress_table} p
            INNER JOIN {$lessons_table} l ON p.lesson_id = l.id
            INNER JOIN {$modules_table} m ON l.module_id = m.id
            WHERE p.user_id = %d AND m.course_id = %d
        ", $user_id, $course_id), ARRAY_A);

        $progress_data = array(
            'watched' => array(),
            'read' => array(),
            'completed_count' => 0,
            'total_lessons' => 0,
            'percentage' => 0
        );

        // Get total lessons count for this course
        $total = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(l.id) 
            FROM {$lessons_table} l
            INNER JOIN {$modules_table} m ON l.module_id = m.id
            WHERE m.course_id = %d
        ", $course_id));

        $progress_data['total_lessons'] = intval($total);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['completed'])) {
                    $progress_data['watched'][$row['lesson_id']] = true;
                    $progress_data['watched'][$row['lesson_code']] = true;
                }
                if (!empty($row['read_completed'])) {
                    $progress_data['read'][$row['lesson_id']] = true;
                    $progress_data['read'][$row['lesson_code']] = true;
                }
            }
            $progress_data['completed_count'] = count($progress_data['watched']);
        }

        if ($progress_data['total_lessons'] > 0) {
            $progress_data['percentage'] = round(($progress_data['completed_count'] / $progress_data['total_lessons']) * 100);
        }

        return $progress_data;
    }

    /**
     * Update user lesson progress
     */
    public function set_lesson_progress($user_id, $lesson_id, $type, $is_completed = true) {
        global $wpdb;
        $table = self::get_table('user_progress');

        $exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE user_id = %d AND lesson_id = %d", $user_id, $lesson_id), ARRAY_A);

        $now = current_time('mysql');
        $val = $is_completed ? 1 : 0;

        if ($exists) {
            $update_data = array('updated_at' => $now);
            if ($type === 'watched') {
                $update_data['completed'] = $val;
            } elseif ($type === 'read') {
                $update_data['read_completed'] = $val;
            }
            return $wpdb->update($table, $update_data, array('id' => $exists['id']));
        } else {
            $insert_data = array(
                'user_id' => $user_id,
                'lesson_id' => $lesson_id,
                'completed' => ($type === 'watched' ? $val : 0),
                'read_completed' => ($type === 'read' ? $val : 0),
                'updated_at' => $now
            );
            return $wpdb->insert($table, $insert_data);
        }
    }

    /**
     * Get Deliverables for course and user
     */
    public function get_user_deliverables($user_id, $course_id) {
        global $wpdb;
        $table = self::get_table('deliverables');
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$table} 
            WHERE user_id = %d AND course_id = %d 
            ORDER BY module_id ASC, id DESC
        ", $user_id, $course_id), ARRAY_A);

        $grouped = array();
        if ($results) {
            foreach ($results as $res) {
                $grouped[$res['module_id']] = $res;
                if (!empty($res['deliverable_name'])) {
                    $grouped[$res['deliverable_name']] = $res;
                }
            }
        }
        return $grouped;
    }

    /**
     * Submit or update deliverable
     */
    public function save_deliverable($data) {
        global $wpdb;
        $table = self::get_table('deliverables');

        $existing = $wpdb->get_row($wpdb->prepare("
            SELECT id FROM {$table} 
            WHERE user_id = %d AND course_id = %d AND module_id = %d
        ", $data['user_id'], $data['course_id'], $data['module_id']), ARRAY_A);

        $payload = array(
            'user_id'          => $data['user_id'],
            'course_id'        => $data['course_id'],
            'module_id'        => $data['module_id'],
            'deliverable_name' => sanitize_text_field($data['deliverable_name']),
            'submission_type'  => sanitize_text_field($data['submission_type']),
            'submission_url'   => esc_url_raw($data['submission_url']),
            'file_path'        => sanitize_text_field(isset($data['file_path']) ? $data['file_path'] : ''),
            'status'           => 'pending',
            'created_at'       => current_time('mysql')
        );

        if ($existing) {
            $wpdb->update($table, $payload, array('id' => $existing['id']));
            return $existing['id'];
        } else {
            $wpdb->insert($table, $payload);
            return $wpdb->insert_id;
        }
    }

    /**
     * Review deliverable (Instructor / Admin)
     */
    public function review_deliverable($deliverable_id, $status, $feedback, $reviewer_id) {
        global $wpdb;
        $table = self::get_table('deliverables');

        return $wpdb->update($table, array(
            'status'          => sanitize_text_field($status),
            'mentor_feedback' => sanitize_textarea_field($feedback),
            'reviewed_by'     => intval($reviewer_id),
            'reviewed_at'     => current_time('mysql')
        ), array('id' => intval($deliverable_id)));
    }

    /**
     * Save/Get Studio Data
     */
    public function get_studio_data($user_id, $course_id, $studio_type) {
        global $wpdb;
        $table = self::get_table('studio_data');
        $row = $wpdb->get_row($wpdb->prepare("
            SELECT data_json, updated_at FROM {$table} 
            WHERE user_id = %d AND course_id = %d AND studio_type = %s
        ", $user_id, $course_id, $studio_type), ARRAY_A);

        if ($row && !empty($row['data_json'])) {
            return json_decode($row['data_json'], true);
        }
        return null;
    }

    public function save_studio_data($user_id, $course_id, $studio_type, $data) {
        global $wpdb;
        $table = self::get_table('studio_data');
        $existing = $wpdb->get_row($wpdb->prepare("
            SELECT id FROM {$table} 
            WHERE user_id = %d AND course_id = %d AND studio_type = %s
        ", $user_id, $course_id, $studio_type), ARRAY_A);

        $json = wp_json_encode($data);
        $now  = current_time('mysql');

        if ($existing) {
            return $wpdb->update($table, array(
                'data_json' => $json,
                'updated_at' => $now
            ), array('id' => $existing['id']));
        } else {
            return $wpdb->insert($table, array(
                'user_id'     => $user_id,
                'course_id'   => $course_id,
                'studio_type' => $studio_type,
                'data_json'   => $json,
                'updated_at'  => $now
            ));
        }
    }

    /**
     * Get courses assigned to an instructor / user
     */
    public function get_user_assigned_courses($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            return array('m0', 'c2');
        }

        // Administrator has access to all courses
        if (user_can($user, 'manage_options') || in_array('administrator', (array)$user->roles)) {
            $all_courses = $this->get_courses();
            return wp_list_pluck($all_courses, 'slug');
        }

        $assigned = get_user_meta($user_id, 'academia_assigned_courses', true);
        if (!empty($assigned) && is_array($assigned)) {
            return $assigned;
        }

        // Default fallback for instructors (e.g. Erika Parra -> c2, m0)
        return array('m0', 'c2');
    }

    /**
     * Set assigned courses for a mentor
     */
    public function set_user_assigned_courses($user_id, $course_slugs) {
        if (!is_array($course_slugs)) {
            $course_slugs = array();
        }
        return update_user_meta($user_id, 'academia_assigned_courses', $course_slugs);
    }

    /**
     * Get course stats (students, revenue, deliverables)
     */
    public function get_course_stats($course_id) {
        global $wpdb;
        $enrollments_table = self::get_table('enrollments');
        $deliverables_table = self::get_table('deliverables');

        $students_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM {$enrollments_table} WHERE course_id = %d AND status = 'active'",
            $course_id
        ));

        $pending_audits = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$deliverables_table} WHERE course_id = %d AND status = 'pending'",
            $course_id
        ));

        return array(
            'students' => intval($students_count),
            'pending_audits' => intval($pending_audits)
        );
    }
}
