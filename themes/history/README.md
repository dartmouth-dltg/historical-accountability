So little to read! Everything here is a work in progress. 

See source/sass/a_components/usage.md for some information about the SASS build system

#NOTE
This is more of a starter kit than a standalone package. While the common files are tracked in Agile’s repo ( https://agile.git.beanstalkapp.com/agile-theme-builder-v002.git ), each new instance should be in its own repo, as many of the standard files (like the gulpfile, local SASS theme, etc.) will change.

Thus, once the starter repo is downloaded you should remove the .git folder and roll it into your project repo. Soon some of the core components will be available as Git submodules, but that day is not yet here. So don't touch source/sass/a_components if you can at all avoid it. If you can't avoid it, report any issues with core components so that they may be rolled into the starter kit.


#REQUIREMENTS
+ node.js 10+
+ npm 6+
+ gulp 4 (installed globally, though package is included)

#INSTALLATION
Remove .git folder to begin a new repo.
npm install

#USAGE
+ gulp build:  Builds entire asset folder (sass, scripts, etc.)
+ gulp watch:  Watches for changes in source directory and builds automatically
+ gulp js: Builds scripts only
+ gulp sass: Builds SASS files only