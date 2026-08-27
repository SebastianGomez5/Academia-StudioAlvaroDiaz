<?php
/**
 * Admin View: Courses & Content Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$courses_table = Academia_DB::get_table('courses');
$modules_table = Academia_DB::get_table('modules');
$lessons_table = Academia_DB::get_table('lessons');

$courses = $wpdb->get_results("SELECT * FROM {$courses_table} ORDER BY sort_order ASC", ARRAY_A);
?>

<div class="wrap academia-admin-wrap">
    <div class="academia-admin-header">
        <h1>📚 Catálogo de Cursos y Contenidos Tectónicos</h1>
        <p class="description">Gestión de la estructura de 8 cursos de la Academia de Negocios Digitales de Álvaro Díaz.</p>
    </div>

    <div class="academia-courses-grid">
        <?php foreach ($courses as $c) : 
            $mod_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$modules_table} WHERE course_id = %d", $c['id']));
            $les_count = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(l.id) FROM {$lessons_table} l 
                INNER JOIN {$modules_table} m ON l.module_id = m.id 
                WHERE m.course_id = %d
            ", $c['id']));
            $is_free = !empty($c['is_free_default']) || $c['slug'] === 'm0';
        ?>
            <div class="academia-course-card" style="border-top: 4px solid <?php echo esc_attr($c['color']); ?>;">
                <div class="course-card-top">
                    <span class="course-icon"><?php echo esc_html($c['icon']); ?></span>
                    <span class="course-badge" style="background:<?php echo esc_attr($c['color']); ?>20; color:<?php echo esc_attr($c['color']); ?>;">
                        <?php echo esc_html($c['code']); ?>
                    </span>
                </div>
                <h3 class="course-title"><?php echo esc_html($c['title']); ?></h3>
                <p class="course-desc"><?php echo esc_html($c['description']); ?></p>

                <div class="course-meta">
                    <div><strong>Módulos:</strong> <?php echo intval($mod_count); ?></div>
                    <div><strong>Lecciones:</strong> <?php echo intval($les_count); ?></div>
                    <div><strong>WC Producto ID:</strong> <code><?php echo esc_html($c['wc_product_id'] ? $c['wc_product_id'] : 'N/A'); ?></code></div>
                    <div>
                        <strong>Acceso:</strong> 
                        <?php if ($is_free) : ?>
                            <span class="badge-free">🌱 Incluido / Gratis</span>
                        <?php else : ?>
                            <span class="badge-paid">🔒 Requiere Compra</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="course-actions">
                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'academia-settings'), admin_url('admin.php'))); ?>" class="button button-small">⚙️ Mapeo WooCommerce</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
