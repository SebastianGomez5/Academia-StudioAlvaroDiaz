<?php
/**
 * Admin View: Student Enrollments
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$enrollments_table = Academia_DB::get_table('enrollments');
$courses_table     = Academia_DB::get_table('courses');

$all_courses = $wpdb->get_results("SELECT id, title, short_name FROM {$courses_table} ORDER BY sort_order ASC", ARRAY_A);
$all_users   = get_users(array('number' => 150, 'orderby' => 'display_name'));

$enrollments = $wpdb->get_results("
    SELECT e.*, c.title as course_title, u.display_name, u.user_email 
    FROM {$enrollments_table} e
    LEFT JOIN {$courses_table} c ON e.course_id = c.id
    LEFT JOIN {$wpdb->users} u ON e.user_id = u.ID
    ORDER BY e.id DESC
    LIMIT 200
", ARRAY_A);
?>

<div class="wrap academia-admin-wrap">
    <div class="academia-admin-header">
        <h1>👥 Matrículas de Alumnos</h1>
        <p class="description">Control de accesos y matriculación manual de alumnos a los cursos de la Academia Tectónica.</p>
    </div>

    <!-- Manual Enrollment Box -->
    <div class="academia-box" style="background:#ffffff; border:1px solid #ccd0d4; padding:20px; border-radius:8px; margin-bottom:24px;">
        <h2>Matricular Alumno Manualmente</h2>
        <form method="post" action="">
            <?php wp_nonce_field('academia_enrollment_action', 'academia_enrollment_nonce'); ?>
            <input type="hidden" name="enroll_action" value="enroll" />
            <div style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">
                <div style="flex:1; min-width:240px;">
                    <label for="user_id"><strong>Seleccionar Alumno / Usuario:</strong></label>
                    <select name="user_id" id="user_id" class="widefat" required style="margin-top:6px;">
                        <option value="">-- Seleccionar Alumno --</option>
                        <?php foreach ($all_users as $u) : ?>
                            <option value="<?php echo esc_attr($u->ID); ?>">
                                <?php echo esc_html($u->display_name); ?> (<?php echo esc_html($u->user_email); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex:1; min-width:240px;">
                    <label for="course_id"><strong>Seleccionar Curso:</strong></label>
                    <select name="course_id" id="course_id" class="widefat" required style="margin-top:6px;">
                        <option value="">-- Seleccionar Curso --</option>
                        <?php foreach ($all_courses as $c) : ?>
                            <option value="<?php echo esc_attr($c['id']); ?>">
                                <?php echo esc_html($c['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <button type="submit" class="button button-primary">➕ Matricular Alumno</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table of Enrollments -->
    <h2>Matrículas Activas</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th>Alumno</th>
                <th>Curso</th>
                <th>Estado</th>
                <th>Pedido WooCommerce</th>
                <th>Fecha Matrícula</th>
                <th style="width:120px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($enrollments)) : ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:24px;">
                        <em>No hay matrículas registradas aún.</em>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($enrollments as $e) : ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($e['id']); ?></strong></td>
                        <td>
                            <strong><?php echo esc_html($e['display_name']); ?></strong><br>
                            <small style="color:#64748b;"><?php echo esc_html($e['user_email']); ?></small>
                        </td>
                        <td><strong><?php echo esc_html($e['course_title']); ?></strong></td>
                        <td>
                            <?php if ($e['status'] === 'completed') : ?>
                                <span class="academia-status-tag status-approved">🎓 Graduado</span>
                            <?php else : ?>
                                <span class="academia-status-tag status-pending">⚡ Activo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $e['wc_order_id'] ? '<code>#' . esc_html($e['wc_order_id']) . '</code>' : '<em>Manual</em>'; ?>
                        </td>
                        <td>
                            <small><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($e['enrolled_at']))); ?></small>
                        </td>
                        <td>
                            <form method="post" action="" onsubmit="return confirm('¿Seguro que deseas revocar el acceso a este curso?');">
                                <?php wp_nonce_field('academia_enrollment_action', 'academia_enrollment_nonce'); ?>
                                <input type="hidden" name="enroll_action" value="revoke" />
                                <input type="hidden" name="user_id" value="<?php echo esc_attr($e['user_id']); ?>" />
                                <input type="hidden" name="course_id" value="<?php echo esc_attr($e['course_id']); ?>" />
                                <button type="submit" class="button button-link-delete" style="color:#b91c1c;">Revocar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
