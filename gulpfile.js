/**
 * Settings
 * Turn on/off build features
 */

const settings = {
	clean: true,
	scripts: true,
	modernizr: true,
	styles: true,
	img: false, // use if images are not separated into subdirectories by file type. otherwise use below.
	svgs: true,
	pngs: true,
	jpgs: true,
	fonts: true,
	copy: true,
	reload: false
};


/**
 * Paths to project folders
 */

const source_dir = './source';
const build_dir = './asset';

const paths = {
	input: source_dir,
	output: build_dir, // TSRA is built in the root folder.
	scripts: {
		input: source_dir + '/js',
		output: build_dir + '/js/',
		cfilename: 'default', // Output file name for concatenated scripts. Set to false to use output folder name
		vfilename: 'vendor' // Output file name for vendor scripts
	},
	styles: {
		input: source_dir + '/sass/**/*.{scss,sass}',
		output: build_dir + '/css',
		vfilename: 'vendor', // Output file name for vendor styles
		sassIncludePaths: [source_dir + '/sass', source_dir + '/sass/a_components', source_dir + '/sass/b_profiles', source_dir + '/sass/c_local', source_dir + '/sass/a_components/00_general', source_dir + '/sass/a_components/10_layout', source_dir + '/sass/a_components/20_colour', source_dir + '/sass/a_components/30_typography', source_dir + '/sass/a_components/40_ui', source_dir + '/sass/a_components/50_animation', source_dir + '/sass/a_components/60_site_elements', 'node_modules']
	},
	img: {
		base: source_dir + '/img',
		input: source_dir + '/img/**/*',
		output: build_dir + '/img'
	},
	svgs: {
		base: source_dir + '/img/svg',
		input: source_dir + '/img/svg/**/*.svg',
		output: build_dir + '/img/svg/'
	},
	pngs: {
		base: source_dir + '/img/png',
		input: source_dir + '/img/png/**/*.png',
		output: build_dir + '/img/png/'
	},
	jpgs: {
		base: source_dir + '/img/jpg',
		input: source_dir + '/img/jpg/**/*.jpg',
		output: build_dir + '/img/jpg/'
	},
	fonts: {
		base: source_dir + '/fonts',
		input: source_dir + '/fonts/**/*',
		output: build_dir + '/fonts'
	},
	copy: {
		base: source_dir + '/copy-js',
		input: source_dir + '/copy-js/**/*',
		output: build_dir + '/js'
	},
	reload: './'
};


/**
 * Copy third-party scripts and styles.
 */

const vendor_scripts = ['node_modules/jquery-reflow-table/dist/js/reflow-table.js', 'node_modules/ev-emitter/ev-emitter.js', 'node_modules/imagesloaded/imagesloaded.pkgd.js'];
const vendor_styles = ['node_modules/jquery-reflow-table/dist/css/reflow-table.css'];


/**
 * Gulp Packages
 */

// General
import { src, dest, watch, series, parallel } from 'gulp';
import { readFileSync, rmSync, existsSync } from 'node:fs';
import flatmap from 'gulp-flatmap';
import lazypipe from 'lazypipe';
import rename from 'gulp-rename';
import header from 'gulp-header';

const pkg = JSON.parse(readFileSync('./package.json', 'utf8'));

// Scripts
import eslint from 'gulp-eslint-new';
import concat from 'gulp-concat';
import uglify from 'gulp-terser';
import modernizr from 'gulp-modernizr';

const modernizrConfig = JSON.parse(readFileSync('./modernizr-config.json', 'utf8'));

// Styles
import gulpSass from 'gulp-sass';
import * as dartSass from 'sass';
import sourcemaps from 'gulp-sourcemaps';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import postcsscomments from 'postcss-discard-comments';
import cssnano from 'cssnano';

const sass = gulpSass(dartSass);

// SVGs
import svgmin from 'gulp-svgmin';

// BrowserSync
import browserSync from 'browser-sync';

/**
 *  Define CSS Plugins
 */

const cssPlugins = [
	autoprefixer({ cascade: true, remove: true }),
	postcsscomments({ removeAll: true }),
	cssnano()
];


/**
 * Template for banner to add to file headers
 */

const banner = {
	full:
		'/*!\n' +
		' * <%= package.name %> v<%= package.version %>\n' +
		' * <%= package.description %>\n' +
		' * (c) ' + new Date().getFullYear() + ' <%= package.author.name %>\n' +
		' * <%= package.license %> License\n' +
		' * <%= package.repository.url %>\n' +
		' */\n\n',
	min:
		'/*!' +
		' <%= package.name %> v<%= package.version %>' +
		' | (c) ' + new Date().getFullYear() + ' <%= package.author.name %>' +
		' | <%= package.license %> License' +
		' | <%= package.repository.url %>' +
		' */\n'
};


/**
 * Gulp Tasks
 */

// Remove pre-existing content from output folder
const cleanDist = (done) => {

	// Make sure this feature is activated before running
	if (!settings.clean) return done();

	// Clean the build folder
	rmSync(paths.output, { recursive: true, force: true });

	// Signal completion
	return done();
};

// Repeated JavaScript tasks
const jsTasks = lazypipe()
	.pipe(header, banner.full, { package: pkg })
	.pipe(dest, paths.scripts.output)
	.pipe(rename, { suffix: '.min' })
	.pipe(uglify)
	.pipe(header, banner.min, { package: pkg })
	.pipe(dest, paths.scripts.output);

// Lint, minify, and concatenate scripts
const buildScripts = (done) => {

	// Make sure this feature is activated before running
	if (!settings.scripts) return done();

	// Run tasks on script files
	return src(paths.scripts.input)
		.pipe(flatmap((stream, file) => {

			// If the file is a directory
			if (file.isDirectory()) {

				// Setup a filename.
				const filename = paths.scripts.cfilename === false ? file.relative : paths.scripts.cfilename;

				// Grab all files and concatenate them
				return src(file.path + '/*.js')
					.pipe(concat(filename + '.js'))
					.pipe(jsTasks());
			}

			// Otherwise, process the file
			return stream.pipe(jsTasks());

		}));

};

const buildVendorScripts = (done) => {

	if (!settings.scripts) return done();
	return src(vendor_scripts)
		.pipe(flatmap((stream, file) => {

			// Setup a filename.
			const filename = paths.scripts.vfilename === false ? file.relative : paths.scripts.vfilename;

			// Grab all files and concatenate them
			return src(file.path)
				.pipe(concat(filename + '.js'))
				.pipe(jsTasks());

		}));
};

// Lint scripts
const lintScripts = (done) => {

	// Make sure this feature is activated before running
	if (!settings.scripts) return done();

	// Lint scripts
	return src(paths.scripts.input + '/*.js')
		.pipe(eslint())
		.pipe(eslint.format());
};

const buildModernizr = (done) => {

	if (!settings.modernizr) return done();

	return src(paths.scripts.input + '/*.js')
		.pipe(modernizr(modernizrConfig))
		.pipe(dest(paths.scripts.output))
		.pipe(rename({ suffix: '.min' }))
		.pipe(uglify())
		.pipe(dest(paths.scripts.output));
};

// Process, lint, and minify Sass files
const buildStyles = (done) => {

	// Make sure this feature is activated before running
	if (!settings.styles) return done();

	// Run tasks on all Sass files
	return src(paths.styles.input)
		.pipe(sourcemaps.init())
		.pipe(sass({
			verbose: true,
			style: 'expanded',
			loadPaths: paths.styles.sassIncludePaths // Allows @import declarations deeper in the tree to target top-level directories. Useful for loading in components and profiles.
		}))
		.pipe(header(banner.full, { package: pkg }))
		.pipe(dest(paths.styles.output))
		.pipe(rename({ suffix: '.min' }))
		.pipe(postcss(cssPlugins))
		.pipe(header(banner.min, { package: pkg }))
		.pipe(sourcemaps.write('./'))
		.pipe(dest(paths.styles.output));

};

const buildVendorStyles = (done) => {

	if (!settings.styles) return done();
	return src(vendor_styles)
		.pipe(flatmap((stream, file) => {

			// Setup a filename.
			const filename = paths.styles.vfilename === false ? file.relative : paths.styles.vfilename;

			// Grab all files and concatenate them
			return src(file.path)
				.pipe(header(banner.full, { package: pkg }))
				.pipe(rename({ basename: filename }))
				.pipe(dest(paths.styles.output))
				.pipe(rename({ basename: filename, suffix: '.min' }))
				.pipe(postcss(cssPlugins))
				.pipe(header(banner.min, { package: pkg }))
				.pipe(dest(paths.styles.output));
		}));
};


// Copy Image files
const buildImages = (done) => {

	// Make sure this feature is activated before running, and the source folder exists
	if (!settings.img || !existsSync(paths.img.base)) return done();

	return src(paths.img.input, { allowEmpty: true, encoding: false })
		.pipe(dest(paths.img.output));
};


// Optimize SVG files
const buildSVGs = (done) => {

	// Make sure this feature is activated before running, and the source folder exists
	if (!settings.svgs || !existsSync(paths.svgs.base)) return done();

	return src(paths.svgs.input, { allowEmpty: true, encoding: false })
		.pipe(svgmin())
		.pipe(dest(paths.svgs.output));

};

// Copy PNG files
const buildPNGs = (done) => {

	// Make sure this feature is activated before running, and the source folder exists
	if (!settings.pngs || !existsSync(paths.pngs.base)) return done();

	return src(paths.pngs.input, { allowEmpty: true, encoding: false })
		.pipe(dest(paths.pngs.output));
};

// Copy JPG files
const buildJPGs = (done) => {

	// Make sure this feature is activated before running, and the source folder exists
	if (!settings.jpgs || !existsSync(paths.jpgs.base)) return done();

	return src(paths.jpgs.input, { allowEmpty: true, encoding: false })
		.pipe(dest(paths.jpgs.output));
};

// Copy Font Files
const buildFonts = (done) => {

	// Make sure this feature is activated before running, and the source folder exists
	if (!settings.fonts || !existsSync(paths.fonts.base)) return done();

	return src(paths.fonts.input, { allowEmpty: true, encoding: false })
		.pipe(dest(paths.fonts.output));
};


// Copy theme-specific static files into output folder
const copyFiles = (done) => {

	// Make sure this feature is activated before running, and the source folder exists
	if (!settings.copy || !existsSync(paths.copy.base)) return done();

	return src(paths.copy.input, { allowEmpty: true, encoding: false })
		.pipe(dest(paths.copy.output));

};

// Watch for changes to the source directory
const startServer = (done) => {

	// Make sure this feature is activated before running
	if (!settings.reload) return done();

	// Initialize BrowserSync
	browserSync.init({
		server: {
			baseDir: paths.reload
		}
	});

	// Signal completion
	done();

};

// Reload the browser when files change
const reloadBrowser = (done) => {
	if (!settings.reload) return done();
	browserSync.reload();
	done();
};


/**
 * Composed Tasks
 */

// Default task: gulp / gulp build
const build = series(
	cleanDist,
	parallel(
		lintScripts,
		buildScripts,
		buildVendorScripts,
		buildStyles,
		buildVendorStyles,
		buildImages,
		buildSVGs,
		buildPNGs,
		buildJPGs,
		buildFonts,
		copyFiles
	),
	buildModernizr
);

// gulp js
const buildJs = series(
	lintScripts,
	buildScripts,
	buildVendorScripts,
	buildModernizr
);

// gulp sass
const buildSass = series(
	buildStyles
);

// gulp images
const buildImagesTask = series(
	buildImages,
	buildSVGs,
	buildPNGs,
	buildJPGs
);

// Watch for changes
const watchSrc = (done) => {
	watch(paths.input, series(build, reloadBrowser));
	done();
};

// Watch for changes to scripts only
const watchJs = (done) => {
	watch(paths.input, series(buildJs, reloadBrowser));
	done();
};

// Watch for changes to styles only
const watchSass = (done) => {
	watch(paths.input, series(buildSass, reloadBrowser));
	done();
};

// gulp watch
const watchAll = series(
	build,
	startServer,
	watchSrc
);

// gulp jswatch
const jswatch = series(
	buildJs,
	startServer,
	watchJs
);

// gulp sasswatch
const sasswatch = series(
	buildSass,
	startServer,
	watchSass
);


/**
 * Export Tasks
 */

export {
	build,
	build as default,
	buildJs as js,
	buildSass as sass,
	buildImagesTask as images,
	watchAll as watch,
	jswatch,
	sasswatch
};
