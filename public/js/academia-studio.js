/**
 * Academia Tectonica - Studio & Interactive Lab JS
 */
(function($) {
  'use strict';

  window.AcademiaStudio = {
    decisions: [],

    init: function() {
      if (typeof AcademiaData === 'undefined') return;

      // Initialize decisions from DB or defaults
      if (AcademiaData.studioMatrix && Array.isArray(AcademiaData.studioMatrix)) {
        this.decisions = AcademiaData.studioMatrix;
      } else {
        this.decisions = [
          { id: 1, text: 'Grabar lecciones módulo 1', impact: 'Alto', effort: 'Bajo' },
          { id: 2, text: 'Crear diseño 3D para la portada', impact: 'Bajo', effort: 'Alto' },
          { id: 3, text: 'Subir plantillas de Notion', impact: 'Alto', effort: 'Bajo' },
          { id: 4, text: 'Rediseñar todo el PDF interactivo', impact: 'Alto', effort: 'Alto' }
        ];
      }

      this.renderMatrix();
      this.bindMatrixEvents();
      this.bindCaosEvents();
    },

    renderMatrix: function() {
      var self = this;
      var $qw = $('#quadrant-list-quick-wins').empty();
      var $mp = $('#quadrant-list-major-projects').empty();
      var $ft = $('#quadrant-list-fill-tasks').empty();
      var $ts = $('#quadrant-list-time-sinks').empty();

      self.decisions.forEach(function(item) {
        var $el = $('<div class="aca-decision-card-item" style="background:#ffffff; padding:10px 12px; border-radius:10px; border:1px solid rgba(0,0,0,0.08); margin-bottom:8px; display:flex; justify-content:space-between; align-items:center; font-size:13px; font-weight:600; color:#1e293b; box-shadow:0 1px 3px rgba(0,0,0,0.03);">' +
          '<span style="flex:1; min-width:0; padding-right:8px; line-height:1.4;">' + $('<div>').text(item.text).html() + '</span>' +
          '<button type="button" class="btn-remove-decision" data-id="' + item.id + '" style="background:transparent !important; border:none !important; color:#94a3b8 !important; cursor:pointer !important; font-size:18px !important; line-height:1 !important; padding:4px 8px !important; margin:0 !important; width:auto !important; height:auto !important; min-width:unset !important; min-height:unset !important; box-shadow:none !important; border-radius:6px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important;" title="Eliminar de la matriz">&times;</button>' +
        '</div>');

        if (item.impact === 'Alto' && item.effort === 'Bajo') {
          $qw.append($el);
        } else if (item.impact === 'Alto' && item.effort === 'Alto') {
          $mp.append($el);
        } else if (item.impact === 'Bajo' && item.effort === 'Bajo') {
          $ft.append($el);
        } else {
          $ts.append($el);
        }
      });
    },

    bindMatrixEvents: function() {
      var self = this;

      // Add decision to Matrix
      $('#form-add-matrix-decision').on('submit', function(e) {
        e.preventDefault();
        var text = $('#matrix-input-text').val().trim();
        var impact = $('#matrix-select-impact').val();
        var effort = $('#matrix-select-effort').val();

        if (!text) return;

        var newItem = {
          id: Date.now(),
          text: text,
          impact: impact,
          effort: effort
        };

        self.decisions.push(newItem);
        self.renderMatrix();
        self.saveMatrix();

        $('#matrix-input-text').val('');
      });

      // Remove decision
      $(document).on('click', '.btn-remove-decision', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        self.decisions = self.decisions.filter(function(d) { return d.id != id; });
        self.renderMatrix();
        self.saveMatrix();
      });
    },

    saveMatrix: function() {
      var self = this;
      var $status = $('#matrix-save-status');
      $status.text('Guardando...');

      $.ajax({
        url: AcademiaData.ajaxUrl,
        type: 'POST',
        data: {
          action: 'academia_save_studio_matrix',
          nonce: AcademiaData.nonce,
          course_id: AcademiaData.currentCourse.id,
          decisions: JSON.stringify(self.decisions)
        },
        success: function(res) {
          if (res.success) {
            $status.text('✓ Guardado en tu perfil');
            setTimeout(function() { $status.text(''); }, 2500);
          }
        }
      });
    },

    bindCaosEvents: function() {
      var self = this;
      var autoSaveTimer = null;

      $('.caos-input').on('input', function() {
        clearTimeout(autoSaveTimer);
        $('#caos-save-status').text('Escribiendo...');
        autoSaveTimer = setTimeout(function() {
          self.saveCaos();
        }, 1200);
      });

      $('#form-save-caos').on('submit', function(e) {
        e.preventDefault();
        self.saveCaos();
      });
    },

    saveCaos: function() {
      var payload = {
        action: 'academia_save_studio_caos',
        nonce: AcademiaData.nonce,
        course_id: AcademiaData.currentCourse.id,
        caos1: $('#caos-1').val(),
        caos2: $('#caos-2').val(),
        caos3: $('#caos-3').val(),
        control1: $('#control-1').val(),
        control2: $('#control-2').val(),
        nextAction: $('#next-action').val()
      };

      var $status = $('#caos-save-status');
      $status.text('Guardando...');

      $.ajax({
        url: AcademiaData.ajaxUrl,
        type: 'POST',
        data: payload,
        success: function(res) {
          if (res.success) {
            $status.text('✓ Protocolo guardado');
            setTimeout(function() { $status.text(''); }, 2500);
          }
        }
      });
    }
  };

  $(document).ready(function() {
    AcademiaStudio.init();
  });
})(jQuery);
