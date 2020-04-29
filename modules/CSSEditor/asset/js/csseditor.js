(function($) {
    $(document).ready(function() {
        $('.add-value').click(function() {
            var template = $('.external-css-value').last().clone();
            var templateInput = template.find('input');
            var fieldCount = templateInput.data('field-count') + 1;
            templateInput.attr('name', 'external-css[' + fieldCount + ']');
            templateInput.attr('data-field-count', fieldCount);
            templateInput.val('');
            $('.external-css .values').append(template);
        });
        
        $(document).on('click', '.remove-value', function(e) {
            e.preventDefault();
            if ($('.external-css-value').length > 1) {
                $(this).parents('.external-css-value').remove();
            }
        });
    });
})(jQuery)