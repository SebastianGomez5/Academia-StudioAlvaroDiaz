<?php
/**
 * Tab 1: Conocimiento / Módulos Template (Matching React Mockup)
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="aca-modules-view-wrapper">
    <!-- LEYENDA FIJA DE LOS 4 EJES DE ALMA (A - L - M - A) -->
    <div class="aca-ejes-alma-bar">
        <div class="aca-ejes-title">
            <span>🏷️</span>
            <span>EJES METODOLÓGICOS ALMA:</span>
        </div>
        <div class="aca-ejes-pills">
            <span class="aca-eje-pill">
                <span class="aca-dot" style="background:#818cf8;"></span>
                <strong>A:</strong> Alineación
            </span>
            <span class="aca-eje-pill">
                <span class="aca-dot" style="background:#2dd4bf;"></span>
                <strong>L:</strong> Liderazgo
            </span>
            <span class="aca-eje-pill">
                <span class="aca-dot" style="background:#fb7185;"></span>
                <strong>M:</strong> Mensaje
            </span>
            <span class="aca-eje-pill">
                <span class="aca-dot" style="background:#34d399;"></span>
                <strong>A:</strong> Acción
            </span>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros de Ejes -->
    <div class="aca-search-filter-bar">
        <div class="aca-search-input-wrap">
            <span class="aca-search-icon">🔍</span>
            <input type="text" id="aca-module-search-input" class="aca-search-input" placeholder="Buscar lecciones, herramientas o entregables..." />
        </div>
        <div class="aca-filter-chips">
            <button type="button" class="aca-filter-chip active" data-tag="Todos">Todos</button>
            <button type="button" class="aca-filter-chip" data-tag="A · Alineación">A · Alineación</button>
            <button type="button" class="aca-filter-chip" data-tag="L · Liderazgo">L · Liderazgo</button>
            <button type="button" class="aca-filter-chip" data-tag="M · Mensaje">M · Mensaje</button>
            <button type="button" class="aca-filter-chip" data-tag="A · Acción">A · Acción</button>
        </div>
    </div>

    <!-- Listado de Módulos (Cards Expandibles) -->
    <div class="aca-modules-list-grid">
        <?php if (empty($modules_data)) : ?>
            <div class="aca-empty-state">
                <div style="font-size:36px; margin-bottom:8px;">📚</div>
                <h3>No hay módulos configurados para este curso aún.</h3>
                <p>El contenido se estructurará desde el panel de administración.</p>
            </div>
        <?php else : ?>
            <?php foreach ($modules_data as $idx => $mod) : 
                $is_expanded = ($idx === 0);
                $first_lesson = !empty($mod['lessonsList']) ? $mod['lessonsList'][0] : null;
                $active_lesson_id = $first_lesson ? $first_lesson['id'] : 0;
            ?>
                <div class="aca-module-card <?php echo $is_expanded ? 'expanded' : ''; ?>" 
                     id="module-card-<?php echo esc_attr($mod['id']); ?>"
                     data-module-id="<?php echo esc_attr($mod['id']); ?>"
                     data-tag="<?php echo esc_attr($mod['tag']); ?>"
                     style="border-left: 6px solid <?php echo esc_attr($mod['color']); ?>;">
                    
                    <!-- ENCABEZADO DEL MÓDULO (Clic para desplegar) -->
                    <div class="aca-module-card-header" data-toggle-module="<?php echo esc_attr($mod['id']); ?>">
                        <div class="aca-module-header-left">
                            <div class="aca-module-num-badge" style="background-color: <?php echo esc_attr($mod['color']); ?>;">
                                0<?php echo ($idx + 1); ?>
                            </div>
                            <div>
                                <h3 class="aca-module-main-title"><?php echo esc_html($mod['title']); ?></h3>
                                <div class="aca-module-meta-chips">
                                    <span class="aca-chip-tag" style="background-color: <?php echo esc_attr($mod['bgLight']); ?>; color: <?php echo esc_attr($mod['color']); ?>;">
                                        <?php echo esc_html($mod['tag']); ?>
                                    </span>
                                    <span class="aca-meta-dot">•</span>
                                    <span class="aca-meta-text"><strong><?php echo intval($mod['lessonsCount']); ?></strong> lecciones en Vídeo HD</span>
                                    <span class="aca-meta-dot">•</span>
                                    <span class="aca-meta-text">⏱️ <?php echo esc_html($mod['estimatedTime']); ?></span>
                                    <span class="aca-meta-dot">•</span>
                                    <span class="aca-meta-text">Dificultad: <?php echo esc_html($mod['difficulty']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="aca-module-arrow">
                            <span class="aca-arrow-icon"><?php echo $is_expanded ? '▲' : '▼'; ?></span>
                        </div>
                    </div>

                    <!-- CONTENIDO DESPLEGADO DEL MÓDULO -->
                    <div class="aca-module-card-body" <?php echo !$is_expanded ? 'style="display:none;"' : ''; ?>>
                        
                        <!-- BLOQUE 1: Resumen del Módulo -->
                        <div class="aca-module-summary-box">
                            <span class="aca-summary-icon">🎯</span>
                            <div>
                                <span class="aca-summary-title">RESUMEN DEL MÓDULO</span>
                                <p class="aca-summary-desc"><?php echo esc_html($mod['summary']); ?></p>
                            </div>
                        </div>

                        <?php if (!empty($mod['lessonsList'])) : ?>
                            <!-- BLOQUE 2: Reproductor de Vídeo Dinámico -->
                            <div class="aca-video-stage-box" id="video-stage-mod-<?php echo esc_attr($mod['id']); ?>">
                                <div class="aca-video-stage-header">
                                    <h4><span>📺</span> LECCIONES Y REPRODUCTOR DE VÍDEO PRINCIPAL</h4>
                                    <span>100% Contenido en Vídeo HD (<?php echo count($mod['lessonsList']); ?> Lecciones)</span>
                                </div>

                                <!-- Dark Video Container -->
                                <div class="aca-video-player-dark">
                                    <div class="aca-player-top-bar">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span class="aca-pulse-dot"></span>
                                            <span class="aca-player-lesson-code" id="player-code-<?php echo esc_attr($mod['id']); ?>">
                                                <?php echo esc_html($first_lesson['lesson_code']); ?> · <?php echo esc_html($first_lesson['type']); ?>
                                            </span>
                                        </div>
                                        <span class="aca-player-duration" id="player-duration-<?php echo esc_attr($mod['id']); ?>">
                                            HD 1080p • ⏱️ <?php echo esc_html($first_lesson['duration']); ?>
                                        </span>
                                    </div>

                                    <div class="aca-player-grid">
                                        <!-- Video Screen (Responsive 16:9 Embed) -->
                                        <div class="aca-video-screen" id="video-screen-<?php echo esc_attr($mod['id']); ?>" style="padding:0; min-height:280px; position:relative; background:#000000; overflow:hidden;">
                                            <?php 
                                                $v_url = !empty($first_lesson['video_url']) ? $first_lesson['video_url'] : 'https://youtu.be/MeKlBPHgmJ0';
                                                $embed_src = 'https://www.youtube-nocookie.com/embed/MeKlBPHgmJ0?rel=0';
                                                if (strpos($v_url, 'youtu') !== false) {
                                                    preg_match('/(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*)/', $v_url, $matches);
                                                    $yt_id = (isset($matches[2]) && strlen($matches[2]) === 11) ? $matches[2] : 'MeKlBPHgmJ0';
                                                    $embed_src = 'https://www.youtube-nocookie.com/embed/' . $yt_id . '?rel=0';
                                                } elseif (strpos($v_url, 'vimeo') !== false) {
                                                    $vimeo_id = substr(strrchr($v_url, '/'), 1);
                                                    $embed_src = 'https://player.vimeo.com/video/' . $vimeo_id;
                                                }
                                            ?>
                                            <iframe id="iframe-player-<?php echo esc_attr($mod['id']); ?>" 
                                                    src="<?php echo esc_url($embed_src); ?>" 
                                                    style="width:100%; height:100%; min-height:280px; border:none;" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen></iframe>
                                        </div>

                                        <!-- Lesson Info Sidebar inside Player -->
                                        <div class="aca-player-info-side">
                                            <div>
                                                <span class="aca-type-badge" id="player-type-<?php echo esc_attr($mod['id']); ?>">
                                                    <?php echo esc_html($first_lesson['type']); ?>
                                                </span>
                                                <h5 class="aca-lesson-title-side" id="player-title-<?php echo esc_attr($mod['id']); ?>">
                                                    <?php echo esc_html($first_lesson['title']); ?>
                                                </h5>
                                                
                                                <div class="aca-lesson-meta-blocks">
                                                    <div>
                                                        <span class="aca-block-lbl">Qué aprenderás:</span>
                                                        <p class="aca-block-txt" id="player-learn-<?php echo esc_attr($mod['id']); ?>">
                                                            <?php echo esc_html($first_lesson['what_you_will_learn']); ?>
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <span class="aca-block-lbl-amber">Para qué te sirve en tu negocio:</span>
                                                        <p class="aca-block-txt" id="player-utility-<?php echo esc_attr($mod['id']); ?>">
                                                            <?php echo esc_html($first_lesson['business_utility']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="aca-player-actions">
                                                <?php 
                                                    $is_watched = !empty($progress['watched'][$first_lesson['id']]) || !empty($progress['watched'][$first_lesson['lesson_code']]);
                                                ?>
                                                <button type="button" class="aca-btn-watched <?php echo $is_watched ? 'watched' : ''; ?>" 
                                                        id="btn-watched-<?php echo esc_attr($mod['id']); ?>"
                                                        data-lesson-id="<?php echo esc_attr($first_lesson['id']); ?>"
                                                        data-mod-id="<?php echo esc_attr($mod['id']); ?>">
                                                    <span><?php echo $is_watched ? '✓ Lección Vista' : '▶️ Reproducir Vídeo'; ?></span>
                                                </button>
                                                <button type="button" class="aca-btn-to-studio" data-tab-target="studio">
                                                    <span>⚡ Ir a Actividad en Studio</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Texto Complementario y Lectura Profunda -->
                                <div class="aca-reading-quote-box" id="reading-box-<?php echo esc_attr($mod['id']); ?>">
                                    <div class="aca-reading-header">
                                        <span class="aca-reading-title">
                                            <span>📖</span> TEXTO COMPLEMENTARIO Y LECTURA PROFUNDA (<span id="reading-code-<?php echo esc_attr($mod['id']); ?>"><?php echo esc_html($first_lesson['lesson_code']); ?></span>)
                                        </span>
                                        <?php 
                                            $is_read = !empty($progress['read'][$first_lesson['id']]) || !empty($progress['read'][$first_lesson['lesson_code']]);
                                        ?>
                                        <button type="button" class="aca-btn-read-toggle <?php echo $is_read ? 'completed' : ''; ?>"
                                                id="btn-read-<?php echo esc_attr($mod['id']); ?>"
                                                data-lesson-id="<?php echo esc_attr($first_lesson['id']); ?>"
                                                data-mod-id="<?php echo esc_attr($mod['id']); ?>">
                                            <?php echo $is_read ? '✓ Lección Leída y Completada' : 'Marcar como Leído'; ?>
                                        </button>
                                    </div>
                                    <p class="aca-reading-paragraph" id="reading-text-<?php echo esc_attr($mod['id']); ?>">
                                        "<?php echo esc_html($first_lesson['reading_text']); ?>"
                                    </p>
                                </div>

                                <!-- Selector de Lecciones en Grid -->
                                <div class="aca-lesson-picker-wrap">
                                    <span class="aca-picker-label">Selecciona una Lección en Vídeo para reproducir:</span>
                                    <div class="aca-lesson-picker-grid">
                                        <?php foreach ($mod['lessonsList'] as $l_idx => $les) : 
                                            $is_selected = ($l_idx === 0);
                                            $les_watched = !empty($progress['watched'][$les['id']]) || !empty($progress['watched'][$les['lesson_code']]);
                                        ?>
                                            <div class="aca-lesson-card-item <?php echo $is_selected ? 'selected' : ''; ?>"
                                                 data-lesson-raw='<?php echo esc_attr(wp_json_encode($les)); ?>'
                                                 data-mod-id="<?php echo esc_attr($mod['id']); ?>"
                                                 data-lesson-id="<?php echo esc_attr($les['id']); ?>">
                                                <span class="aca-les-order-num <?php echo $is_selected ? 'active' : ''; ?>">
                                                    0<?php echo ($l_idx + 1); ?>
                                                </span>
                                                <div class="aca-les-details">
                                                    <div class="aca-les-details-top">
                                                        <h6 class="aca-les-title-text"><?php echo esc_html($les['title']); ?></h6>
                                                        <span class="aca-les-dur"><?php echo esc_html($les['duration']); ?></span>
                                                    </div>
                                                    <div class="aca-les-details-bottom">
                                                        <span class="aca-les-type-tag"><?php echo esc_html($les['type']); ?></span>
                                                        <button type="button" class="aca-quick-watch-btn <?php echo $les_watched ? 'is-watched' : ''; ?>"
                                                                data-lesson-id="<?php echo esc_attr($les['id']); ?>"
                                                                data-mod-id="<?php echo esc_attr($mod['id']); ?>">
                                                            <?php echo $les_watched ? '✓ Visto' : '○ Marcar visto'; ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                            </div>
                        <?php endif; ?>

                        <!-- BLOQUE 3: Herramientas Disponibles en este Módulo -->
                        <div class="aca-module-tools-box">
                            <h4 class="aca-tools-section-title">
                                <span>🛠️</span> HERRAMIENTAS DISPONIBLES EN ESTE MÓDULO
                            </h4>
                            <div class="aca-tools-chips-grid">
                                <?php foreach ($mod['tools'] as $tool_name) : ?>
                                    <div class="aca-tool-chip">
                                        <span class="aca-tool-bullet">❖</span>
                                        <span><?php echo esc_html($tool_name); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- BLOQUE 4: Actividades Prácticas y Materiales Nuevos -->
                        <div class="aca-acts-mats-grid">
                            <div class="aca-subpanel-card">
                                <h4 class="aca-subpanel-title">
                                    <span>✅</span> ACTIVIDADES PRÁCTICAS
                                </h4>
                                <ul class="aca-subpanel-list">
                                    <?php foreach ($mod['activities'] as $act_i => $act_name) : ?>
                                        <li>
                                            <span class="aca-badge-act">A<?php echo ($act_i + 1); ?></span>
                                            <span><?php echo esc_html($act_name); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="aca-subpanel-card">
                                <h4 class="aca-subpanel-title">
                                    <span>🆕</span> MATERIAL NUEVO A CREAR
                                </h4>
                                <ul class="aca-subpanel-list">
                                    <?php foreach ($mod['newMaterials'] as $mat_name) : ?>
                                        <li>
                                            <span class="aca-star-bullet">★</span>
                                            <span><?php echo esc_html($mat_name); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- BLOQUE 5: Entregable de Graduación -->
                        <div class="aca-graduation-alert-card">
                            <div class="aca-grad-alert-left">
                                <span class="aca-grad-icon">🎓</span>
                                <div>
                                    <span class="aca-grad-title">Entregable Requerido en Graduación</span>
                                    <span class="aca-grad-desc">
                                        Debes generar y validar: <strong><?php echo esc_html($mod['targetDeliverable']); ?></strong> (Construido con <?php echo esc_html($mod['targetTool']); ?>).
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="aca-btn-goto-grad" data-tab-target="graduation">
                                Verificar en Graduación →
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
