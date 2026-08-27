/**
 * Academia Tectonica - Core JS (Matching React Mockup Interactivity)
 */
(function($) {
  'use strict';

  window.Academia = {
    state: {
      activeTab: 'modules',
      selectedTag: 'Todos',
      searchQuery: '',
      watchedLessons: {},
      readLessons: {}
    },

    init: function() {
      if (typeof AcademiaData === 'undefined') return;

      this.state.watchedLessons = (AcademiaData.progress && AcademiaData.progress.watched) ? AcademiaData.progress.watched : {};
      this.state.readLessons = (AcademiaData.progress && AcademiaData.progress.read) ? AcademiaData.progress.read : {};

      this.bindCourseSwitcher();
      this.bindTabNavigation();
      this.bindModuleAccordion();
      this.bindLessonSelection();
      this.bindSearchAndFilters();
      this.bindProgressToggles();
    },

    showToast: function(msg) {
      var $toast = $('#academia-toast');
      $('#academia-toast-text').text(msg);
      $toast.stop(true, true).fadeIn(200);
      setTimeout(function() {
        $toast.fadeOut(300);
      }, 3000);
    },

    bindCourseSwitcher: function() {
      var self = this;
      $('#academia-course-switcher').on('change', function() {
        var $opt = $(this).find(':selected');
        var url = $opt.data('url');
        var enrolled = $opt.data('enrolled');
        var purchaseUrl = $opt.data('purchase');

        self.showToast('Cambiando de curso...');
        window.location.href = url;
      });
    },

    bindTabNavigation: function() {
      var self = this;
      $('.aca-tab-btn, [data-tab-target]').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab') || $(this).data('tab-target');
        self.switchTab(tab);
      });
    },

    switchTab: function(tab) {
      this.state.activeTab = tab;
      $('.aca-tab-btn').removeClass('active');
      $('.aca-tab-btn[data-tab="' + tab + '"]').addClass('active');

      $('.aca-tab-pane').hide();
      $('#tab-content-' + tab).fadeIn(150);

      // Scroll smoothly to top of main area
      $('html, body').animate({
        scrollTop: $('.aca-sticky-nav-bar').offset().top - 20
      }, 200);
    },

    bindModuleAccordion: function() {
      $('[data-toggle-module]').on('click', function() {
        var modId = $(this).data('toggle-module');
        var $card = $('#module-card-' + modId);
        var $body = $card.find('.aca-module-card-body');
        var $arrow = $card.find('.aca-arrow-icon');

        if ($body.is(':visible')) {
          $body.slideUp(200);
          $arrow.text('▼');
          $card.removeClass('expanded');
        } else {
          $body.slideDown(200);
          $arrow.text('▲');
          $card.addClass('expanded');
        }
      });
    },

    bindLessonSelection: function() {
      var self = this;
      $('.aca-lesson-card-item').on('click', function(e) {
        e.preventDefault();
        var $item = $(this);
        var modId = $item.data('mod-id');
        var lesson = $item.data('lesson-raw');
        if (!lesson) return;

        // Set selected style on picker items
        $('#video-stage-mod-' + modId + ' .aca-lesson-card-item').removeClass('selected');
        $('#video-stage-mod-' + modId + ' .aca-les-order-num').removeClass('active');
        $item.addClass('selected');
        $item.find('.aca-les-order-num').addClass('active');

        // Update stage player fields
        $('#player-code-' + modId).text((lesson.lesson_code || 'L1') + ' · ' + (lesson.type || '🎬 Masterclass'));
        $('#player-duration-' + modId).text('HD 1080p • ⏱️ ' + (lesson.duration || '12 min'));
        $('#player-vtitle-' + modId).text(lesson.video_title || lesson.title);
        $('#player-type-' + modId).text(lesson.type || '🎬 Masterclass');
        $('#player-title-' + modId).text(lesson.title);
        $('#player-learn-' + modId).text(lesson.what_you_will_learn || 'Conceptos clave de estructuración.');
        $('#player-utility-' + modId).text(lesson.business_utility || 'Aplicación en tu negocio.');
        $('#reading-code-' + modId).text(lesson.lesson_code || 'L1');
        $('#reading-text-' + modId).text('"' + (lesson.reading_text || 'Lectura complementaria disponible próximamente.') + '"');

        // Update video iframe player
        var vUrl = lesson.video_url || 'https://youtu.be/MeKlBPHgmJ0';
        var embedSrc = 'https://www.youtube-nocookie.com/embed/MeKlBPHgmJ0?rel=0';
        if (vUrl.indexOf('youtu') !== -1) {
          var regExp = /(?:youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*)/;
          var match = vUrl.match(regExp);
          var ytId = (match && match[1] && match[1].length === 11) ? match[1] : 'MeKlBPHgmJ0';
          embedSrc = 'https://www.youtube-nocookie.com/embed/' + ytId + '?rel=0';
        } else if (vUrl.indexOf('vimeo') !== -1) {
          var vimeoId = vUrl.split('/').pop();
          embedSrc = 'https://player.vimeo.com/video/' + vimeoId;
        } else if (vUrl.indexOf('mediadelivery.net') !== -1) {
          embedSrc = vUrl;
        }
        $('#iframe-player-' + modId).attr('src', embedSrc);

        self.showToast('Cargando: ' + lesson.title);
      });
    },

    bindSearchAndFilters: function() {
      var self = this;

      // Filter chips
      $('.aca-filter-chip').on('click', function() {
        $('.aca-filter-chip').removeClass('active');
        $(this).addClass('active');
        self.state.selectedTag = $(this).data('tag');
        self.applyModuleFilters();
      });

      // Search input
      $('#aca-module-search-input').on('input', function() {
        self.state.searchQuery = $(this).val().toLowerCase().trim();
        self.applyModuleFilters();
      });
    },

    applyModuleFilters: function() {
      var self = this;
      $('.aca-module-card').each(function() {
        var $card = $(this);
        var tag = $card.data('tag') || '';
        var text = $card.text().toLowerCase();

        var matchesTag = (self.state.selectedTag === 'Todos' || tag.indexOf(self.state.selectedTag.replace(/^[A-Z] · /, '')) !== -1 || tag === self.state.selectedTag);
        var matchesSearch = (self.state.searchQuery === '' || text.indexOf(self.state.searchQuery) !== -1);

        if (matchesTag && matchesSearch) {
          $card.show();
        } else {
          $card.hide();
        }
      });
    },

    bindProgressToggles: function() {
      var self = this;

      // Toggle Watched from Player
      $(document).on('click', '.aca-btn-watched, .aca-quick-watch-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var lessonId = $btn.data('lesson-id');
        var modId = $btn.data('mod-id');
        var isCompleted = !$btn.hasClass('watched') && !$btn.hasClass('is-watched');

        self.state.watchedLessons[lessonId] = isCompleted;

        // Update UI
        if ($btn.hasClass('aca-btn-watched')) {
          $btn.toggleClass('watched', isCompleted);
          $btn.find('span').text(isCompleted ? '✓ Lección Vista' : '▶️ Reproducir Vídeo');
        }
        $('.aca-quick-watch-btn[data-lesson-id="' + lessonId + '"]').toggleClass('is-watched', isCompleted).text(isCompleted ? '✓ Visto' : '○ Marcar visto');

        self.showToast(isCompleted ? '✓ Lección marcada como completada' : 'Lección desmarcada');

        // AJAX
        $.ajax({
          url: AcademiaData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_save_lesson_progress',
            nonce: AcademiaData.nonce,
            lesson_id: lessonId,
            course_id: AcademiaData.currentCourse.id,
            type: 'watched',
            completed: isCompleted ? 1 : 0
          }
        });
      });

      // Toggle Read from Reading Box
      $(document).on('click', '.aca-btn-read-toggle', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var lessonId = $btn.data('lesson-id');
        var isCompleted = !$btn.hasClass('completed');

        self.state.readLessons[lessonId] = isCompleted;
        $btn.toggleClass('completed', isCompleted);
        $btn.text(isCompleted ? '✓ Lección Leída y Completada' : 'Marcar como Leído');

        self.showToast(isCompleted ? '✓ Lectura completada' : 'Lectura desmarcada');

        $.ajax({
          url: AcademiaData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_save_lesson_progress',
            nonce: AcademiaData.nonce,
            lesson_id: lessonId,
            course_id: AcademiaData.currentCourse.id,
            type: 'read',
            completed: isCompleted ? 1 : 0
          }
        });
      });
    }
  };

  $(document).ready(function() {
    Academia.init();
  });
})(jQuery);
