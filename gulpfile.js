'use strict';

/**
 * Récupération d'un paramètre en ligne de commande
 * @param key
 * @return bool|string
 */
function getArg(key) {
	var index = process.argv.indexOf(key),
		next = process.argv[index + 1]
	;
	return (index == -1) ? null : (! next || next[0] === "-") ? true : next;
};

 
var gulp = require('gulp'),
	sass = require('gulp-sass'),
	sourcemaps = require('gulp-sourcemaps'),
	environment = (getArg('--env') || 'development'),
	environmentsAllowed = [ 'development', 'production', ],
	withSourceMaps = (environment == 'development'),
	compressFiles = (environment != 'development')
;

// Vérification  de l'environment
if(environmentsAllowed.indexOf(environment) == -1) {
	throw 'Environnement incorrect.';
}
 
gulp.task('update-styles', function() {
	var object = gulp.src('./files/sass/**/*.scss'),
		options = (compressFiles) ? { outputStyle: 'compressed' } : {}
	;
	
	if(withSourceMaps) {
		object = object.pipe(sourcemaps.init({loadMaps: true}));
	}
	
	object = object.pipe(sass(options).on('error', sass.logError));
	
	if(withSourceMaps) {
		object = object.pipe(sourcemaps.write('./maps'));
	}
	
	object.pipe(gulp.dest('./files/styles'));
});
 
gulp.task('watch', function() {
	gulp.watch('./files/sass/**/*.scss', [ 'update-styles' ]);
});

gulp.task('update-static', [ 'update-styles' ]);