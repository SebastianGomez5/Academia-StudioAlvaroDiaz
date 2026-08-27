/**
 * Academia Tectónica - Mentor & Admin Dashboard Controller
 * Matches 100% of panel_de_control_y_mentor_a_tect_nica.tsx logic
 */
(function($) {
  'use strict';

  window.AcademiaMentorApp = {
    state: {
      activeUserId: 'admin',
      selectedCourseFilter: 'all',
      activeTab: 'finance',
      audits: [],
      sessions1a1: [],
      postSaleServices: [],
      payoutHistory: [],
      userProfiles: [],
      coursesCatalog: [],
      currentAuditModal: null,
      auditDecision: 'approved'
    },

    init: function() {
      if (typeof MentorDashboardData === 'undefined') return;

      this.state.activeUserId = MentorDashboardData.initialUserId || 'admin';
      this.state.userProfiles = MentorDashboardData.userProfiles || [];
      this.state.coursesCatalog = MentorDashboardData.coursesCatalog || [];
      this.state.audits = MentorDashboardData.audits || [];
      this.state.sessions1a1 = MentorDashboardData.sessions1a1 || [];
      this.state.postSaleServices = MentorDashboardData.postSaleServices || [];
      this.state.payoutHistory = MentorDashboardData.payoutHistory || [];

      this.bindUserAndCourseSelectors();
      this.bindTabNavigation();
      this.bindModals();
      this.bindAuditActions();
      this.bindWithdrawal();
      this.bindCMSActions();

      this.renderAll();
    },

    showToast: function(msg) {
      var $toast = $('#mentor-toast');
      $('#mentor-toast-text').text(msg);
      $toast.stop(true, true).fadeIn(200);
      setTimeout(function() {
        $toast.fadeOut(300);
      }, 3500);
    },

    getActiveUser: function() {
      var self = this;
      var found = self.state.userProfiles.find(function(u) { return u.id === self.state.activeUserId; });
      return found || self.state.userProfiles[0];
    },

    getAccessibleCourses: function() {
      var self = this;
      var user = self.getActiveUser();
      if (user.isAdmin) {
        return self.state.coursesCatalog;
      }
      return self.state.coursesCatalog.filter(function(c) {
        return user.assignedCourses && user.assignedCourses.indexOf(c.id) !== -1;
      });
    },

    getScopedCourses: function() {
      var self = this;
      var accessible = self.getAccessibleCourses();
      if (self.state.selectedCourseFilter === 'all') {
        return accessible;
      }
      return accessible.filter(function(c) {
        return c.id === self.state.selectedCourseFilter;
      });
    },

    calculateFinancialSplit: function() {
      var self = this;
      var scoped = self.getScopedCourses();
      var user = self.getActiveUser();

      // 1. Gross from courses
      var coursesGross = scoped.reduce(function(acc, c) { return acc + (c.revenue || 0); }, 0);

      // 2. 1a1 calls
      var relevantCalls = user.isAdmin
        ? self.state.sessions1a1
        : self.state.sessions1a1.filter(function(s) { return s.mentorId === user.id; });
      var callsGross = relevantCalls.length * 97;

      // 3. Extra audits
      var relevantAudits = user.isAdmin
        ? self.state.audits
        : self.state.audits.filter(function(a) { return user.assignedCourses.indexOf(a.courseId) !== -1; });
      var extraAuditsGross = relevantAudits.filter(function(a) { return a.isExtraPaid; }).length * 47;

      var totalGrossRevenue = coursesGross + callsGross + extraAuditsGross;
      var operationalCosts20 = Math.round(totalGrossRevenue * 0.20);
      var netDistributablePool80 = totalGrossRevenue - operationalCosts20;
      var academyShare40 = Math.round(netDistributablePool80 * 0.40);
      var professionalShare60 = netDistributablePool80 - academyShare40;

      var totalStudentsCount = scoped.reduce(function(acc, c) { return acc + (c.students || 0); }, 0);
      var pendingAuditsCount = relevantAudits.filter(function(a) { return a.status === 'pending' || a.status === 'reviewing'; }).length;
      var confirmedCallsCount = relevantCalls.filter(function(c) { return c.status === 'confirmed'; }).length;

      return {
        totalGrossRevenue: totalGrossRevenue,
        operationalCosts20: operationalCosts20,
        netDistributablePool80: netDistributablePool80,
        academyShare40: academyShare40,
        professionalShare60: professionalShare60,
        totalStudentsCount: totalStudentsCount,
        pendingAuditsCount: pendingAuditsCount,
        confirmedCallsCount: confirmedCallsCount,
        relevantCallsCount: relevantCalls.length
      };
    },

    renderAll: function() {
      var self = this;
      var user = self.getActiveUser();
      var accessible = self.getAccessibleCourses();
      var split = self.calculateFinancialSplit();

      // Header Updates
      $('#header-user-avatar').text(user.avatar);
      $('#header-user-name').text(user.name);
      $('#header-user-sub').html(user.role + ' · <strong class="highlight-text">' + accessible.length + (accessible.length === 1 ? ' curso asignado' : ' cursos asignados') + '</strong>');
      
      var $badge = $('#header-role-badge');
      $badge.removeClass('admin-badge pro-badge')
            .addClass(user.isAdmin ? 'admin-badge' : 'pro-badge')
            .text(user.isAdmin ? '👑 PANEL ADMINISTRADOR GLOBAL' : '👨‍🏫 PANEL DEL PROFESIONAL');

      // Update Course Filter Select Dropdown
      var $courseSelect = $('#select-course-filter').empty();
      $courseSelect.append('<option value="all">🌐 Todos mis cursos asignados (' + accessible.length + ')</option>');
      accessible.forEach(function(c) {
        var isSel = (self.state.selectedCourseFilter === c.id) ? ' selected' : '';
        $courseSelect.append('<option value="' + c.id + '"' + isSel + '>' + c.icon + ' ' + c.shortName + '</option>');
      });

      // Top Formula Banner Stats
      $('#stat-total-students').text(split.totalStudentsCount);
      $('#stat-pending-audits').text(split.pendingAuditsCount);

      // Financial Metric Cards
      $('#val-gross-revenue').text(split.totalGrossRevenue.toLocaleString('es-ES') + ' €');
      $('#val-op-costs').text('-' + split.operationalCosts20.toLocaleString('es-ES') + ' €');
      $('#val-net-pool').text(split.netDistributablePool80.toLocaleString('es-ES') + ' €');
      $('#val-academy-share').text(split.academyShare40.toLocaleString('es-ES') + ' €');
      $('#val-professional-share').text(split.professionalShare60.toLocaleString('es-ES') + ' €');
      $('#val-withdrawable-balance').text(user.withdrawableBalance.toLocaleString('es-ES') + ' €');

      // Nav Tab Badges
      $('#nav-audits-count').text(split.pendingAuditsCount).toggle(split.pendingAuditsCount > 0);
      $('#nav-calls-count').text(split.confirmedCallsCount).toggle(split.confirmedCallsCount > 0);

      // Render Individual Tab Contents
      self.renderFinanceTab(split, user);
      self.renderAuditsTab(user);
      self.renderCallsTab(user);
      self.renderCMSTab();
      self.renderUpsellsTab();
    },

    renderFinanceTab: function(split, user) {
      var self = this;
      var scoped = self.getScopedCourses();

      $('#evo-user-name').text('Actividad Financiera de ' + user.name);
      var monthSales = Math.round(split.totalGrossRevenue * 0.38);
      var prevMonthSales = Math.round(split.totalGrossRevenue * 0.30);
      var growthNet = monthSales - prevMonthSales;

      $('#evo-sales-month').text(monthSales.toLocaleString('es-ES') + ' €');
      $('#evo-prev-month').text(prevMonthSales.toLocaleString('es-ES') + ' €');
      $('#evo-growth-net').text('+' + growthNet.toLocaleString('es-ES') + ' € netos');

      // Waterfall Table
      $('#waterfall-courses-count').text(scoped.length);
      var $tbody = $('#waterfall-table-body').empty();

      scoped.forEach(function(c) {
        var gross = c.revenue || 0;
        var op = Math.round(gross * 0.20);
        var net = gross - op;
        var acad = Math.round(net * 0.40);
        var prof = net - acad;

        $tbody.append(
          '<tr>' +
            '<td class="font-bold text-white"><span style="font-size:18px; margin-right:8px;">' + c.icon + '</span>' + c.shortName + '</td>' +
            '<td class="text-slate">' + c.students + '</td>' +
            '<td class="font-bold text-white">' + gross.toLocaleString('es-ES') + ' €</td>' +
            '<td class="text-rose">-' + op.toLocaleString('es-ES') + ' €</td>' +
            '<td class="text-indigo">' + net.toLocaleString('es-ES') + ' €</td>' +
            '<td class="text-purple">' + acad.toLocaleString('es-ES') + ' €</td>' +
            '<td class="text-emerald font-bold" style="background:rgba(6,78,59,0.2);">' + prof.toLocaleString('es-ES') + ' €</td>' +
          '</tr>'
        );
      });

      $('#waterfall-table-foot').html(
        '<tr>' +
          '<td class="text-amber">TOTAL CONSOLIDADO</td>' +
          '<td class="text-white">' + split.totalStudentsCount + '</td>' +
          '<td class="text-white">' + split.totalGrossRevenue.toLocaleString('es-ES') + ' €</td>' +
          '<td class="text-rose">-' + split.operationalCosts20.toLocaleString('es-ES') + ' €</td>' +
          '<td class="text-indigo">' + split.netDistributablePool80.toLocaleString('es-ES') + ' €</td>' +
          '<td class="text-purple">' + split.academyShare40.toLocaleString('es-ES') + ' €</td>' +
          '<td class="text-emerald font-bold" style="background:rgba(6,78,59,0.4); font-size:13px;">' + split.professionalShare60.toLocaleString('es-ES') + ' €</td>' +
        '</tr>'
      );

      // Wallet Details
      $('#w-iban').text(user.payoutIban);
      $('#w-paidout').text(user.totalPaidOut.toLocaleString('es-ES') + ' €');
      $('#w-escrow').text(user.escrowBalance.toLocaleString('es-ES') + ' €');

      // Payout History List
      var $pList = $('#payouts-list-container').empty();
      $('#payouts-count-label').text(self.state.payoutHistory.length + ' Transferencias SEPA');

      self.state.payoutHistory.forEach(function(pay) {
        $pList.append(
          '<div class="payout-item-card">' +
            '<div>' +
              '<div style="display:flex; align-items:center; gap:8px;">' +
                '<strong class="text-white font-mono" style="font-size:14px;">' + pay.amount.toLocaleString('es-ES') + ' €</strong>' +
                '<span style="font-size:10px; background:rgba(16,185,129,0.2); color:#6ee7b7; padding:2px 8px; border-radius:9999px; border:1px solid rgba(16,185,129,0.3);">' +
                  'Transferencia Completada' +
                '</span>' +
              '</div>' +
              '<span style="color:#94a3b8; font-size:11px; display:block; margin-top:2px;">Destinatario: ' + pay.recipient + ' · Cuenta: ' + pay.iban + '</span>' +
            '</div>' +
            '<div style="display:flex; align-items:center; gap:12px;">' +
              '<span class="font-mono text-slate" style="font-size:11px;">' + pay.date + '</span>' +
              '<button type="button" class="btn-cancel btn-receipt" data-receipt="' + pay.receiptId + '" style="padding:6px 12px; font-size:11px;">' +
                '📥 ' + pay.receiptId +
              '</button>' +
            '</div>' +
          '</div>'
        );
      });
    },

    renderAuditsTab: function(user) {
      var self = this;
      var filtered = self.state.audits;

      if (!user.isAdmin) {
        filtered = filtered.filter(function(a) {
          return user.assignedCourses && user.assignedCourses.indexOf(a.courseId) !== -1;
        });
      }

      if (self.state.selectedCourseFilter !== 'all') {
        filtered = filtered.filter(function(a) { return a.courseId === self.state.selectedCourseFilter; });
      }

      $('#audits-header-user').text('Cola de Auditoría para: ' + user.name);
      $('#audits-count-pill').text(filtered.length + ' Expedientes de tus cursos');

      var $list = $('#audits-list-container').empty();

      if (filtered.length === 0) {
        $list.html('<div style="background:#0f172a; padding:32px; border-radius:24px; text-align:center; color:#94a3b8; border:1px solid #1e293b;">✓ No hay auditorías pendientes en los cursos seleccionados.</div>');
        return;
      }

      filtered.forEach(function(a) {
        var statusBadge = '';
        if (a.status === 'approved') {
          statusBadge = '<span style="background:rgba(16,185,129,0.2); color:#6ee7b7; border:1px solid rgba(16,185,129,0.4); padding:3px 10px; border-radius:9999px; font-size:10px; font-weight:800;">✓ Graduación Aprobada</span>';
        } else if (a.status === 'needs_changes') {
          statusBadge = '<span style="background:rgba(225,29,72,0.2); color:#fda4af; border:1px solid rgba(225,29,72,0.4); padding:3px 10px; border-radius:9999px; font-size:10px; font-weight:800;">⚠️ Corrección Requerida</span>';
        } else {
          statusBadge = '<span style="background:rgba(245,158,11,0.2); color:#fcd34d; border:1px solid rgba(245,158,11,0.4); padding:3px 10px; border-radius:9999px; font-size:10px; font-weight:800;">⏳ Pendiente de Auditoría</span>';
        }

        var pillsHtml = '';
        if (a.deliverables) {
          Object.keys(a.deliverables).forEach(function(k) {
            var deliv = a.deliverables[k];
            var isOk = deliv.status === 'complete';
            pillsHtml += 
              '<div class="deliv-pill">' +
                '<div class="deliv-pill-top">' +
                  '<span style="font-weight:700; color:#cbd5e1;" class="truncate">' + deliv.name + '</span>' +
                  '<span style="font-weight:900; color:' + (isOk ? '#34d399' : '#f43f5e') + ';">' + (isOk ? '✓' : '✕') + '</span>' +
                '</div>' +
                '<span style="font-size:10px; color:#64748b; display:block; margin-top:2px;">' + (deliv.notes || (isOk ? 'Completado' : 'Pendiente')) + '</span>' +
              '</div>';
          });
        }

        var feedbackNote = a.mentorNotes ? ('<div style="background:#020617; padding:12px 16px; border-radius:14px; border:1px solid #1e293b; font-size:12px; color:#cbd5e1; font-style:italic;"><strong style="color:#fbbf24; font-style:normal; display:block; margin-bottom:2px;">Observación previa del Mentor:</strong>"' + a.mentorNotes + '"</div>') : '';

        $list.append(
          '<div class="audit-card" data-audit-id="' + a.id + '">' +
            '<div class="audit-card-top">' +
              '<div class="student-info-row">' +
                '<div class="student-avatar-badge">' + a.studentName.substring(0, 2).toUpperCase() + '</div>' +
                '<div>' +
                  '<div style="display:flex; align-items:center; gap:8px;">' +
                    '<h3 class="student-name-h3">' + a.studentName + '</h3>' +
                    '<span class="font-mono text-slate text-xs">(' + a.studentEmail + ')</span>' +
                  '</div>' +
                  '<span style="color:#a5b4fc; font-weight:700; font-size:12px; display:block; margin-top:2px;">' + a.courseName + '</span>' +
                  '<span style="color:#64748b; font-size:11px; display:block; margin-top:2px;">Enviado: ' + a.submittedAt + '</span>' +
                '</div>' +
              '</div>' +
              '<div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">' +
                '<div style="text-align:right;">' +
                  statusBadge +
                  '<span class="font-mono text-slate text-xs" style="display:block; margin-top:4px;">Ronda ' + a.round + ' de ' + a.maxRounds + (a.isExtraPaid ? ' (3ª Ronda Extra 47€)' : '') + '</span>' +
                '</div>' +
                '<button type="button" class="btn-primary-amber btn-open-audit-modal" data-audit-id="' + a.id + '">' +
                  '<span>🔍</span> Auditar Entregables' +
                '</button>' +
              '</div>' +
            '</div>' +
            '<div class="deliverables-pills-grid">' + pillsHtml + '</div>' +
            feedbackNote +
          '</div>'
        );
      });
    },

    renderCallsTab: function(user) {
      var self = this;
      var filtered = self.state.sessions1a1;

      if (!user.isAdmin) {
        filtered = filtered.filter(function(s) { return s.mentorId === user.id; });
      }

      if (self.state.selectedCourseFilter !== 'all') {
        filtered = filtered.filter(function(s) { return s.courseId === self.state.selectedCourseFilter; });
      }

      $('#calls-header-user').text('Agenda de Videollamadas 1 a 1 de ' + user.name);
      $('#calls-revenue-pill').text((filtered.length * 97).toLocaleString('es-ES') + ' € Facturados en Asesorías');

      var $grid = $('#calls-list-container').empty();

      if (filtered.length === 0) {
        $grid.html('<div style="grid-column:1/-1; background:#0f172a; padding:32px; border-radius:24px; text-align:center; color:#94a3b8; border:1px solid #1e293b;">📅 No hay videollamadas 1 a 1 programadas en esta vista.</div>');
        return;
      }

      filtered.forEach(function(call) {
        $grid.append(
          '<div class="call-card">' +
            '<div style="display:flex; flex-direction:column; gap:12px;">' +
              '<div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #1e293b; padding-bottom:12px;">' +
                '<div style="display:flex; align-items:center; gap:10px;">' +
                  '<span style="font-size:24px;">📅</span>' +
                  '<div>' +
                    '<span class="font-mono font-bold text-white text-xs" style="display:block;">' + call.date + ' · ' + call.time + '</span>' +
                    '<span class="text-emerald font-bold" style="font-size:10px; text-transform:uppercase;">Tarifa Abonada: ' + call.amountPaid + ' € (Vía Stripe)</span>' +
                  '</div>' +
                '</div>' +
                '<span style="background:rgba(16,185,129,0.2); color:#6ee7b7; border:1px solid rgba(16,185,129,0.3); padding:3px 10px; border-radius:9999px; font-size:10px; font-weight:800; text-transform:uppercase;">Confirmada</span>' +
              '</div>' +
              '<div>' +
                '<h3 style="font-size:16px; font-weight:900; color:#ffffff; margin:0;">' + call.studentName + '</h3>' +
                '<span style="color:#a5b4fc; font-weight:700; font-size:12px; display:block;">' + call.courseName + '</span>' +
                '<span class="font-mono text-slate text-xs">' + call.studentEmail + '</span>' +
              '</div>' +
              '<div style="background:#020617; padding:12px 14px; border-radius:14px; border:1px solid #1e293b; font-size:12px; display:flex; flex-direction:column; gap:6px;">' +
                '<div><strong style="color:#fbbf24; font-size:10px; text-transform:uppercase; display:block;">Objetivo de la Sesión:</strong><p style="color:#cbd5e1; margin:2px 0 0 0;">' + call.objective + '</p></div>' +
                '<div><strong style="color:#94a3b8; font-size:10px; text-transform:uppercase; display:block;">Notas de Contexto:</strong><p style="color:#94a3b8; margin:2px 0 0 0; font-style:italic;">' + call.notes + '</p></div>' +
              '</div>' +
            '</div>' +
            '<div style="display:flex; gap:10px; padding-top:12px; border-top:1px solid #1e293b;">' +
              '<a href="' + call.meetUrl + '" target="_blank" rel="noopener noreferrer" style="flex:1; padding:10px 14px; background:#4f46e5; color:#ffffff; font-weight:800; font-size:12px; border-radius:12px; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:8px;">' +
                '<span>📹</span> Entrar a Google Meet' +
              '</a>' +
              '<button type="button" class="btn-cancel btn-call-reminder" data-student="' + call.studentName + '" style="padding:10px 14px; font-size:12px;">' +
                '🔔 Recordar' +
              '</button>' +
            '</div>' +
          '</div>'
        );
      });
    },

    renderCMSTab: function() {
      var self = this;
      var scoped = self.getScopedCourses();

      $('#cms-courses-count-pill').text(scoped.length + (scoped.length === 1 ? ' Curso Activo' : ' Cursos Activos'));
      var $grid = $('#cms-courses-container').empty();

      scoped.forEach(function(c) {
        $grid.append(
          '<div class="cms-course-card">' +
            '<div style="display:flex; flex-direction:column; gap:12px;">' +
              '<div style="display:flex; justify-content:space-between; align-items:center;">' +
                '<span style="font-size:28px; padding:8px; background:#1e293b; border-radius:14px;">' + c.icon + '</span>' +
                '<span style="font-size:10px; font-weight:800; background:rgba(16,185,129,0.15); color:#6ee7b7; padding:3px 10px; border-radius:9999px; border:1px solid rgba(16,185,129,0.3);">✓ Publicado</span>' +
              '</div>' +
              '<div>' +
                '<h3 style="font-size:15px; font-weight:900; color:#ffffff; margin:0; line-height:1.3;">' + c.name + '</h3>' +
                '<span style="font-size:11px; color:#94a3b8; display:block; margin-top:4px;">Precio: <strong class="font-mono text-amber">' + c.price + ' €</strong> · Alumnos: <strong class="font-mono text-white">' + c.students + '</strong></span>' +
              '</div>' +
            '</div>' +
            '<div style="display:flex; gap:8px; padding-top:12px; border-top:1px solid #1e293b;">' +
              '<button type="button" class="btn-cancel btn-edit-cms-course" data-course-id="' + c.id + '" data-course-name="' + c.name + '" style="flex:1; padding:8px 12px; font-size:11px; display:flex; align-items:center; justify-content:center; gap:6px;">' +
                '<span>✏️</span> Editar Vídeos' +
              '</button>' +
              '<button type="button" class="btn-cancel btn-cms-resources" data-course-name="' + c.shortName + '" style="padding:8px 12px; font-size:11px;">' +
                '📎 Recursos' +
              '</button>' +
            '</div>' +
          '</div>'
        );
      });
    },

    renderUpsellsTab: function() {
      var self = this;
      var $grid = $('#upsells-list-container').empty();

      self.state.postSaleServices.forEach(function(srv) {
        $grid.append(
          '<div class="upsell-card">' +
            '<div style="display:flex; flex-direction:column; gap:12px;">' +
              '<div style="display:flex; justify-content:space-between; align-items:center;">' +
                '<span style="font-size:10px; font-weight:900; background:rgba(245,158,11,0.2); color:#fcd34d; padding:3px 10px; border-radius:9999px; border:1px solid rgba(245,158,11,0.3);">' + srv.badge + '</span>' +
                '<div style="text-align:right;">' +
                  '<span style="font-size:22px; font-weight:900; color:#ffffff; font-family:monospace;">' + srv.price.toLocaleString('es-ES') + ' €</span>' +
                  '<span class="font-mono text-slate" style="font-size:10px; display:block;">/ ' + srv.billingType + '</span>' +
                '</div>' +
              '</div>' +
              '<div>' +
                '<h3 style="font-size:16px; font-weight:900; color:#ffffff; margin:0;">' + srv.name + '</h3>' +
                '<span style="font-size:11px; color:#94a3b8; display:block; margin-top:2px;">Audiencia: <strong style="color:#cbd5e1;">' + srv.targetAudience + '</strong></span>' +
              '</div>' +
              '<p style="font-size:12px; color:#cbd5e1; background:#020617; padding:14px; border-radius:16px; border:1px solid #1e293b; line-height:1.5; margin:0;">' + srv.description + '</p>' +
            '</div>' +
            '<div style="display:flex; justify-content:space-between; align-items:center; padding-top:12px; border-top:1px solid #1e293b; font-size:12px;">' +
              '<div><span style="color:#94a3b8; font-size:10px; text-transform:uppercase; font-weight:700; display:block;">Clientes Activos:</span><strong class="text-emerald font-mono">' + srv.activeClients + ' contrataciones</strong></div>' +
              '<button type="button" class="btn-primary-amber btn-pitch-service" data-service-name="' + srv.name + '" style="padding:8px 14px; font-size:11px;">🚀 Ofertar a Graduados</button>' +
            '</div>' +
          '</div>'
        );
      });
    },

    bindUserAndCourseSelectors: function() {
      var self = this;

      $('#select-active-user').on('change', function() {
        self.state.activeUserId = $(this).val();
        self.state.selectedCourseFilter = 'all';
        self.showToast('Cambiando sesión de visualización a: ' + self.getActiveUser().name);
        self.renderAll();
      });

      $(document).on('change', '#select-course-filter', function() {
        self.state.selectedCourseFilter = $(this).val();
        self.showToast('Filtrando vista por: ' + (self.state.selectedCourseFilter === 'all' ? 'Todos mis cursos' : self.state.selectedCourseFilter));
        self.renderAll();
      });
    },

    bindTabNavigation: function() {
      var self = this;
      $('.nav-tab-btn').on('click', function() {
        var tab = $(this).data('tab');
        self.state.activeTab = tab;

        $('.nav-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.mentor-tab-pane').hide();
        $('#tab-pane-' + tab).fadeIn(150);
      });
    },

    bindModals: function() {
      var self = this;
      $('[data-close-modal]').on('click', function() {
        var modalKey = $(this).data('close-modal');
        $('#modal-' + modalKey).fadeOut(150);
      });

      $('.mentor-modal-overlay').on('click', function(e) {
        if ($(e.target).hasClass('mentor-modal-overlay')) {
          $(this).fadeOut(150);
        }
      });

      $(document).on('click', '.btn-receipt', function() {
        var receipt = $(this).data('receipt');
        self.showToast('Descargando justificante fiscal: ' + receipt + '.pdf');
      });

      $(document).on('click', '.btn-call-reminder', function() {
        var student = $(this).data('student');
        self.showToast('Recordatorio de sesión enviado por correo a ' + student);
      });

      $(document).on('click', '.btn-pitch-service', function() {
        var srv = $(this).data('service-name');
        self.showToast('Lanzando propuesta de \'' + srv + '\' a graduados recientes...');
      });
    },

    bindAuditActions: function() {
      var self = this;

      // Open Audit Modal
      $(document).on('click', '.btn-open-audit-modal', function() {
        var auditId = $(this).data('audit-id');
        var audit = self.state.audits.find(function(a) { return a.id === auditId; });
        if (!audit) return;

        self.state.currentAuditModal = audit;
        self.state.auditDecision = audit.status === 'approved' ? 'approved' : 'approved';

        $('#audit-modal-student-name').text('Evaluando a: ' + audit.studentName);
        $('#audit-modal-course-name').text(audit.courseName);
        $('#audit-modal-feedback-input').val(audit.mentorNotes || '');

        $('.btn-decision-choice').removeClass('active');
        $('.btn-decision-choice[data-decision="' + self.state.auditDecision + '"]').addClass('active');

        var $delivs = $('#audit-modal-deliverables-list').empty();
        if (audit.deliverables) {
          var idx = 1;
          Object.keys(audit.deliverables).forEach(function(k) {
            var d = audit.deliverables[k];
            var isOk = d.status === 'complete';
            var linkBtn = d.link ? ('<a href="' + d.link + '" target="_blank" class="btn-cancel" style="padding:6px 12px; font-size:11px; color:#a5b4fc; text-decoration:none;">Abrir Enlace Externo ↗</a>') : '';

            $delivs.append(
              '<div style="background:#020617; padding:12px 14px; border-radius:14px; border:1px solid #1e293b; display:flex; justify-content:space-between; align-items:center; font-size:12px;">' +
                '<div>' +
                  '<div style="display:flex; align-items:center; gap:8px;">' +
                    '<strong class="text-white">0' + (idx++) + '. ' + d.name + '</strong>' +
                    '<span style="font-size:10px; font-weight:800; color:' + (isOk ? '#34d399' : '#f43f5e') + ';">' + (isOk ? '✓ Válido' : '⚠️ Faltante') + '</span>' +
                  '</div>' +
                  '<span style="color:#64748b; font-size:11px; display:block; margin-top:2px;">' + (d.notes || 'Completado') + '</span>' +
                '</div>' +
                linkBtn +
              '</div>'
            );
          });
        }

        $('#modal-audit').fadeIn(150);
      });

      // Toggle Decision
      $('.btn-decision-choice').on('click', function() {
        self.state.auditDecision = $(this).data('decision');
        $('.btn-decision-choice').removeClass('active');
        $(this).addClass('active');
      });

      // Save Decision
      $('#btn-save-audit-decision').on('click', function() {
        if (!self.state.currentAuditModal) return;

        var audit = self.state.currentAuditModal;
        var decision = self.state.auditDecision;
        var feedback = $('#audit-modal-feedback-input').val();

        audit.status = decision;
        audit.mentorNotes = feedback;

        $.ajax({
          url: MentorDashboardData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_mentor_save_audit',
            nonce: MentorDashboardData.nonce,
            audit_id: audit.id,
            decision: decision,
            feedback: feedback
          }
        });

        self.showToast('✅ Dictamen para ' + audit.studentName + ' calificado como: ' + decision.toUpperCase());
        $('#modal-audit').fadeOut(150);
        self.renderAll();
      });
    },

    bindWithdrawal: function() {
      var self = this;

      $('#btn-open-withdraw-modal').on('click', function() {
        var user = self.getActiveUser();
        $('#modal-w-beneficiary').text(user.name);
        $('#modal-w-balance').text(user.withdrawableBalance.toLocaleString('es-ES') + ' €');
        $('#input-withdraw-amount').val(1000).attr('max', user.withdrawableBalance);
        $('#input-withdraw-iban').val(user.payoutIban);
        $('#modal-withdraw').fadeIn(150);
      });

      $('#form-withdraw-funds').on('submit', function(e) {
        e.preventDefault();
        var user = self.getActiveUser();
        var amount = parseFloat($('#input-withdraw-amount').val());
        var iban = $('#input-withdraw-iban').val();
        var taxId = $('#input-withdraw-taxid').val();
        var notes = $('#input-withdraw-notes').val();

        if (amount <= 0 || amount > user.withdrawableBalance) {
          self.showToast('El monto solicitado no es válido o supera tu saldo.');
          return;
        }

        var newPayout = {
          id: 'pay-' + Date.now(),
          date: new Date().toISOString().split('T')[0],
          amount: amount,
          iban: iban,
          recipient: user.name,
          status: 'completed',
          receiptId: 'TEC-REC-' + Math.floor(1000 + Math.random() * 9000)
        };

        user.withdrawableBalance -= amount;
        user.totalPaidOut += amount;
        self.state.payoutHistory.unshift(newPayout);

        $.ajax({
          url: MentorDashboardData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_mentor_request_withdrawal',
            nonce: MentorDashboardData.nonce,
            amount: amount,
            iban: iban,
            tax_id: taxId,
            notes: notes
          }
        });

        $('#modal-withdraw').fadeOut(150);
        self.showToast('💸 Transferencia SEPA de ' + amount.toLocaleString('es-ES') + ' € solicitada');
        self.renderAll();
      });
    },

    bindCMSActions: function() {
      var self = this;

      $(document).on('click', '.btn-edit-cms-course', function() {
        var cId = $(this).data('course-id');
        var cName = $(this).data('course-name');

        $('#cms-modal-title').text('Editar Lección & Vídeo: ' + cName);
        $('#cms-lesson-id').val(1);
        $('#cms-lesson-title').val('Bienvenida y Manifiesto de la Identidad');
        $('#cms-lesson-duration').val('12 min');
        $('#cms-lesson-video-title').val('Vídeo Principal: Masterclass Identidad');
        $('#cms-lesson-video-url').val('https://youtu.be/MeKlBPHgmJ0');
        $('#cms-lesson-what-learn').val('Comprender la transición de la mente reactiva al liderazgo consciente.');
        $('#cms-lesson-utility').val('Tomar decisiones de alto valor con claridad estratégica.');
        $('#cms-lesson-reading').val('La mente reactiva opera bajo el sistema de amenaza biológico. Estructurar procesos previene el agotamiento.');

        $('#modal-lesson-cms').fadeIn(150);
      });

      $(document).on('click', '.btn-cms-resources', function() {
        var cName = $(this).data('course-name');
        self.showToast('Abriendo gestor de recursos y entregables de: ' + cName);
      });

      $('#form-edit-lesson-cms').on('submit', function(e) {
        e.preventDefault();
        var lessonId = $('#cms-lesson-id').val();
        var title = $('#cms-lesson-title').val();
        var videoTitle = $('#cms-lesson-video-title').val();
        var videoUrl = $('#cms-lesson-video-url').val();
        var duration = $('#cms-lesson-duration').val();
        var whatLearn = $('#cms-lesson-what-learn').val();
        var utility = $('#cms-lesson-utility').val();
        var reading = $('#cms-lesson-reading').val();

        $.ajax({
          url: MentorDashboardData.ajaxUrl,
          type: 'POST',
          data: {
            action: 'academia_mentor_update_lesson_cms',
            nonce: MentorDashboardData.nonce,
            lesson_id: lessonId,
            title: title,
            video_title: videoTitle,
            video_url: videoUrl,
            duration: duration,
            what_you_will_learn: whatLearn,
            business_utility: utility,
            reading_text: reading
          },
          success: function() {
            self.showToast('✅ Cambios en la lección guardados en tiempo real');
            $('#modal-lesson-cms').fadeOut(150);
          }
        });
      });
    }
  };

  $(document).ready(function() {
    AcademiaMentorApp.init();
  });
})(jQuery);
