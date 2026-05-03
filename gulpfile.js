const { src, dest, watch, series } = require('gulp');
const sass = require('gulp-sass')(require('sass'));

function scss() {
  return src('assets/scss/projects.scss')
    .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
    .pipe(dest('assets/css'));
}

function watchFiles() {
  watch('assets/scss/**/*.scss', scss);
}

exports.default = series(scss, watchFiles);
exports.build   = scss;