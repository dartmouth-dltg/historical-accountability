#AGILE DESIGN SYSTEM: Animate Visibility component
InView is a third-party script https://github.com/camwiegert/in-view that runs JS functions when a DOM object is in the viewport.
An accompanying custom script, visibilityClasses.js adds a ".visible" class via InView, allowing stylesheets to react accordingly.

This implementation can be configured entirely in SASS. Relevant values are built by the \_set\_visiblity\_stack() mixin as CSS
variables and picked up by Javascript accordingly. 

##Usage
- Add the inView library as dependency in package.json ( https://github.com/camwiegert/in-view )
- Copy visibilityClasses.js to the local js folder.
- Set the component SASS variables:
  - $visibility_stack:  A SASS list containing the DOM elements to be tracked.
  - $visibility_offset: The distance in pixels a DOM element must be in the viewport before being considered visible. This allows
    for fade-in effects, etc. See inView documentation for more.
  - $threshold: The ratio of height to width a DOM object must have before it's tracked. See inView documentation for more.
  - $visibility\_stack\_animation\_speed: A relative duration integer denoting the length of the fade-in. Set to 0 for heartbeat value, null for instant appearance.
  - $visibility\_stack\_animation\_delay: A relative duration integer denoting the delay before fading in. Set to null for no delay.
- Additional objects may be set for animation via the \_set\_visibility\_animation($speed,$delay,$easing,$properties). 
  - $easing is set to $standard\_easing (an core animation component config).
  - $properties defaults to 'opacity'. Set if the object requires other properties be assigned this transition value.