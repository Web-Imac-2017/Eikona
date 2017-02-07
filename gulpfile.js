//VARIABLES
var gulp 	    = require('gulp'),
    gutil 	    = require('gulp-util'),
    livereload  = require('gulp-livereload'),
    runSequence = require('run-sequence');


//TACHES
gulp.task('phpHtmlReload', function(){
	return gulp.src('app/**/*.+(php|html)')
		.pipe(livereload().on('error', gutil.log));
});

gulp.task('watch', function(){
	livereload.listen();
	gulp.watch('app/**/*.+(php|html)', ['phpHtmlReload']);
});


gulp.task('default', function(callback){
	runSequence(['watch'], callback);
});
