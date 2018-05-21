'use strict';

var gulp        = require('gulp'),
    prefixer    = require('gulp-autoprefixer'), 
    uglify      = require('gulp-uglify'),     
    rigger      = require('gulp-rigger'),     
    sass        = require('gulp-sass'),
    notify      = require('gulp-notify'),
    sourcemaps  = require('gulp-sourcemaps'),
    cssmin      = require('gulp-clean-css'),  
    rename      = require('gulp-rename'),
    imagemin    = require('gulp-imagemin'), 
    pngquant    = require('imagemin-pngquant'), 
    spritesmith = require('gulp.spritesmith'),
    plumber     = require('gulp-plumber'),
    concat      = require('gulp-concat'),
    urlAdjuster = require('gulp-css-url-adjuster');

var path = { // skins
    build: {
        js:         '../web/assets/build/js/',
        css:        '../web/assets/build/css/',
        img:        '../web/assets/build/img/',
        images:     '../web/assets/build/images/'
    },
    src: {
        js:         'src/js/**/*.js',
        scss:       'src/scss/**/*.scss*',
        img:        'src/img/**/*.*',
        css:        'src/css/*.css',
        images:     'src/images/*.*'
    },
    watch: {
        js:     'src/js/**/*.js',
        scss:   'src/scss/**/*.scss',
        img:    'src/img/**/*.*',
        css:    'src/css/*.css',
        images: 'src/images/*.*'
    }
};

var supportingBrowsers = [
  '> 3%',
  'last 2 versions',
  'ie 9',
  'ie 10'
];

gulp.task('js:build', function () {
    gulp.src(path.src.js)               // Найдем наш main файл
        .pipe(plumber())
        .pipe(rigger())                 // Прогоним через rigger
        .pipe(sourcemaps.init())        // Инициализируем sourcemap
        .pipe(sourcemaps.write())       // Пропишем карты
        .pipe(gulp.dest(path.build.js)) // Выплюнем готовый файл в build
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())                 // Сожмем наш js
        .pipe(gulp.dest(path.build.js))
        .pipe(plumber.stop())
        .pipe(notify("js create!"));
});

gulp.task('scss:build', function () {
    gulp.src(path.src.scss)            // Выберем наш main.scss
        .pipe(plumber())
        .pipe(sourcemaps.init())         // То же самое что и с js
        .pipe(sass())                    // Скомпилируем
        .pipe(prefixer(supportingBrowsers)) // Добавим вендорные префиксы
        .pipe(sourcemaps.write())        // Пропишем карты
        .pipe(gulp.dest(path.build.css))               
        .pipe(rename({suffix: '.min'}))
        .pipe(cssmin())                  // Сожмем
        .pipe(gulp.dest(path.build.css)) // И в build
        .pipe(plumber.stop())
        .pipe(notify("sass create!"));
});

gulp.task('css:build', function () {
    gulp.src(path.src.css)
        .pipe(urlAdjuster({
            replace:  ['../images','/assets/build/images'],
        }))
        .pipe(urlAdjuster({
            replace:  ['../../images','/assets/build/images'],
        }))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(path.build.css))
        .pipe(cssmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(path.build.css))
        .pipe(notify("css create !"));
});

gulp.task('image:build', function () {
    gulp.src(path.src.img) //Выберем наши картинки
        .pipe(plumber())
        .pipe(imagemin({   //Сожмем их
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()],
            interlaced: true
        }))
        .pipe(plumber.stop())
        .pipe(gulp.dest(path.build.img))
        .pipe(notify("img create!"));

    gulp.src(path.src.images)
        .pipe(plumber())
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()],
            interlaced: true
        }))
        .pipe(plumber.stop())
        .pipe(gulp.dest(path.build.images))
        .pipe(notify("images create!"));

});

gulp.task('build', [
    'js:build',
    'scss:build',
    'css:build',
    'image:build',
]);

gulp.task('watch', function(){
    gulp.watch([path.watch.js], ['js:build']);
    gulp.watch([path.watch.scss], ['scss:build']);
    gulp.watch([path.watch.img], ['image:build']);
    gulp.watch([path.watch.images], ['image:build']);
    gulp.watch([path.watch.css], ['css:build']);
});

gulp.task('default', ['build', 'watch']);
