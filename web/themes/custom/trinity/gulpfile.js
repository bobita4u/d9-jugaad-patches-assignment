const gulp = require('gulp');
const sass = require('gulp-sass')(require('node-sass')); // Pass node-sass instance
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');

const paths = {
  styles: {
    src: 'scss/**/*.scss',
    dest: 'css/'
  },
  scripts: {
    src: 'js/**/*.js',
    dest: 'js/min/'
  }
};

function styles() {
  return gulp.src(paths.styles.src)
    .pipe(sass())
    .pipe(autoprefixer())
    .pipe(gulp.dest(paths.styles.dest));
}

function scripts() {
  return gulp.src(paths.scripts.src)
    .pipe(concat('scripts.js'))
    .pipe(uglify())
    .pipe(gulp.dest(paths.scripts.dest));
}

exports.default = gulp.parallel(styles, scripts);
