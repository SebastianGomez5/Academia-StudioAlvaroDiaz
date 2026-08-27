/**
 * Academia Tectonica - Graduation & Deliverables JS
 */
(function($) {
  'use strict';

  window.AcademiaGraduation = {
    init: function() {
      if (typeof AcademiaData === 'undefined') return;

      this.bindDeliverableSubmissions();
      this.bindMentorModal();
      this.bindCertificationRequest();
      this.bindImpactSurvey();
    },

    bindDeliverableSubmissions: function() {
      // Handle deliverable form submission
      $('.form-submit-deliverable').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('.btn-submit-deliverable');
        var moduleId = $form.data('module-id');
        var deliverableName = $form.data('deliverable-name');
        var submissionType = $form.find('input[name="submission_type"]:checked').val() || 'link';
        var submissionUrl = $form.find('.deliverable-input-url').val();

        var formData = new FormData();
        formData.append('action', 'academia_submit_deliverable');
        formData.append('nonce', AcademiaData.nonce);
        formData.append('course_id', AcademiaData.currentCourse.id);
        formData.append('module_id', moduleId);
        formData.append('deliverable_name', deliverableName);
        formData.append('submission_type', submissionType);
        formData.append('submission_url', submissionUrl);

        var fileInput = $form.find('.deliverable-input-file')[0];
        if (submissionType === 'file' && fileInput && fileInput.files.length > 0) {
          formData.append('deliverable_file', fileInput.files[0]);
        }

        $btn.prop('disabled', true).text('Enviando...');

        $.ajax({
          url: AcademiaData.ajaxUrl,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(res) {
            $btn.prop('disabled', false).text('Reenviar / Actualizar');
            if (res.success) {
              var $card = $form.closest('.deliverable-card');
              $card.find('.deliverable-status-badge')
                .removeClass('badge-empty badge-approved badge-changes')
                .addClass('badge-pending')
                .text('🟡 En Revisión');
              
              alert('¡Entregable enviado con éxito al equipo evaluador de la Academia Tectónica!');
            } else {
              alert(res.data.message || 'Error al enviar el entregable.');
            }
          },
          error: function() {
            $btn.prop('disabled', false).text('Enviar a Revisión');
            alert('Error en la conexión al enviar el archivo.');
          }
        });
      });

      // Toggle link vs file inputs
      $('.radio-submission-type').on('change', function() {
        var $form = $(this).closest('.form-submit-deliverable');
        var type = $(this).val();
        if (type === 'file') {
          $form.find('.deliverable-input-url-wrap').hide();
          $form.find('.deliverable-input-file-wrap').show();
        } else {
          $form.find('.deliverable-input-file-wrap').hide();
          $form.find('.deliverable-input-url-wrap').show();
        }
      });
    },

    bindMentorModal: function() {
      $('#btn-open-mentor-modal').on('click', function(e) {
        e.preventDefault();
        $('#academia-mentor-modal').fadeIn(200);
      });

      $('.aca-modal-close-btn, .academia-modal-close').on('click', function() {
        $('.aca-modal-overlay, .academia-modal-backdrop').fadeOut(200);
      });

      $('.aca-modal-overlay').on('click', function(e) {
        if ($(e.target).hasClass('aca-modal-overlay')) {
          $(this).fadeOut(200);
        }
      });
    },

    bindCertificationRequest: function() {
      $('#btn-request-certification').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true).text('Verificando entregables...');

        $.ajax({
          url: AcademiaData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_request_certification',
            nonce: AcademiaData.nonce,
            course_id: AcademiaData.currentCourse.id
          },
          success: function(res) {
            $btn.prop('disabled', false).text('Solicitar Titulación Oficial');
            if (res.success) {
              alert(res.data.message);
              location.reload();
            } else {
              alert(res.data.message);
            }
          },
          error: function() {
            $btn.prop('disabled', false).text('Solicitar Titulación Oficial');
            alert('Error al procesar la solicitud.');
          }
        });
      });
    },

    bindImpactSurvey: function() {
      $('#form-publish-impact').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-submit-impact-case');
        $btn.prop('disabled', true).text('Registrando...');

        var beforeVal = $('#impact-before').val().trim();
        var actionVal = $('#impact-action').val().trim();
        var resultVal = $('#impact-result').val().trim();
        var timelineVal = $('#impact-timeline').val();
        var formatVal = $('#impact-format').val();

        var payload = {
          before: beforeVal,
          action: actionVal,
          result: resultVal,
          timeline: timelineVal,
          formatType: formatVal,
          author: AcademiaData.userName || 'Alumno Tectónico'
        };

        $.ajax({
          url: AcademiaData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_save_impact_survey',
            nonce: AcademiaData.nonce,
            course_id: AcademiaData.currentCourse.id,
            responses: JSON.stringify(payload)
          },
          success: function(res) {
            $btn.prop('disabled', false).text('✨ Registrar Caso de Impacto');
            
            // Dynamic card prepend
            var initials = (AcademiaData.userName ? AcademiaData.userName.substring(0, 2).toUpperCase() : 'AL');
            var newCardHtml = 
              '<div class="aca-impact-story-card" style="background:#ffffff; border:2px solid #a7f3d0; border-radius:18px; padding:22px; box-shadow:0 4px 12px rgba(16,185,129,0.1); display:flex; flex-direction:column; justify-content:space-between; gap:16px; animation:fadeIn 0.3s ease;">' +
                '<div style="display:grid; gap:14px;">' +
                  '<div style="display:flex; align-items:center; gap:12px; border-bottom:1px solid #f1f5f9; padding-bottom:14px;">' +
                    '<div style="width:46px; height:46px; border-radius:50%; background:#10b981; color:#ffffff; font-weight:900; font-size:15px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.15);">' +
                      initials +
                    '</div>' +
                    '<div style="min-width:0; flex:1;">' +
                      '<h4 style="font-size:15px; font-weight:800; color:#0f172a; margin:0; line-height:1.2;">' + (AcademiaData.userName || 'Mi Caso') + '</h4>' +
                      '<span style="font-size:11px; color:#64748b; display:block; margin-top:2px;">Alumno en Proceso de Titulación</span>' +
                      '<span style="display:inline-block; margin-top:6px; font-size:10px; font-weight:800; background:#eef2ff; color:#4338ca; padding:2px 8px; border-radius:9999px;">⏱️ ' + timelineVal + '</span>' +
                    '</div>' +
                  '</div>' +
                  '<div style="display:grid; gap:10px; font-size:12px;">' +
                    '<div>' +
                      '<span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; display:block;">Antes:</span>' +
                      '<p style="margin:2px 0 0 0; color:#475569; font-style:italic;">"' + $('<div>').text(beforeVal).html() + '"</p>' +
                    '</div>' +
                    '<div>' +
                      '<span style="font-size:10px; font-weight:800; color:#4f46e5; text-transform:uppercase; display:block;">Acción:</span>' +
                      '<p style="margin:2px 0 0 0; color:#1e293b; font-weight:600;">' + $('<div>').text(actionVal).html() + '</p>' +
                    '</div>' +
                    '<div>' +
                      '<span style="font-size:10px; font-weight:800; color:#059669; text-transform:uppercase; display:block;">Resultado:</span>' +
                      '<p style="margin:4px 0 0 0; color:#065f46; font-weight:800; background:#f0fdf4; border:1px solid #bbf7d0; padding:10px 12px; border-radius:10px;">' + $('<div>').text(resultVal).html() + '</p>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div style="border-top:1px solid #f1f5f9; padding-top:12px; display:flex; justify-content:space-between; align-items:center; font-size:10px; color:#94a3b8;">' +
                  '<span style="color:#d97706; font-weight:700;">🟡 Pendiente de Moderación Final</span>' +
                  '<span>Recién enviado</span>' +
                '</div>' +
              '</div>';

            $('#impact-stories-container').prepend(newCardHtml);
            $('#form-publish-impact')[0].reset();
            alert('¡Tu caso de impacto ha sido registrado con éxito y enviado para verificación!');
          },
          error: function() {
            $btn.prop('disabled', false).text('✨ Registrar Caso de Impacto');
            alert('Error en el servidor al registrar el caso.');
          }
        });
      });
    }
  };

  $(document).ready(function() {
    AcademiaGraduation.init();
  });
})(jQuery);
