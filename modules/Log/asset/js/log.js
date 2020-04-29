$(document).ready(function() {

    /**
     * Search sidebar.
     */
    $('#content').on('click', 'a.search', function(e) {
        e.preventDefault();
        var sidebar = $('#sidebar-search');
        Omeka.openSidebar(sidebar);

        // Auto-close if other sidebar opened
        $('body').one('o:sidebar-opened', '.sidebar', function () {
            if (!sidebar.is(this)) {
                Omeka.closeSidebar(sidebar);
            }
        });
    });

    /**
     * Better display of big logs.
     */
    $('a.popover').webuiPopover('destroy').webuiPopover({
        placement: 'auto-bottom',
        content: function (element) {
            var target = $('[data-target=' + element.id + ']');
            var content = target.closest('.webui-popover-parent').find('.webui-popover-current');
            $(content).removeClass('truncate').show();
            return content;
        },
        title: '',
        arrow: false,
        backdrop: true,
        onShow: function(element) { element.css({left: 0}); }
    });

    $('a.popover').webuiPopover();

    // Complete the batch delete form after confirmation.
    // TODO Check if this is still needed.
    $('#confirm-delete-selected, #confirm-delete-all').on('submit', function(e) {
        var confirmForm = $(this);
        if ('confirm-delete-all' === this.id) {
            confirmForm.append($('.batch-query').clone());
        } else {
            $('#batch-form').find('input[name="resource_ids[]"]:checked:not(:disabled)').each(function() {
                confirmForm.append($(this).clone().prop('disabled', false).attr('type', 'hidden'));
            });
        }
    });
    $('.delete-all').on('click', function(e) {
        Omeka.closeSidebar($('#sidebar-delete-selected'));
    });
    $('.delete-selected').on('click', function(e) {
        Omeka.closeSidebar($('#sidebar-delete-all'));
        var inputs = $('input[name="resource_ids[]"]');
        $('#delete-selected-count').text(inputs.filter(':checked').length);
    });
    $('#sidebar-delete-all').on('click', 'input[name="confirm-delete-all-check"]', function(e) {
        $('#confirm-delete-all input[type="submit"]').prop('disabled', this.checked ? false : true);
    });

});
