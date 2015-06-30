
var gulp = require('gulp');
var minifycss = require('gulp-minify-css');


gulp.task('css', function(){
	return gulp.src('css/main.css')
	.pipe(minifycss()
	.pipe(gulp.dest('css/min'));

});
