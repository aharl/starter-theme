'use strict';

import gulp from 'gulp';
import mainBowerFiles from 'main-bower-files';
import notify from 'gulp-notify';
import sass from 'gulp-sass';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';
import sourcemaps from 'gulp-sourcemaps';
import clean from 'gulp-clean';
import {create as bsCreate} from 'browser-sync';
const browserSync = bsCreate();
import wiredeps from 'wiredep';
const wiredep = wiredeps.stream;

const devUrl = 'usfulglass.dev';

const dirs = {
  static: 'static'
};

const stylePaths = {
  src: `${dirs.static}/scss/**/*.scss`,
  dest: `${dirs.static}/css/`
};

gulp.task('styles', () => {
  return gulp.src(stylePaths.src)
    .pipe(wiredep())
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(postcss([
      autoprefixer(),
      cssnano(),
    ]))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(stylePaths.dest))
    .pipe(browserSync.stream());
});

gulp.task('handleBowerFiles', () => {
    // move JS files
    gulp.src(mainBowerFiles('**/*.js'))
        .pipe(gulp.dest(`${dirs.static}/js/libs`))
        .pipe(notify({ message: 'Bower JS files moved' }));

    // move css files
    // gulp.src(mainBowerFiles('**/*.css'))
    //     .pipe(gulp.dest(`${dirs.static}/css/libs`))
    //     .pipe(notify({ message: 'Bower css files moved' }));

    // move font files
    gulp.src(mainBowerFiles(['**/*.eot', '**/*.svg', '**/*.ttf', '**/*.woff', '**/*.woff2']))
        .pipe(gulp.dest(`${dirs.static}/fonts`))
        .pipe(notify({ message: 'Bower fonts moved' }));
});

gulp.task('clean', () => {
  return gulp.src([`${dirs.static}/scss/libs/**/*.scss`], {read: false})
    .pipe(clean())
    .pipe(notify({ message: 'Files cleaned up' }));
});

gulp.task('watch', ['styles'], () => {
    browserSync.init({
        files: ['{inc,template-parts}/**/*.php', '*.php', 'templates/*.twig', 'js/**/*.js'],
        proxy: devUrl,
        snippetOptions: {
            whitelist: ['/wp-admin/admin-ajax.php'],
            blacklist: ['/wp-admin/**']
        }
    });
    gulp.watch(stylePaths.src, ['styles']);
});
