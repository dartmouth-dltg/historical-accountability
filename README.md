# Dartmouth Historical Accountability Project Omeka S Theme

This is an Omeka S theme developed by The Agile Humanities Agency on behalf of the Dartmouth Historical Accountability Project.

#NOTE
This theme includes build files (final CSS, Javascript, and Image files) for portability. Any changes to the theme should be done through the source files, which requires node.js, npm, and Gulp be installed.

#REQUIREMENTS FOR THEME USERS
* Omeka Modules:
  * Agile Theme Tools
  * Alt Text ( https://github.com/zerocrates/AltText )
  
#USAGE FOR THEME USERS
* Create a folder in Omeka’s ./themes directory named “dhap”.
* Clone the theme into this directory
* Activate the theme via the Omeka Admin UI

#REQUIREMENTS FOR THEME DEVELOPERS
* node.js 10+
* npm 6+
* gulp 4 (installed globally, though package is included)

#INSTALLATION FOR THEME DEVELOPERS
* Run npm install to load vendor packages

#USAGE FOR THEME DEVELOPERS
* gulp build:  Builds entire asset folder (sass, scripts, etc.)
* gulp watch:  Watches for changes in source directory and builds automatically
* gulp js: Builds scripts only
* gulp sass: Builds SASS files only