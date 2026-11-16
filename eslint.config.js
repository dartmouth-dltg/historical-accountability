import js from '@eslint/js';
import globals from 'globals';

export default [
	js.configs.recommended,
	{
		files: ['source/js/**/*.js'],
		languageOptions: {
			ecmaVersion: 2021,
			sourceType: 'script',
			globals: {
				...globals.browser,
				// Third-party libraries loaded as global scripts (see vendor_scripts in gulpfile.js)
				jQuery: 'readonly',
				$: 'readonly',
				inView: 'readonly',
				// Shared state declared in source/js/00_*.js and default.js, relied on by
				// later files once Gulp concatenates everything into one bundle.
				heartbeat: 'writable',
				themepath: 'writable',
				assetpath: 'writable',
				imagepath: 'writable',
				breakpoint_xsml: 'writable',
				breakpoint_sml: 'writable',
				breakpoint_med: 'writable',
				breakpoint_lrg: 'writable',
				breakpoint_xlrg: 'writable',
				breakpoint_xxlrg: 'writable',
				breakpoint_stack: 'writable',
				breakpoint_tablet: 'writable',
				breakpoint_desktop: 'writable',
				breakpoint_ultrawide: 'writable'
			}
		},
		rules: {
			'no-unused-vars': 'warn',
			'no-console': 'off'
		}
	},
	{
		// These files declare the shared globals above for the rest of the concatenated
		// bundle to use - that's an intentional redeclaration, not a mistake.
		files: ['source/js/00_globalBreakpoints.js', 'source/js/default.js'],
		rules: {
			'no-redeclare': 'off'
		}
	},
	{
		// Vendored plugin using the classic `function(window, undefined)` guard idiom.
		files: ['source/js/debounce.js'],
		rules: {
			'no-shadow-restricted-names': 'off'
		}
	}
];
