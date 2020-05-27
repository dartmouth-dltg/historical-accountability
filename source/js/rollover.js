/**
 *  @file rollover.js
 *
 *  Handles rollovers in two ways – by file naming convention or by the presence of a
 *  named data attribute on the image.
 *
 *  Method 1: File Naming
 *  Label the neutral state of the image file by appending “_up”, and provide a corresponding
 *  active state image file appended with “_over” in the same folder.
 *
 *  E.g. /path/to/myimage_up.png --> /path/to/myimage_over.png
 *
 *  Method 2: Named attribute
 *  Provide a "data-src-active" attribute in the image tag with a full path to the active
 *  state image.
 *
 *  E.g. <img src='/path/to/myimage.png' data-src-active='/path/to/myactiveimage.png' ... />
 *
 */

var cache = []; // preloader

jQuery(document).ready(function() {
	jQuery('img').each(function() {
		rollover_bind(this);
	});
});


function rollover_bind(e) {
	var states = ['_up','_down'];		
	for (i=0;i<states.length;i++) { // bind all listed states
		var src = jQuery(e).attr('src');
		if (src != null) {
			jQuery(e).on('mouseover touchstart',function() {  
				var replace ='';
				if (typeof jQuery(this).attr('data-src-active') != 'undefined') {
  				replace = jQuery(this).attr('data-src-active');
  				jQuery(this).attr('data-src',src);
  				jQuery(this).attr('src',replace);
        } else if (src.indexOf(states[i],0) > 0) {
					replace = src.replace('_up','_over');
					jQuery(this).attr('src',replace);
        }
        if (replace != '') {
  				var cacheimage = document.createElement('img'); // preload
  				cacheimage.src = replace;
  				cache.push(cacheimage);
				}
			});
			jQuery(e).on('mouseout touchend',function() {
				var replace ='';
				if (typeof jQuery(this).attr('data-src') != 'undefined') {
  				replace = jQuery(this).attr('data-src');
  				jQuery(this).attr('src',replace);
        } else if (src.indexOf(states[i],0) > 0) {
					replace = src.replace('_over','_up');
					jQuery(this).attr('src',replace);
				}
			});
		}
	}
}