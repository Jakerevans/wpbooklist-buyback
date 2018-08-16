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
var sassBackendSource = ['dev/scss/buyback-main-admin.scss'];

var jsSources = ['dev/js/*.js']; // Any .js file in scripts directory


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

// Task to concatenate and uglify js files
gulp.task('concat', function() {
    gulp.src(jsSources) // use jsSources
        .pipe(concat('wpbooklist-buyback-admin-min.js')) // Concat to a file named 'script.js'
        .pipe(uglify()) // Uglify concatenated file
        .pipe(gulp.dest('assets/js')); // The destination for the concatenated and uglified file
});

// Task to watch for changes in our file sources
gulp.task('watch', function() {
    gulp.watch(sassMain,['sassFrontendSource']); // If any changes in 'sassMain', perform 'sass' task
    gulp.watch(sassMain,['sassBackendSource']);
    gulp.watch(jsSources,['concat']);
});

// Default gulp task
gulp.task('default', ['sassFrontendSource', 'sassBackendSource', 'concat', 'watch']);