var gulp = require('gulp'),
	browserSync = require('browser-sync'),
	reload = browserSync.reload;

// load plugins
var $ = require('gulp-load-plugins')();

// ---------------------------
// Configuration
// ----------------------------

var ROOT = './',
	SOURCE = ROOT + 'assets/',
	BUILD_CSS = ROOT + 'public/css/',
	BUILD_SCRIPTS = ROOT + 'public/js/',
	BUILD_FONTS = ROOT + 'public/fonts/',
	LIBRARY = ROOT + 'node_modules/';

var FONTS = 'fonts/',
	IMAGES = 'img/',
	SCRIPTS = 'scripts/',
	STYLES = 'styles/';

var onError = function (err) {
	$.notify.onError({
		title  : "Gulp error in " + err.plugin,
		message: err.toString()
	})(err);
	this.emit('end');
};

gulp.task('scripts', function () {
	// TODO:: Split admin js and frontend?
	return gulp.src([
		SOURCE + SCRIPTS + '/components/global.js',
		SOURCE + SCRIPTS + '/components/*.js',
		SOURCE + SCRIPTS + 'main.js',
		SOURCE + SCRIPTS + 'frontend.js'
	])
		.pipe($.concat('main.js'))
		//.pipe($.rename('main.min.js'))
		//.pipe($.uglify())
		.pipe(gulp.dest(BUILD_SCRIPTS));
});

gulp.task('js-vendor', function () {
	return gulp.src([
		LIBRARY + 'jquery/dist/jquery.js',
		LIBRARY + 'tether/dist/js/tether.js',
		//LIBRARY + 'bootstrap/dist/js/bootstrap.js',
		LIBRARY + 'tinymce/tinymce.js',
		LIBRARY + 'dropzone/dist/dropzone.js'
	])
		.pipe($.concat('vendor.js'))
		.pipe(gulp.dest(BUILD_SCRIPTS))
		.pipe($.rename('vendor.min.js'))
		.pipe($.uglify())
		.pipe(gulp.dest(BUILD_SCRIPTS));
});

// Move TinyMCE themes etc
gulp.task('tinymce', function () {
	return gulp.src([
		LIBRARY + 'tinymce/themes/**/*.js',
		LIBRARY + 'tinymce/skins/**/*',
		LIBRARY + 'tinymce/plugins/**/*'
	], {
		base: './node_modules/tinymce'
	})
		.pipe(gulp.dest(BUILD_SCRIPTS))
});

//--------------------------//
//  Styles.
//  Scss compilation
//-------------
gulp.task('styles', function () {
	return gulp.src(SOURCE + STYLES + 'main.scss')
		.pipe($.plumber({
			errorHandler: onError
		}))
		.pipe($.sourcemaps.init())
		.pipe($.sass({
			errLogToConsole: false
		}))
		.pipe($.autoprefixer('last 2 versions'))
		.pipe($.sourcemaps.write('.'))
		.pipe(gulp.dest(BUILD_CSS))
		.pipe(reload({
			stream: true
		}));
	//.pipe($.notify("SCSS Compilation complete."));;
});

// Move font-awesome fonts folder to css compiled folder
gulp.task('icons', function () {
	return gulp.src(LIBRARY + '/components-font-awesome/fonts/**.*')
		.pipe(gulp.dest(BUILD_FONTS));
});

//--------------------------//
//  Default tasks.
//-------------
gulp.task('default', function () {
	gulp.start('watch');
});


gulp.task('build', ['styles', 'icons', 'js-vendor', 'scripts', 'tinymce']);
//--------------------------//
//  Serve & Watch
//-------------

gulp.task('watch', ['build'], function () {

	browserSync.init({
		files: ['{src,public}/**/*.php', '{templates}/**/*.twig'],
		proxy: 'localhost/recipe-manager/public',
	});
	gulp.watch(SOURCE + STYLES + '**/*.scss', ['styles']);
	gulp.watch(SOURCE + SCRIPTS + '**/*.js', ['scripts']);

});