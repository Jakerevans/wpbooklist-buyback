// Mostly derived from https://bitsofco.de/a-simple-gulp-workflow

//npm install gulp
//npm install --save-dev gulp-sass
//npm install --save-dev gulp-concat
//npm install --save-dev gulp-uglify
//npm install --save-dev gulp-util
//npm install --save-dev gulp-rename
//npm install --save-dev gulp-babel


// First require gulp
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    gutil = require('gulp-util'),
    rename = require('gulp-rename');


// Define default task
gulp.task('default', function() {
  console.log("Hello, world!");
});

// Define file sources
var sassMain = ['dev/scss/wpbooklist-buyback-admin-ui.scss'];
var sassFrontendSource = ['dev/scss/buyback-main-frontend.scss'];
var sassFrontendSourcePartial = ['dev/scss/_buyback-frontend-ui.scss'];
var sassBackendSource = ['dev/scss/buyback-main-admin.scss'];
var sassBackendSourcePartial = ['dev/scss/_buyback-backend-ui.scss'];

var jsBackendSource = ['dev/js/wpbooklist_buyback_admin.min.js']; // Any .js file in scripts directory
var jsFrontendSource = ['dev/js/wpbooklist_buyback_frontend.min.js']; // Any .js file in scripts directory


// Task to compile Frontend SASS file
gulp.task('sassFrontendSource', function() {
    gulp.src(sassFrontendSource) // use sassMain file source
        .pipe(sass({
            outputStyle: 'compressed' // Style of compiled CSS
        })
            .on('error', gutil.log)) // Log descriptive errors to the terminal
        .pipe(gulp.dest('assets/css')); // The destination for the compiled file
});

// Task to compile Backend SASS file
gulp.task('sassBackendSource', function() {
    gulp.src(sassBackendSource) // use sassMain file source
        .pipe(sass({
            outputStyle: 'compressed' // Style of compiled CSS
        })
            .on('error', gutil.log)) // Log descriptive errors to the terminal
        .pipe(gulp.dest('assets/css')); // The destination for the compiled file
});

// Task to minify Backend js file
gulp.task('jsBackendSource', function() {
    gulp.src(jsBackendSource) // use jsSources
        .pipe(uglify()) // Uglify concatenated file
        .pipe(gulp.dest('assets/js')); // The destination for the concatenated and uglified file
});

// Task to minify Frontend js file
gulp.task('jsFrontendSource', function() {
    gulp.src(jsFrontendSource) // use jsSources
        .pipe(uglify()) // Uglify concatenated file
        .pipe(gulp.dest('assets/js')); // The destination for the concatenated and uglified file
});

// Task to watch for changes in our file sources
gulp.task('watch', function() {
    gulp.watch(sassFrontendSource,['sassFrontendSource']);
    gulp.watch(sassFrontendSourcePartial,['sassFrontendSource']);
    gulp.watch(sassBackendSource,['sassBackendSource']);
    gulp.watch(sassBackendSourcePartial,['sassBackendSource']);
    gulp.watch(jsBackendSource,['jsBackendSource']);
    gulp.watch(jsFrontendSource,['jsFrontendSource']);
});

// Default gulp task
gulp.task('default', ['sassFrontendSource', 'sassBackendSource', 'jsBackendSource', 'jsFrontendSource', 'watch']);