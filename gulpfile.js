var gulp = require('gulp');
var phpunit = require('gulp-phpunit');

gulp.task('phpunit', function() {
    var options = {debug: false};
    gulp.src('phpunit.xml')
        .pipe(phpunit('./vendor/bin/phpunit',options));
});

gulp.task('default', function() {
    gulp.watch(['src/**/*.php', 'tests/**/*.php'], function() {
        gulp.run('phpunit')
    });
});
