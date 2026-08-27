<?php
/**
 * Tab 4: Graduación y Portafolio de Entregables (Matching React Mockup)
 */

if (!defined('ABSPATH')) {
    exit;
}

$all_approved = true;
$approved_count = 0;
$total_modules = max(count($modules_data), 1);

foreach ($modules_data as $m) {
    $sub = isset($deliverables[$m['id']]) ? $deliverables[$m['id']] : null;
    if ($sub && $sub['status'] === 'approved') {
        $approved_count++;
    } else {
        $all_approved = false;
    }
}
$grad_percentage = round(($approved_count / $total_modules) * 100);
?>

<div class="aca-graduation-view-wrapper" style="display:grid; grid-template-columns:1fr; gap:24px;">
    <!-- Intro Card -->
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <span style="font-size:11px; font-weight:800; background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:6px; text-transform:uppercase;">
                    CERTIFICACIÓN OFICIAL
                </span>
                <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin:6px 0 4px 0;">🎓 Portafolio de Entregables & Acompañamiento Directo</h2>
                <p style="color:#64748b; font-size:13px; margin:0;">
                    Para graduarte en la metodología ALMA de Álvaro Díaz, debes cargar los entregables correspondientes a cada módulo para la revisión de tu mentora asignada.
                </p>
            </div>
            <div>
                <span style="font-size:13px; font-weight:800; background:#dcfce7; color:#166534; padding:6px 14px; border-radius:10px;">
                    Avance: <?php echo intval($approved_count); ?>/<?php echo intval($total_modules); ?> Aprobados (<?php echo intval($grad_percentage); ?>%)
                </span>
            </div>
        </div>
    </div>

    <!-- Layout Grid: Deliverables on Left, Mentor on Right -->
    <div style="display:grid; grid-template-columns:1fr; gap:24px;">
        <style>
            @media (min-width: 900px) {
                .aca-grad-two-col { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start; }
            }
        </style>
        <div class="aca-grad-two-col">
            <!-- Columna Izquierda: Entregables por Módulo -->
            <div style="display:grid; gap:16px;">
                <?php if (empty($modules_data)) : ?>
                    <p style="color:#64748b;">No hay entregables requeridos para este curso.</p>
                <?php else : ?>
                    <?php foreach ($modules_data as $mod) : 
                        $sub = isset($deliverables[$mod['id']]) ? $deliverables[$mod['id']] : null;
                        $deliverable_name = !empty($mod['targetDeliverable']) ? $mod['targetDeliverable'] : ('Entregable: ' . $mod['title']);
                        $status = $sub ? $sub['status'] : 'empty';
                    ?>
                        <div class="aca-deliverable-card-item" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                <div>
                                    <span style="font-size:11px; font-weight:800; color:<?php echo esc_attr($mod['color']); ?>; background:<?php echo esc_attr($mod['bgLight']); ?>; padding:2px 8px; border-radius:6px;">
                                        <?php echo esc_html($mod['tag'] ? $mod['tag'] : 'Módulo'); ?>
                                    </span>
                                    <h4 style="font-size:16px; font-weight:800; color:#0f172a; margin:6px 0 0 0;">
                                        <?php echo esc_html($deliverable_name); ?>
                                    </h4>
                                </div>
                                <div>
                                    <?php if ($status === 'approved') : ?>
                                        <span style="background:#d1fae5; color:#065f46; font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px;">🟢 Aprobado ✓</span>
                                    <?php elseif ($status === 'needs_changes') : ?>
                                        <span style="background:#fee2e2; color:#991b1b; font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px;">🔴 Requiere Cambios</span>
                                    <?php elseif ($status === 'pending') : ?>
                                        <span style="background:#fef3c7; color:#92400e; font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px;">🟡 En Revisión</span>
                                    <?php else : ?>
                                        <span style="background:#f1f5f9; color:#64748b; font-size:12px; font-weight:800; padding:4px 10px; border-radius:20px;">⚪ Sin Enviar</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Mentor Feedback box if available -->
                            <?php if ($sub && !empty($sub['mentor_feedback'])) : ?>
                                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:12px 16px; margin:12px 0; font-size:13px; color:#78350f;">
                                    <strong>💬 Retroalimentación de tu Mentora:</strong>
                                    <p style="margin:4px 0 0 0; line-height:1.5;"><?php echo nl2br(esc_html($sub['mentor_feedback'])); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if ($sub && !empty($sub['submission_url'])) : ?>
                                <div style="font-size:12px; margin-bottom:12px; color:#475569;">
                                    📁 <strong>Última evidencia enviada:</strong> 
                                    <a href="<?php echo esc_url($sub['submission_url']); ?>" target="_blank" style="color:#4f46e5; font-weight:700; text-decoration:underline;">
                                        Abrir trabajo entregado ↗
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Deliverable Submission Form -->
                            <form class="form-submit-deliverable" data-module-id="<?php echo esc_attr($mod['id']); ?>" data-deliverable-name="<?php echo esc_attr($deliverable_name); ?>" style="border-top:1px solid #f1f5f9; padding-top:14px; margin-top:10px;">
                                <div style="display:flex; gap:16px; margin-bottom:10px; font-size:12px; font-weight:700; color:#475569;">
                                    <label style="cursor:pointer;">
                                        <input type="radio" name="submission_type" value="link" class="radio-submission-type" checked /> Enlace (Canva, Miro, Drive, Notion)
                                    </label>
                                    <label style="cursor:pointer;">
                                        <input type="radio" name="submission_type" value="file" class="radio-submission-type" /> Subir Archivo (PDF, ZIP, DOCX)
                                    </label>
                                </div>

                                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                    <div class="deliverable-input-url-wrap" style="flex:1; min-width:200px;">
                                        <input type="url" class="deliverable-input-url" style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px;" placeholder="https://canva.com/design/..." value="<?php echo ($sub && $sub['submission_type'] === 'link') ? esc_attr($sub['submission_url']) : ''; ?>" />
                                    </div>
                                    <div class="deliverable-input-file-wrap" style="flex:1; min-width:200px; display:none;">
                                        <input type="file" class="deliverable-input-file" style="font-size:12px;" accept=".pdf,.zip,.docx,.png,.jpg" />
                                    </div>
                                    <button type="submit" class="btn-submit-deliverable" style="background:#4f46e5; color:#ffffff; font-weight:800; font-size:12px; padding:8px 16px; border-radius:8px; border:none; cursor:pointer;">
                                        <?php echo $sub ? 'Reenviar / Actualizar' : '📤 Enviar a Revisión'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Columna Derecha: Tarjeta de la Mentora & Agendamiento 1 a 1 -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03); text-align:center;">
                <div style="width:90px; height:90px; border-radius:26px; overflow:hidden; margin:0 auto 14px auto; box-shadow:0 10px 25px -5px rgba(0,0,0,0.15); border:3px solid #ffffff; background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                    <img src="<?php echo esc_url($mentor_info['avatarUrl']); ?>" alt="<?php echo esc_attr($mentor_info['name']); ?>" style="width:100%; height:100%; object-fit:cover; display:block;" onerror="this.style.display='none'; this.parentElement.innerText='👩‍🏫';" />
                </div>
                <h3 style="font-size:17px; font-weight:800; color:#0f172a; margin:0 0 4px 0;"><?php echo esc_html($mentor_info['name']); ?></h3>
                <p style="font-size:12px; color:#64748b; margin:0 0 18px 0; line-height:1.4;"><?php echo esc_html($mentor_info['role']); ?></p>

                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:18px; font-size:12px; text-align:left;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:#64748b; font-weight:600;">Entregables aprobados:</span>
                        <strong><?php echo intval($approved_count); ?> / <?php echo intval($total_modules); ?></strong>
                    </div>
                    <div style="background:#e2e8f0; border-radius:10px; height:8px; overflow:hidden;">
                        <div style="background:#10b981; height:100%; width:<?php echo intval($grad_percentage); ?>%;"></div>
                    </div>
                </div>

                <button type="button" id="btn-open-mentor-modal" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:#ffffff; font-weight:800; font-size:13px; padding:12px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.3); margin-bottom:12px;">
                    <span>📅</span> Solicitar Sesión 1 a 1 (<?php echo esc_html($mentor_info['price']); ?>)
                </button>

                <button type="button" id="btn-request-certification" style="width:100%; background:#10b981; color:#ffffff; font-weight:800; font-size:14px; padding:12px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(16,185,129,0.3); <?php echo !$all_approved ? 'opacity:0.6;' : ''; ?>">
                    🏆 Solicitar Titulación Oficial
                </button>
                <?php if (!$all_approved) : ?>
                    <small style="display:block; color:#94a3b8; font-size:11px; margin-top:8px;">
                        * Requiere que tu mentora apruebe todos los entregables.
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
