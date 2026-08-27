/**
 * Academia Admin JS
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Open review modal
        $('.btn-open-review').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('id');
            var student = $btn.data('student');
            var deliverable = $btn.data('deliverable');
            var url = $btn.data('url');
            var status = $btn.data('status');
            var feedback = $btn.data('feedback');

            $('#modal-deliverable-id').val(id);
            $('#modal-student-name').text(student);
            $('#modal-deliverable-title').text(deliverable);
            $('#modal-submission-link').attr('href', url);
            
            if (status === 'approved' || status === 'needs_changes') {
                $('#modal-status').val(status);
            } else {
                $('#modal-status').val('approved');
            }

            $('#modal-feedback').val(feedback || '');

            $('#academia-review-modal').fadeIn(200);
        });

        // Close modal
        $('.academia-modal-close').on('click', function() {
            $('#academia-review-modal').fadeOut(200);
        });

        // Submit review form via AJAX
        $('#academia-review-form').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('#btn-save-review');
            $btn.prop('disabled', true).text('Guardando...');

            var deliverableId = $('#modal-deliverable-id').val();
            var status = $('#modal-status').val();
            var feedback = $('#modal-feedback').val();

            $.ajax({
                url: AcademiaAdminData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'academia_review_deliverable',
                    nonce: AcademiaAdminData.nonce,
                    deliverable_id: deliverableId,
                    status: status,
                    feedback: feedback
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Guardar Evaluación');
                    if (response.success) {
                        alert(response.data.message);
                        $('#academia-review-modal').fadeOut(200);
                        location.reload();
                    } else {
                        alert(response.data.message || 'Ocurrió un error');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Guardar Evaluación');
                    alert('Error en el servidor al guardar la evaluación.');
                }
            });
        });
    });
})(jQuery);
