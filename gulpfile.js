'use strict'

let gulp = require('gulp'),
  scss = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  babel = require('gulp-babel'),
  concat = require('gulp-concat'),
  plumber = require('gulp-plumber'),
  imagemin = require('gulp-imagemin')

let SASS_INCLUDE_PATHS = [
  './node_modules/bootstrap/scss',
]

function handleError (err) {
  console.log(err.toString())
  this.emit('end')
}

gulp.task('styles', function () {
  return gulp.src('./assets/scss/main.scss')
    .pipe(plumber({errorHandler: handleError}))
    .pipe(sourcemaps.init())
    .pipe(scss({outputStyle: 'compressed', includePaths: SASS_INCLUDE_PATHS}))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./public/build/css'))
})

gulp.task('css', function () {
  return gulp.src('./assets/css/*.css')
    .pipe(plumber({errorHandler: handleError}))
    .pipe(gulp.dest('./public/build/css'))
})

gulp.task('fonts', function () {
  return gulp.src('./assets/fonts/*')
    .pipe(plumber({errorHandler: handleError}))
    .pipe(gulp.dest('./public/build/fonts'))
})

gulp.task('js', function () {
  return gulp.src('./assets/js/*.js')
    .pipe(plumber({errorHandler: handleError}))
    .pipe(sourcemaps.init())
    .pipe(babel({compact: true}))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./public/build/js'))
})

gulp.task('libs-js', function () {
  return gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/bootstrap-validator/dist/validator.js',
  ])
    .pipe(plumber({errorHandler: handleError}))
    .pipe(concat('libs.js'))
    .pipe(gulp.dest('./public/build/js'))
})

gulp.task('images', function () {
  gulp.src('assets/images/*')
    .pipe(imagemin())
    .pipe(gulp.dest('./public/build/img'))
}
)

gulp.task('watch', ['styles', 'js'], function () {
  gulp.watch('./assets/scss/**/*.scss', ['styles'])
  gulp.watch('./assets/css/**/*.css', ['css'])
  gulp.watch('./assets/js/**/*.js', ['js'])
  gulp.watch('./assets/img/*', ['images'])
})

gulp.task('default', ['libs-js', 'styles', 'css', 'js', 'images', 'fonts'], function () {

})
