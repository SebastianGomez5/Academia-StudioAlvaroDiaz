<?php
/**
 * Tab 3: Studio & Interactive Laboratory Template (Matching React Mockup)
 */

if (!defined('ABSPATH')) {
    exit;
}

$caos_saved = !empty($caos_data) ? $caos_data : array(
    'caos1' => '',
    'caos2' => '',
    'caos3' => '',
    'control1' => '',
    'control2' => '',
    'nextAction' => ''
);
?>

<div class="aca-studio-view-wrapper" style="display:grid; gap:24px;">
    <!-- Intro Card -->
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <span style="font-size:11px; font-weight:800; background:#ede9fe; color:#6d28d9; padding:3px 8px; border-radius:6px; text-transform:uppercase;">
                    LABORATORIO EN VIVO
                </span>
                <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin:6px 0 4px 0;">🧪 Studio de Pensamiento & Toma de Decisiones</h2>
                <p style="color:#64748b; font-size:13px; margin:0;">
                    Espacio interactivo para modelar tu matriz de impacto vs. esfuerzo y ejecutar el protocolo de triaje mental matutino con autoguardado en tu cuenta.
                </p>
            </div>
            <div>
                <span id="matrix-save-status" style="font-size:12px; font-weight:800; color:#059669;"></span>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1: MATRIZ 2X2 DE IMPACTO VS ESFUERZO -->
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <div>
                <h3 style="font-size:18px; font-weight:800; color:#0f172a; margin:0;">📐 Matriz de Decisiones 2x2</h3>
                <p style="font-size:13px; color:#64748b; margin:2px 0 0 0;">
                    Clasifica tus proyectos para detectar victorias rápidas (Quick Wins) y eliminar sumideros de recursos.
                </p>
            </div>
        </div>

        <!-- Add form -->
        <form id="form-add-matrix-decision" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
            <input type="text" id="matrix-input-text" style="flex:2; min-width:240px; padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; outline:none;" placeholder="Escribe el nombre de un proyecto o iniciativa empresarial..." required />
            <select id="matrix-select-impact" style="flex:1; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; background:#f8fafc;">
                <option value="Alto">🎯 Impacto: Alto</option>
                <option value="Bajo">🎯 Impacto: Bajo</option>
            </select>
            <select id="matrix-select-effort" style="flex:1; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; font-size:13px; background:#f8fafc;">
                <option value="Bajo">⚡ Esfuerzo: Bajo</option>
                <option value="Alto">⚡ Esfuerzo: Alto</option>
            </select>
            <button type="submit" style="background:#4f46e5; color:#ffffff; font-weight:800; font-size:13px; padding:10px 18px; border-radius:10px; border:none; cursor:pointer;">
                ➕ Añadir a la Matriz
            </button>
        </form>

        <!-- Cuadrantes -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
            <!-- Cuadrante 1: Quick Wins -->
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:16px; min-height:190px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h4 style="margin:0; font-size:14px; font-weight:800; color:#166534;">🚀 Victorias Rápidas (Quick Wins)</h4>
                    <span style="font-size:10px; font-weight:800; background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px;">ALTO IMPACTO · BAJO ESFUERZO</span>
                </div>
                <div id="quadrant-list-quick-wins" class="quadrant-items-list"></div>
            </div>

            <!-- Cuadrante 2: Proyectos Mayores -->
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:14px; padding:16px; min-height:190px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h4 style="margin:0; font-size:14px; font-weight:800; color:#1e40af;">🏗️ Proyectos Mayores</h4>
                    <span style="font-size:10px; font-weight:800; background:#dbeafe; color:#1e40af; padding:2px 6px; border-radius:4px;">ALTO IMPACTO · ALTO ESFUERZO</span>
                </div>
                <div id="quadrant-list-major-projects" class="quadrant-items-list"></div>
            </div>

            <!-- Cuadrante 3: Tareas de Relleno -->
            <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:14px; padding:16px; min-height:190px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h4 style="margin:0; font-size:14px; font-weight:800; color:#92400e;">⏳ Tareas de Relleno</h4>
                    <span style="font-size:10px; font-weight:800; background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px;">BAJO IMPACTO · BAJO ESFUERZO</span>
                </div>
                <div id="quadrant-list-fill-tasks" class="quadrant-items-list"></div>
            </div>

            <!-- Cuadrante 4: Sumideros de Tiempo -->
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:16px; min-height:190px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h4 style="margin:0; font-size:14px; font-weight:800; color:#991b1b;">⚠️ Sumideros de Tiempo</h4>
                    <span style="font-size:10px; font-weight:800; background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px;">BAJO IMPACTO · ALTO ESFUERZO</span>
                </div>
                <div id="quadrant-list-time-sinks" class="quadrant-items-list"></div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: PROTOCOLO 3-2-1 FILTRO DEL CAOS -->
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <div>
                <h3 style="font-size:18px; font-weight:800; color:#0f172a; margin:0;">🧘 Filtro del Caos: Protocolo 3-2-1</h3>
                <p style="font-size:13px; color:#64748b; margin:2px 0 0 0;">
                    Rutina de triaje matutino: neutraliza la amígdala cerebral y define tu micro-acción estratégica de 20 minutos.
                </p>
            </div>
            <div>
                <span id="caos-save-status" style="font-size:12px; font-weight:800; color:#059669;"></span>
            </div>
        </div>

        <form id="form-save-caos">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
                <!-- 3 Focos de Caos -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
                    <h4 style="font-size:14px; font-weight:800; color:#dc2626; margin:0 0 4px 0;">🌪️ 3 Focos de Caos Externo</h4>
                    <p style="font-size:11px; color:#64748b; margin:0 0 10px 0;">Variables que generan ruido fuera de tu control directo.</p>
                    <textarea id="caos-1" class="caos-input" rows="2" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px; font-size:13px; margin-bottom:8px;" placeholder="Foco 1 (ej. retraso de entrega de un proveedor)"><?php echo esc_textarea($caos_saved['caos1']); ?></textarea>
                    <textarea id="caos-2" class="caos-input" rows="2" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px; font-size:13px; margin-bottom:8px;" placeholder="Foco 2 (ej. cliente indeciso sobre la propuesta)"><?php echo esc_textarea($caos_saved['caos2']); ?></textarea>
                    <textarea id="caos-3" class="caos-input" rows="2" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px; font-size:13px;" placeholder="Foco 3 (ej. fluctuación en anuncios publicitarios)"><?php echo esc_textarea($caos_saved['caos3']); ?></textarea>
                </div>

                <!-- 2 Variables de Control -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
                    <h4 style="font-size:14px; font-weight:800; color:#2563eb; margin:0 0 4px 0;">🎯 2 Variables Bajo Control Directo</h4>
                    <p style="font-size:11px; color:#64748b; margin:0 0 10px 0;">Decisiones y acciones concretas que dependen 100% de ti hoy.</p>
                    <textarea id="control-1" class="caos-input" rows="3" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px; font-size:13px; margin-bottom:8px;" placeholder="Variable 1 (ej. enviar el contrato reestructurado antes de las 11:00)"><?php echo esc_textarea($caos_saved['control1']); ?></textarea>
                    <textarea id="control-2" class="caos-input" rows="3" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px; font-size:13px;" placeholder="Variable 2 (ej. auditar costos fijos del trimestre)"><?php echo esc_textarea($caos_saved['control2']); ?></textarea>
                </div>

                <!-- 1 Micro-Acción Inmediata -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
                    <h4 style="font-size:14px; font-weight:800; color:#16a34a; margin:0 0 4px 0;">⚡ 1 Micro-Acción Inmediata (<20 min)</h4>
                    <p style="font-size:11px; color:#64748b; margin:0 0 10px 0;">La única acción que ejecutarás sin distracciones en los próximos 20 minutos.</p>
                    <textarea id="next-action" class="caos-input" rows="7" style="width:100%; border:1px solid #e2e8f0; border-radius:8px; padding:8px; font-size:13px;" placeholder="Escribe tu micro-acción inmediata y pon tu cronómetro en marcha..."><?php echo esc_textarea($caos_saved['nextAction']); ?></textarea>
                </div>
            </div>

            <div style="margin-top:16px; text-align:right;">
                <button type="submit" style="background:#059669; color:#ffffff; font-weight:800; font-size:13px; padding:10px 20px; border-radius:10px; border:none; cursor:pointer;">
                    💾 Guardar Protocolo en Mi Perfil
                </button>
            </div>
        </form>
    </div>
</div>
