var gulp = require('gulp');
var $    = require('gulp-load-plugins')();

var sassPaths = [
  'node_modules/foundation-sites/scss',
  'node_modules/motion-ui/src'
];

// Add or remove plugins here after mediaQuery.
var jsFoundationPlugins = [
  'node_modules/foundation-sites/dist/js/plugins/foundation.core.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.mediaQuery.min.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.keyboard.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.triggers.min.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.timerAndImageLoader.min.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.accordion.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.equalizer.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.sticky.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.tabs.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.orbit.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.motion.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.timer.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.imageLoader.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.util.touch.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.responsiveMenu.min.js',
  'node_modules/foundation-sites/dist/js/plugins/foundation.responsiveToggle.min.js'
];

gulp.task('sass', function() {
  return gulp.src('scss/app.scss')
    .pipe($.sassGlob())
    .pipe($.sourcemaps.init())
    .pipe($.sass({
      includePaths: sassPaths,
      outputStyle: 'expanded' // if css compressed **file size**
    })
      .on('error', $.sass.logError))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9']
    }))
    .pipe($.sourcemaps.write('.'))
    .pipe(gulp.dest('css'))
    .pipe($.livereload());
});

gulp.task('javascript', function() {
  return gulp.src(jsFoundationPlugins)
    .pipe($.concat('foundation.js'))
    .pipe(gulp.dest('js'));
});

gulp.task('default', ['sass', 'javascript'], function() {
  $.livereload.listen();
  gulp.watch(['scss/**/*.scss'], ['sass']);
});
