/**
 * Visibility Classes
 * Uses the in-view.js library ( https://www.npmjs.com/package/in-view ) add visibility
 * classes to DOM objects.
 */

jQuery(document).ready(function() {
    
  const style = getComputedStyle(document.body);
  const propcount = style.getPropertyValue('--visibility-propcount'); // Lets JS know how many properties to expect.
  
  if (typeof propcount != 'undefined' && parseInt(propcount) > 0) {
    var visibilityStack = [];
    
    const visibilityOffset = style.getPropertyValue('--visibility-offset');
    const visibilityThreshold = style.getPropertyValue('--visibility-threshold');
    
    inView.threshold(visibilityThreshold);
    inView.offset(visibilityOffset);
    
    for(var i=1;i<=parseInt(propcount);i++) {
      visibilityStack.push(style.getPropertyValue("--visibility-element-" + i));
    }
                  
    $(visibilityStack).each(function(i,selector){    
      if($(selector).length > 0) {
        inView(selector)
          .on('enter', elem => {
            $(elem).addClass('visible');
          })
          .on('exit',elem => {
            $(elem).removeClass('visible');
          });
      }
    });
    
  }
});