var gulp         = require('gulp'),
    browserSync  = require('browser-sync').create();

gulp.task('browser-sync', function() {
    browserSync.init({
        //proxy:'http://vit.dev/_optimized_gulp_sass/app/'
         server: {
         		baseDir: "."
         },
        notify: false
    });
});


gulp.task('watch', function () {
    gulp.watch('*.*').on('change', browserSync.reload);
});

gulp.task('default', ['browser-sync', 'watch']);
