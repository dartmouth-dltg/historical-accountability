#Usage: Colour UI System

The UI system provides a standard set of configuration values alongside base attribute definitions that use these values. 

Other components and themes should assign the values to core elements, e.g.

button, .btn
  @extend %btn
  
input[type='submit']
  @extend %btn-submit
  
  
##Warning
The UI config contains colour definitions established in 20\_colour/00]_core. If you override these colours locally you'll also need to copy the UI config file, as it will need to redefine UI elements based on local changes.