<?php
/**
 * Admin View: Deliverables Submissions Review
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$deliverables_table = Academia_DB::get_table('deliverables');
$courses_table      = Academia_DB::get_table('courses');
$modules_table      = Academia_DB::get_table('modules');

$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$filter_course = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$where = array('1=1');
if ($filter_status) {
    $where[] = $wpdb->prepare("d.status = %s", $filter_status);
}
if ($filter_course) {
    $where[] = $wpdb->prepare("d.course_id = %d", $filter_course);
}
$where_clause = implode(' AND ', $where);

$submissions = $wpdb->get_results("
    SELECT d.*, c.title as course_title, m.title as module_title, u.display_name, u.user_email 
    FROM {$deliverables_table} d
    LEFT JOIN {$courses_table} c ON d.course_id = c.id
    LEFT JOIN {$modules_table} m ON d.module_id = m.id
    LEFT JOIN {$wpdb->users} u ON d.user_id = u.ID
    WHERE {$where_clause}
    ORDER BY d.id DESC
", ARRAY_A);

$courses = $wpdb->get_results("SELECT id, title FROM {$courses_table} ORDER BY sort_order ASC", ARRAY_A);
?>

<div class="wrap academia-admin-wrap">
    <div class="academia-admin-header">
        <h1>🏛️ Bandeja de Entregables · Mentoría & Evaluación</h1>
        <p class="description">Revisa los entregables presentados por los alumnos para su certificación en la metodología ALMA de Álvaro Díaz.</p>
    </div>

    <!-- Filters -->
    <div class="academia-admin-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="academia-tectonica" />
            <select name="status">
                <option value=""><?php _e('Todos los estados', 'academia-tectonica'); ?></option>
                <option value="pending" <?php selected($filter_status, 'pending'); ?>><?php _e('🟡 Pendiente de Revisión', 'academia-tectonica'); ?></option>
                <option value="approved" <?php selected($filter_status, 'approved'); ?>><?php _e('🟢 Aprobado', 'academia-tectonica'); ?></option>
                <option value="needs_changes" <?php selected($filter_status, 'needs_changes'); ?>><?php _e('🔴 Requiere Cambios', 'academia-tectonica'); ?></option>
            </select>

            <select name="course_id">
                <option value="0"><?php _e('Todos los Cursos', 'academia-tectonica'); ?></option>
                <?php foreach ($courses as $c) : ?>
                    <option value="<?php echo esc_attr($c['id']); ?>" <?php selected($filter_course, $c['id']); ?>>
                        <?php echo esc_html($c['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="button button-secondary"><?php _e('Filtrar', 'academia-tectonica'); ?></button>
        </form>
    </div>

    <!-- Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width:50px;">ID</th>
                <th>Alumno</th>
                <th>Curso / Módulo</th>
                <th>Entregable</th>
                <th>Evidencia</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th style="width:140px;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)) : ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:30px;">
                        <em><?php _e('No hay entregables que coincidan con el filtro.', 'academia-tectonica'); ?></em>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($submissions as $sub) : ?>
                    <tr id="sub-row-<?php echo esc_attr($sub['id']); ?>">
                        <td><strong>#<?php echo esc_html($sub['id']); ?></strong></td>
                        <td>
                            <strong><?php echo esc_html($sub['display_name']); ?></strong><br>
                            <small style="color:#64748b;"><?php echo esc_html($sub['user_email']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo esc_html($sub['course_title']); ?></strong><br>
                            <span class="academia-badge-sub"><?php echo esc_html($sub['module_title']); ?></span>
                        </td>
                        <td>
                            <strong><?php echo esc_html($sub['deliverable_name']); ?></strong>
                        </td>
                        <td>
                            <?php if (!empty($sub['submission_url'])) : ?>
                                <a href="<?php echo esc_url($sub['submission_url']); ?>" target="_blank" class="button button-small" style="display:inline-flex; align-items:center; gap:4px;">
                                    🔗 <?php echo ($sub['submission_type'] === 'file') ? __('Ver Archivo', 'academia-tectonica') : __('Abrir Enlace', 'academia-tectonica'); ?>
                                </a>
                            <?php else : ?>
                                <em>Sin evidencia</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($sub['status'] === 'approved') : ?>
                                <span class="academia-status-tag status-approved">🟢 Aprobado</span>
                            <?php elseif ($sub['status'] === 'needs_changes') : ?>
                                <span class="academia-status-tag status-changes">🔴 Requiere Cambios</span>
                            <?php else : ?>
                                <span class="academia-status-tag status-pending">🟡 En Revisión</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($sub['created_at']))); ?></small>
                        </td>
                        <td>
                            <button type="button" class="button button-primary btn-open-review" 
                                    data-id="<?php echo esc_attr($sub['id']); ?>"
                                    data-student="<?php echo esc_attr($sub['display_name']); ?>"
                                    data-deliverable="<?php echo esc_attr($sub['deliverable_name']); ?>"
                                    data-url="<?php echo esc_url($sub['submission_url']); ?>"
                                    data-status="<?php echo esc_attr($sub['status']); ?>"
                                    data-feedback="<?php echo esc_attr($sub['mentor_feedback']); ?>">
                                ✍️ Evaluar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Evaluación -->
<div id="academia-review-modal" class="academia-modal-backdrop" style="display:none;">
    <div class="academia-modal-box">
        <div class="academia-modal-header">
            <h3>Evaluación del Entregable</h3>
            <button type="button" class="academia-modal-close">&times;</button>
        </div>
        <form id="academia-review-form">
            <input type="hidden" name="deliverable_id" id="modal-deliverable-id" value="" />
            
            <div class="form-group" style="margin-bottom:12px;">
                <label><strong>Alumno:</strong> <span id="modal-student-name"></span></label>
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label><strong>Entregable:</strong> <span id="modal-deliverable-title"></span></label>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label><strong>Enlace / Archivo:</strong></label>
                <div><a id="modal-submission-link" href="#" target="_blank" class="button button-secondary">🔗 Abrir trabajo del alumno</a></div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label for="modal-status"><strong>Veredicto del Mentor:</strong></label>
                <select name="status" id="modal-status" class="widefat" style="margin-top:6px;">
                    <option value="approved">🟢 Aprobar Entregable</option>
                    <option value="needs_changes">🔴 Solicitar Cambios / Devolver al Alumno</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label for="modal-feedback"><strong>Retroalimentación / Comentarios de la Mentora:</strong></label>
                <textarea name="feedback" id="modal-feedback" rows="5" class="widefat" style="margin-top:6px;" placeholder="Escribe aquí las observaciones, mejoras o felicitaciones para el alumno..."></textarea>
            </div>

            <div class="academia-modal-footer" style="text-align:right;">
                <button type="button" class="button button-secondary academia-modal-close">Cancelar</button>
                <button type="submit" class="button button-primary" id="btn-save-review">Guardar Evaluación</button>
            </div>
        </form>
    </div>
</div>
