(function($) {
    $(document).ready(function() {
        $('main > div > .region').each(function() {
            var block = $(this);
            // Check to see if target id attribute and matching DOM object exists.
            if (typeof block.attr('data-target-region-id') !== "undefined" && $(block.attr('data-target-region-id')).length > 0) {
                var target = block.attr('data-target-region-id');
                $(target).show();
                block.removeAttr('data-target-region-id'); // Remove target id attribute
                block.appendTo(target); // Move block to target id.
                block.parent().show(); // block hidden by default to prevent any styling from bleeding through
            }
        });
    });
})(jQuery);
