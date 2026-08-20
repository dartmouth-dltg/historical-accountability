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
* node.js 22.11+ (see .nvmrc; run `nvm use` if you use nvm)
* npm 10+
* gulp 5 (installed locally via npm install; no global install needed)

#INSTALLATION FOR THEME DEVELOPERS
* Run npm install to load vendor packages

#USAGE FOR THEME DEVELOPERS
Use the npm scripts below rather than a globally-installed `gulp` command - this
avoids "Unsupported gulp version" errors caused by an outdated global `gulp-cli`.
* npm run build:  Builds entire asset folder (sass, scripts, etc.)
* npm run watch:  Watches for changes in source directory and builds automatically
* npm run js: Builds scripts only, and lints them (see eslint.config.js)
* npm run sass: Builds SASS files only
* npm run images: Builds image assets only (SVG/PNG/JPG)

If you'd rather use the bare `gulp` command, update your global CLI first:
`npm install --global gulp-cli@latest`.