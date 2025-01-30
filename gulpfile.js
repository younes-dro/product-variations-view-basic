const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const header = require('gulp-header');
const moment = require('moment');
const yargs = require('yargs');
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require('gulp-clean-css');

// Get the file from command-line arguments or use the default
const argv = yargs.argv;
const filePathAdmin = argv.file || 'scss/admin/dro-pvvp-variation-image-collections.scss'; // Default to admin file
const filePathFrontEnd = argv.file || 'scss/frontend/dro-pvvp-frontend.scss'; // Default to Frontend file

// Get options for minification and source maps from arguments
const isMinified = argv.minify || false;
const isSourceMap = argv.sourcemap || false;

const getDate = () => moment().format('MM/DD/YYYY');

// Admin task to compile a specific Sass file
gulp.task('sass-admin', function(done) {
  let stream = gulp.src(filePathAdmin)
    .pipe(sass().on('error', sass.logError));

  // If source maps are enabled, add sourcemaps
  if (isSourceMap) {
    stream = stream.pipe(sourcemaps.init());
  }

  stream = stream.pipe(header(`
/*!
 * Product Variations View Pro
 *
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: <%= date %>
 * Released under the GPLv2.0 or later
 */
`, { date: getDate() }));

  // Unminified file output (always generate this)
  stream.pipe(gulp.dest('assets/css/admin'));

  // If minification is enabled, create a minified version with .min.css
  if (isMinified) {
    stream.pipe(cleanCSS({ compatibility: 'ie8' })) // Minify the CSS
      .pipe(gulp.dest('assets/css/admin')); // Save the minified version without renaming
    stream.pipe(cleanCSS({ compatibility: 'ie8' })) // Minify and rename with .min.css
      .pipe(gulp.dest(function(file) {
        // Rename the output file to have a .min.css extension
        file.basename = file.basename.replace('.css', '.min.css');
        return 'assets/css/admin';
      }));
  }

  // If source maps are enabled, write them for both unminified and minified versions
  if (isSourceMap) {
    stream.pipe(sourcemaps.write('.'))
      .pipe(gulp.dest('assets/css/admin'));
  }

  done();
});

// Frontend task to compile a specific Sass file
gulp.task('sass-frontend', function(done) {
  let stream = gulp.src(filePathFrontEnd)
    .pipe(sass().on('error', sass.logError));

  // If source maps are enabled, add sourcemaps
  if (isSourceMap) {
    stream = stream.pipe(sourcemaps.init());
  }

  stream = stream.pipe(header(`
/*!
 * Product Variations View Pro
 *
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: <%= date %>
 * Released under the GPLv2.0 or later
 */
`, { date: getDate() }));

  // Unminified file output (always generate this)
  stream.pipe(gulp.dest('assets/css/frontend'));

  // If minification is enabled, create a minified version with .min.css
  if (isMinified) {
    stream.pipe(cleanCSS({ compatibility: 'ie8' })) // Minify the CSS
      .pipe(gulp.dest('assets/css/frontend')); // Save the minified version without renaming
    stream.pipe(cleanCSS({ compatibility: 'ie8' })) // Minify and rename with .min.css
      .pipe(gulp.dest(function(file) {
        // Rename the output file to have a .min.css extension
        file.basename = file.basename.replace('.css', '.min.css');
        return 'assets/css/frontend';
      }));
  }

  // If source maps are enabled, write them for both unminified and minified versions
  if (isSourceMap) {
    stream.pipe(sourcemaps.write('.'))
      .pipe(gulp.dest('assets/css/frontend'));
  }

  done();
});

// Watch task for both Admin and Frontend Sass files
gulp.task('watch', function() {
  gulp.watch('scss/admin/**/*.scss', gulp.series('sass-admin'));
  gulp.watch('scss/frontend/**/*.scss', gulp.series('sass-frontend'));
});

// Default task to compile both Admin and Frontend Sass and watch files
gulp.task('default', gulp.series('sass-admin', 'sass-frontend', 'watch'));
