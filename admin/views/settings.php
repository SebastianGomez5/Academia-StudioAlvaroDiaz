<?php
/**
 * Admin View: Settings & WooCommerce / Mentor Configuration
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$courses_table = Academia_DB::get_table('courses');
$courses = $wpdb->get_results("SELECT * FROM {$courses_table} ORDER BY sort_order ASC", ARRAY_A);

$mentor_name             = get_option('academia_mentor_name', 'Dra. Erika Tatiana Parra');
$mentor_role             = get_option('academia_mentor_role', 'Directora de Evaluación y mentora de estructuras ALMA');
$mentor_avatar           = get_option('academia_mentor_avatar', '👩‍🏫');
$mentor_call_price       = get_option('academia_mentor_call_price', '97€');
$fluent_booking_url      = get_option('academia_fluent_booking_url', 'https://alvarodiaz.com/reserva-mentoria');
$fluent_booking_shortcode= get_option('academia_fluent_booking_shortcode', '');

$is_updated = isset($_GET['updated']) && $_GET['updated'] === '1';
?>

<div class="wrap academia-admin-wrap">
    <div class="academia-admin-header">
        <h1>⚙️ Ajustes Generales de la Academia Tectónica</h1>
        <p class="description">Configura los parámetros del mentor, la integración con WooCommerce y el agendamiento con FluentBooking.</p>
    </div>

    <?php if ($is_updated) : ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php _e('Ajustes guardados correctamente.', 'academia-tectonica'); ?></strong></p>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field('academia_save_settings', 'academia_save_settings_nonce'); ?>

        <div class="academia-settings-grid">
            <!-- Columna 1: Mentor & FluentBooking -->
            <div class="academia-card">
                <h2>👩‍🏫 Perfil de la Mentora / Evaluador</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="mentor_name">Nombre de la Mentora</label></th>
                        <td>
                            <input name="mentor_name" type="text" id="mentor_name" value="<?php echo esc_attr($mentor_name); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mentor_role">Cargo / Rol</label></th>
                        <td>
                            <input name="mentor_role" type="text" id="mentor_role" value="<?php echo esc_attr($mentor_role); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mentor_avatar">Avatar / Emoji</label></th>
                        <td>
                            <input name="mentor_avatar" type="text" id="mentor_avatar" value="<?php echo esc_attr($mentor_avatar); ?>" class="small-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mentor_avatar_url">Foto de la Mentora (1440x1440 px)</label></th>
                        <td>
                            <input name="mentor_avatar_url" type="url" id="mentor_avatar_url" value="<?php echo esc_attr(get_option('academia_mentor_avatar_url', ACADEMIA_PLUGIN_URL . 'public/images/erika-parra.jpg')); ?>" class="large-text" placeholder="https://studioalvarodiaz.es/wp-content/uploads/erika-1440x1440.jpg" />
                            <p class="description">URL directa de la fotografía en alta resolución (1440×1440 píxeles) para la Dra. Erika Tatiana Parra.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mentor_call_price">Precio Sesión 1 a 1</label></th>
                        <td>
                            <input name="mentor_call_price" type="text" id="mentor_call_price" value="<?php echo esc_attr($mentor_call_price); ?>" class="small-text" />
                            <p class="description">Ejemplo: 97€</p>
                        </td>
                    </tr>
                </table>

                <h2 style="margin-top:24px;">📅 Integración con FluentBooking (Mentoría 1 a 1)</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="fluent_booking_url">URL de Reserva FluentBooking</label></th>
                        <td>
                            <input name="fluent_booking_url" type="url" id="fluent_booking_url" value="<?php echo esc_attr($fluent_booking_url); ?>" class="large-text" />
                            <p class="description">Enlace directo a tu calendario de FluentBooking o página de checkout de la llamada.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="fluent_booking_shortcode">Shortcode de FluentBooking (Opcional)</label></th>
                        <td>
                            <input name="fluent_booking_shortcode" type="text" id="fluent_booking_shortcode" value="<?php echo esc_attr($fluent_booking_shortcode); ?>" class="large-text" placeholder="[fluent_booking_embed id='...']" />
                            <p class="description">Si prefieres incrustar el calendario en modal dentro de la academia.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Columna 2: Mapeo WooCommerce -->
            <div class="academia-card">
                <h2>🛒 Vinculación con Productos WooCommerce</h2>
                <p class="description">Cada vez que un cliente compre el producto de WooCommerce correspondiente, se le otorgará acceso inmediato al curso en la academia.</p>

                <table class="wp-list-table widefat fixed striped" style="margin-top:12px;">
                    <thead>
                        <tr>
                            <th>Curso de la Academia</th>
                            <th style="width:140px;">ID Producto WC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($c['title']); ?></strong>
                                    <?php if (!empty($c['is_free_default']) || $c['slug'] === 'm0') : ?>
                                        <br><small style="color:#059669;">🌱 Gratuito por defecto para todos los alumnos</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" name="wc_mapping[<?php echo esc_attr($c['id']); ?>]" value="<?php echo esc_attr($c['wc_product_id']); ?>" style="width:100px;" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2 style="margin-top:24px;">🚀 Shortcode de la Academia</h2>
                <p>Inserta este shortcode en cualquier página o plantilla de WordPress:</p>
                <code style="display:block; padding:12px; font-size:16px; background:#f1f5f9; border-radius:6px; border:1px solid #cbd5e1;">[academia_tectonica]</code>
            </div>
        </div>

        <p class="submit" style="margin-top:24px;">
            <button type="submit" class="button button-primary button-large">💾 Guardar Todos los Ajustes</button>
        </p>
    </form>
</div>
