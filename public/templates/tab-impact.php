<?php
/**
 * Tab 5: Impacto y Casos de Éxito de la Comunidad ALMA (Matching React Mockup)
 */

if (!defined('ABSPATH')) {
    exit;
}

$initial_impact_entries = array(
    array(
        'id' => 1,
        'author' => 'Kike Castillo',
        'role' => 'Director de Exhala Consultores y Autoescuela Sebastía (España)',
        'avatarInitials' => 'KC',
        'avatarBg' => '#4f46e5',
        'before' => 'Falta de claridad en la agenda diaria y dispersión táctica; regalaba su tiempo en la operativa rutinaria sin poder concentrarse en decisiones de alto valor.',
        'action' => 'Implementación del Protocolo 3-2-1, delegación estructurada bajo Proceso Justo y mapa de decisiones.',
        'result' => 'Estructura mental clara, salida definitiva de la operación rutinaria y enfoque exclusivo en tareas estratégicas de alto impacto.',
        'formatType' => 'Texto + Captura de Dashboard',
        'date' => 'Hace 2 semanas',
        'timeline' => 'A las 4 semanas post-graduación',
        'moderationStatus' => 'Verificado y Aprobado por Dra. Erika Tatiana Parra',
    ),
    array(
        'id' => 2,
        'author' => 'Alexander Parra',
        'role' => 'CEO de DEnova Pharmaceutical y Amyet Laboratorio (España)',
        'avatarInitials' => 'AP',
        'avatarBg' => '#0d9488',
        'before' => 'Tenía las ideas, la base técnica y ventas activas, pero le faltaban conexiones estructurales claras; al no tener este sistema, su negocio exigía excesivo tiempo personal.',
        'action' => 'Diagramación del Mapa de Sistemas de 7 Nodos y Matriz de Decisiones 2x2 para liberar cuellos de botella.',
        'result' => 'Arquitectura técnica integrada con procesos claros que redujeron la dependencia operativa del CEO.',
        'formatType' => 'Vídeo corto (2 min)',
        'date' => 'Hace 1 mes',
        'timeline' => 'A las 8 semanas post-graduación',
        'moderationStatus' => 'Verificado y Aprobado por Dra. Erika Tatiana Parra',
    ),
    array(
        'id' => 3,
        'author' => 'Alicia de la Puerta',
        'role' => 'Directora de Water Memory Academy',
        'avatarInitials' => 'AP',
        'avatarBg' => '#d97706',
        'before' => 'Dificultad para rentabilizar su conocimiento, infravaloración de sus servicios y ausencia de un sistema digital y de procesos en el trabajo del día a día.',
        'action' => 'Orquestación Estratégica de Academia Digital y automatización de entrega de procesos.',
        'result' => 'Construcción de una academia digital rentable que logró independizarla por completo de la atención física en cabina.',
        'formatType' => 'Enlace Externo + Caso Documentado',
        'date' => 'Hace 1 mes y medio',
        'timeline' => 'A las 8 semanas post-graduación',
        'moderationStatus' => 'Verificado y Aprobado por Dra. Erika Tatiana Parra',
    ),
);
?>

<div class="aca-impact-view-wrapper" style="display:grid; gap:24px;">
    <!-- Encabezado Banner de Impacto -->
    <div style="background:linear-gradient(135deg, #064e3b 0%, #115e59 40%, #1e1b4b 100%); color:#ffffff; padding:28px 32px; border-radius:18px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.15);">
        <h2 style="font-size:24px; font-weight:900; margin:0 0 6px 0; letter-spacing:-0.02em;">
            💎 Evidencias de Impacto & Transformación Post-Certificación
        </h2>
        <p style="color:#ccfbf1; font-size:13px; margin:0; max-width:640px; line-height:1.6; font-weight:300;">
            Espacio reservado para registrar y compartir los resultados reales obtenidos en la vida y el negocio tras haber aplicado las herramientas de la metodología ALMA.
        </p>
    </div>

    <!-- Advertencia Post-Graduación -->
    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:16px; padding:16px 20px; display:flex; align-items:center; gap:14px; color:#78350f;">
        <span style="font-size:26px; line-height:1;">🔒</span>
        <div style="font-size:12px; line-height:1.5;">
            <strong style="font-size:13px; color:#92400e; display:block; margin-bottom:2px;">Advertencia Post-Graduación:</strong>
            Puedes redactar tu caso de impacto preliminar hoy. Tras la validación de tus entregables en la pestaña de <strong>Graduación</strong>, tu caso será verificado oficialmente por la <strong><?php echo esc_html($mentor_info['name']); ?></strong> para publicarse en la comunidad.
        </div>
    </div>

    <!-- Formulario para Registrar Nuevo Caso de Impacto -->
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <h3 style="font-size:16px; font-weight:800; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:12px; margin:0 0 18px 0;">
            📝 Publicar un nuevo Caso de Impacto Real
        </h3>

        <form id="form-publish-impact" style="display:grid; gap:16px;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
                <div>
                    <label style="display:block; font-size:11px; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:6px;">
                        1. Estado Inicial (Antes)
                    </label>
                    <textarea id="impact-before" rows="2" style="width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; background:#f8fafc;" placeholder="Ej. Caos en la agenda, trabajando 60 horas a la semana y con parálisis por análisis..." required></textarea>
                </div>
                <div>
                    <label style="display:block; font-size:11px; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:6px;">
                        2. Herramienta ALMA Implementada
                    </label>
                    <textarea id="impact-action" rows="2" style="width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; background:#f8fafc;" placeholder="Ej. Rediseño de procesos justos + Mapa de Sistemas de 7 Nodos del Módulo 4..." required></textarea>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                <div>
                    <label style="display:block; font-size:11px; font-weight:800; color:#065f46; text-transform:uppercase; margin-bottom:6px;">
                        3. Resultado Medible (Después)
                    </label>
                    <input type="text" id="impact-result" style="width:100%; padding:10px 12px; border:1px solid #a7f3d0; border-radius:10px; font-size:13px; background:#f0fdf4; font-weight:700; color:#065f46;" placeholder="Ej. +35% de margen libre y 12h delegadas..." required />
                </div>

                <div>
                    <label style="display:block; font-size:11px; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:6px;">
                        4. Seguimiento Temporal
                    </label>
                    <select id="impact-timeline" style="width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; background:#f8fafc; font-weight:600;">
                        <option value="A las 2 semanas post-graduación">A las 2 semanas post-graduación</option>
                        <option value="A las 4 semanas post-graduación" selected>A las 4 semanas post-graduación</option>
                        <option value="A las 8 semanas post-graduación">A las 8 semanas post-graduación</option>
                        <option value="A los 6 meses post-graduación">A los 6 meses post-graduación</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:11px; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:6px;">
                        5. Formato de Evidencia
                    </label>
                    <select id="impact-format" style="width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; background:#f8fafc; font-weight:600;">
                        <option value="Texto / Documento">Texto / Documento</option>
                        <option value="Vídeo Corto (Demostración)">Vídeo Corto (Demostración)</option>
                        <option value="Captura de Pantalla / Métricas">Captura de Pantalla / Métricas</option>
                        <option value="Enlace Externo (Notion/Miro)">Enlace Externo (Notion/Miro)</option>
                        <option value="Audio / Testimonio de Voz">Audio / Testimonio de Voz</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:6px;">
                <button type="submit" id="btn-submit-impact-case" style="background:#059669; color:#ffffff; font-weight:800; font-size:13px; padding:12px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 2px 6px rgba(5,150,105,0.3); transition:background 0.15s ease;">
                    ✨ Registrar Caso de Impacto
                </button>
            </div>
        </form>
    </div>

    <!-- Historias de Impacto Registradas en la Comunidad -->
    <div style="display:grid; gap:16px;">
        <h3 style="font-size:18px; font-weight:800; color:#0f172a; margin:0;">
            🏆 Historias de Impacto Registradas en la Comunidad ALMA
        </h3>

        <div id="impact-stories-container" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
            <?php foreach ($initial_impact_entries as $item) : ?>
                <div class="aca-impact-story-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; padding:22px; box-shadow:0 1px 3px rgba(0,0,0,0.03); display:flex; flex-direction:column; justify-content:space-between; gap:16px;">
                    <div style="display:grid; gap:14px;">
                        <!-- Autor y Rol -->
                        <div style="display:flex; align-items:center; gap:12px; border-bottom:1px solid #f1f5f9; padding-bottom:14px;">
                            <div style="width:46px; height:46px; border-radius:50%; background:<?php echo esc_attr($item['avatarBg']); ?>; color:#ffffff; font-weight:900; font-size:15px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                                <?php echo esc_html($item['avatarInitials']); ?>
                            </div>
                            <div style="min-width:0; flex:1;">
                                <h4 style="font-size:15px; font-weight:800; color:#0f172a; margin:0; line-height:1.2;">
                                    <?php echo esc_html($item['author']); ?>
                                </h4>
                                <span style="font-size:11px; color:#64748b; display:block; margin-top:2px; line-height:1.3;">
                                    <?php echo esc_html($item['role']); ?>
                                </span>
                                <span style="display:inline-block; margin-top:6px; font-size:10px; font-weight:800; background:#eef2ff; color:#4338ca; padding:2px 8px; border-radius:9999px;">
                                    ⏱️ <?php echo esc_html($item['timeline']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Antes, Acción y Resultado -->
                        <div style="display:grid; gap:10px; font-size:12px;">
                            <div>
                                <span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; display:block;">
                                    Antes (El Problema):
                                </span>
                                <p style="margin:2px 0 0 0; color:#475569; font-style:italic; line-height:1.5;">
                                    "<?php echo esc_html($item['before']); ?>"
                                </p>
                            </div>

                            <div>
                                <span style="font-size:10px; font-weight:800; color:#4f46e5; text-transform:uppercase; letter-spacing:0.05em; display:block;">
                                    Acción y Sistema Aplicado:
                                </span>
                                <p style="margin:2px 0 0 0; color:#1e293b; font-weight:600; line-height:1.4;">
                                    <?php echo esc_html($item['action']); ?>
                                </p>
                            </div>

                            <div>
                                <span style="font-size:10px; font-weight:800; color:#059669; text-transform:uppercase; letter-spacing:0.05em; display:block;">
                                    Resultado e Impacto Medible:
                                </span>
                                <p style="margin:4px 0 0 0; color:#065f46; font-weight:800; background:#f0fdf4; border:1px solid #bbf7d0; padding:10px 12px; border-radius:10px; line-height:1.4;">
                                    <?php echo esc_html($item['result']); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer de Verificación -->
                    <div style="border-top:1px solid #f1f5f9; padding-top:12px; display:flex; justify-content:space-between; align-items:center; font-size:10px; color:#94a3b8;">
                        <span style="color:#059669; font-weight:700; display:flex; align-items:center; gap:4px;">
                            <span>✓</span> <?php echo esc_html($item['moderationStatus']); ?>
                        </span>
                        <span><?php echo esc_html($item['date']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
