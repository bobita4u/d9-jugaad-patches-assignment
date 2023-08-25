const gulp = require('gulp');
const sass = require('gulp-sass')(require('node-sass'));
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');

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

// Define a task to transpile, minify, and concatenate JS files
function scripts() {
  return gulp.src(paths.scripts.src)
    .pipe(babel({
      presets: ['@babel/preset-env']
    }))
    .pipe(concat('scripts.js')) // Concatenate into a single file
    .pipe(uglify()) // Minify
    .pipe(gulp.dest(paths.scripts.dest)); // Output directory
}

// Define a default task
exports.default = gulp.parallel(styles, scripts);
