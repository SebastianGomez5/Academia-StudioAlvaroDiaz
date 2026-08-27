<?php
/**
 * Tab 2: Acción / Herramientas Template (Matching React Mockup)
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="aca-tools-view-wrapper">
    <div class="aca-tab-intro-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h2 style="font-size:20px; font-weight:800; color:#0f172a; margin:0 0 4px 0;">⚡ Repositorio de Acción & Plantillas Operativas</h2>
                <p style="color:#64748b; font-size:13px; margin:0;">
                    Descarga y utiliza los frameworks metodológicos de Canva, Miro, Google Sheets y Notion para construir tus entregables oficiales.
                </p>
            </div>
            <div>
                <span style="font-size:12px; font-weight:800; background:#eef2ff; color:#4f46e5; padding:6px 12px; border-radius:8px;">
                    <?php echo count($tools); ?> Herramientas Disponibles
                </span>
            </div>
        </div>
    </div>

    <!-- Grid de Herramientas -->
    <div class="aca-tools-cards-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:20px;">
        <?php if (empty($tools)) : ?>
            <div style="grid-column: 1/-1; background:#ffffff; padding:40px; border-radius:16px; text-align:center; border:1px solid #e2e8f0; color:#64748b;">
                <em>No hay herramientas configuradas para este curso en este momento.</em>
            </div>
        <?php else : ?>
            <?php foreach ($tools as $t) : 
                $platform_colors = array(
                    'Canva' => '#7c3aed',
                    'Miro' => '#eab308',
                    'Notion' => '#0f172a',
                    'Google Sheets' => '#059669',
                    'PDF' => '#dc2626',
                    'Studio' => '#4f46e5'
                );
                $plat_color = '#64748b';
                foreach ($platform_colors as $plat => $col) {
                    if (stripos($t['platform'], $plat) !== false) {
                        $plat_color = $col;
                        break;
                    }
                }
            ?>
                <div class="aca-tool-item-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 1px 3px rgba(0,0,0,0.03); transition:all 0.2s ease;">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <span style="font-size:11px; font-weight:800; color:#ffffff; background:<?php echo esc_attr($plat_color); ?>; padding:3px 8px; border-radius:6px;">
                                <?php echo esc_html($t['platform']); ?>
                            </span>
                            <span style="font-size:14px;"><?php echo esc_html($t['priority']); ?></span>
                        </div>

                        <h4 style="font-size:16px; font-weight:800; color:#0f172a; margin:0 0 8px 0; line-height:1.3;">
                            <?php echo esc_html($t['name']); ?>
                        </h4>

                        <p style="font-size:13px; color:#64748b; line-height:1.5; margin:0 0 16px 0;">
                            <?php echo esc_html($t['objective'] ? $t['objective'] : 'Herramienta de estructuración operativa y toma de decisiones.'); ?>
                        </p>

                        <div style="font-size:12px; color:#475569; border-top:1px solid #f1f5f9; padding-top:10px; margin-bottom:14px; display:flex; justify-content:space-between;">
                            <span>⏱️ <?php echo esc_html($t['estimated_time']); ?></span>
                            <span>📊 Dificultad: <?php echo esc_html($t['difficulty']); ?></span>
                        </div>

                        <?php if (!empty($t['feeds_deliverable'])) : ?>
                            <div style="font-size:11px; background:#fffbeb; color:#92400e; border:1px solid #fde68a; padding:6px 10px; border-radius:8px; margin-bottom:16px;">
                                🎯 <strong>Aporta a:</strong> <?php echo esc_html($t['feeds_deliverable']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <a href="<?php echo esc_url($t['url'] ? $t['url'] : '#'); ?>" target="_blank" style="display:block; text-align:center; background:#f1f5f9; color:#0f172a; font-size:13px; font-weight:800; padding:10px; border-radius:10px; text-decoration:none; transition:all 0.15s ease;" onmouseover="this.style.background='#4f46e5'; this.style.color='#ffffff';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#0f172a';">
                        🚀 Abrir / Descargar Plantilla
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
