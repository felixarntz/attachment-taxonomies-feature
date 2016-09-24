/* ---- THE FOLLOWING CONFIG SHOULD BE EDITED ---- */

var pkg = require( './package.json' );

function parseKeywords( keywords ) {
	// These keywords are useful for Packagist/NPM/Bower, but not for the WordPress plugin repository.
	var disallowed = [ 'wordpress', 'plugin' ];

	k = keywords;
	for ( var i in disallowed ) {
		var index = k.indexOf( disallowed[ i ] );
		if ( -1 < index ) {
			k.splice( index, 1 );
		}
	}

	return k;
}

var keywords = parseKeywords( pkg.keywords );

var config = {
	pluginSlug: pkg.name,
	pluginName: 'Attachment Taxonomies (Feature Plugin)',
	pluginURI: pkg.homepage,
	author: pkg.author.name,
	authorURI: pkg.author.url,
	description: pkg.description,
	version: pkg.version,
	license: pkg.license.name,
	licenseURI: pkg.license.url,
	tags: keywords.join( ', ' ),
	contributors: [ 'flixos90' ].join( ', ' ),
	minRequired: '4.6',
	testedUpTo: '4.6'
};

/* ---- DO NOT EDIT BELOW THIS LINE ---- */

// WP plugin header for main plugin file
var pluginheader = 	'Plugin Name: ' + config.pluginName + '\n' +
					'Plugin URI:  ' + config.pluginURI + '\n' +
					'Description: ' + config.description + '\n' +
					'Version:     ' + config.version + '\n' +
					'Author:      ' + config.author + '\n' +
					'Author URI:  ' + config.authorURI + '\n' +
					'License:     ' + config.license + '\n' +
					'License URI: ' + config.licenseURI + '\n' +
					'Tags:        ' + config.tags;

// WP plugin header for readme.txt
var readmeheader =	'Plugin Name:       ' + config.pluginName + '\n' +
					'Plugin URI:        ' + config.pluginURI + '\n' +
					'Author:            ' + config.author + '\n' +
					'Author URI:        ' + config.authorURI + '\n' +
					'Contributors:      ' + config.contributors + '\n' +
					'Requires at least: ' + config.minRequired + '\n' +
					'Tested up to:      ' + config.testedUpTo + '\n' +
					'Stable tag:        ' + config.version + '\n' +
					'Version:           ' + config.version + '\n' +
					'License:           ' + config.license + '\n' +
					'License URI:       ' + config.licenseURI + '\n' +
					'Tags:              ' + config.tags;


/* ---- REQUIRED DEPENDENCIES ---- */

var gulp = require( 'gulp' );

var sass = require( 'gulp-sass' );
var csscomb = require( 'gulp-csscomb' );
var minifyCSS = require( 'gulp-minify-css' );
var jshint = require( 'gulp-jshint' );
var uglify = require( 'gulp-uglify' );
var rename = require( 'gulp-rename' );
var replace = require( 'gulp-replace' );

var paths = {
	php: {
		src: [ './*.php', './src/**/*.php' ]
	},
	sass: {
		src: [ './src/wp-includes/css/*.scss' ],
		dst: './src/wp-includes/css/'
	},
	js: {
		src: [ './src/wp-includes/js/*.js' ],
		dst: './src/wp-includes/js/'
	}
};

/* ---- MAIN TASKS ---- */

// general task (compile Sass and JavaScript)
gulp.task( 'default', [ 'sass', 'js' ]);

// build the plugin
gulp.task( 'build', [ 'readme-replace' ], function() {
	gulp.start( 'header-replace' );
	gulp.start( 'default' );
});

/* ---- SUB TASKS ---- */

// compile Sass
gulp.task( 'sass', function( done ) {
	gulp.src( paths.sass.src )
		.pipe( sass({
			errLogToConsole: true
		}) )
		.pipe( csscomb() )
		.pipe( gulp.dest( paths.sass.dst ) )
		.pipe( minifyCSS({
			keepSpecialComments: 0
		}) )
		.pipe( rename({
			extname: '.min.css'
		}) )
		.pipe( gulp.dest( paths.sass.dst ) )
		.on( 'end', done );
});

// compile JavaScript
gulp.task( 'js', function( done ) {
	gulp.src( paths.js.src )
		.pipe( jshint({
			lookup: true
		}) )
		.pipe( gulp.dest( paths.js.dst ) )
		.pipe( uglify() )
		.pipe( rename({
			extname: '.min.js'
		}) )
		.pipe( gulp.dest( paths.js.dst ) )
		.on( 'end', done );
});

// replace the plugin header in the main plugin file
gulp.task( 'header-replace', function( done ) {
	gulp.src( './' + config.pluginSlug + '.php' )
		.pipe( replace( /((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/, '/*\n' + pluginheader + '\n*/' ) )
		.pipe( gulp.dest( './' ) )
		.on( 'end', done );
});

// replace the plugin header in readme.txt
gulp.task( 'readme-replace', function( done ) {
	gulp.src( './readme.txt' )
		.pipe( replace( /\=\=\= (.+) \=\=\=([\s\S]+)\=\= Description \=\=/m, '=== ' + config.pluginName + ' ===\n\n' + readmeheader + '\n\n' + config.description + '\n\n== Description ==' ) )
		.pipe( gulp.dest( './' ) )
		.on( 'end', done );
});
