/**
 * Global Breakpoints
 * Sets global values for breakpoints based on CSS root variables provided by the layout_css_variables SASS component. Must come early in the load order.
 */

var breakpoint_xsml, breakpoint_sml, breakpoint_med, breakpoint_lrg, breakpoint_xlrg, breakpoint_xxlrg, breakpoint_stack, breakpoint_tablet, breakpoint_desktop, breakpoint_ultrawide;

(function($) {    
  $(document).ready(function() {
    const style = getComputedStyle(document.body);
    breakpoint_xsml = style.getPropertyValue('--breakpoint-xsml')
    breakpoint_sml = style.getPropertyValue('--breakpoint-sml')
    breakpoint_med = style.getPropertyValue('--breakpoint-med')
    breakpoint_lrg = style.getPropertyValue('--breakpoint-lrg')
    breakpoint_xlrg = style.getPropertyValue('--breakpoint-xlrg')
    breakpoint_xxlrg = style.getPropertyValue('--breakpoint-xxlrg')
    breakpoint_stack = style.getPropertyValue('--breakpoint-stack')
    breakpoint_tablet = style.getPropertyValue('--breakpoint-tablet')
    breakpoint_desktop = style.getPropertyValue('--breakpoint-desktop')
    breakpoint_ultrawide = style.getPropertyValue('--breakpoint-ultrawide')
  });  
})(jQuery);
