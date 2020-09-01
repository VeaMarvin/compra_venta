let mix = require('laravel-mix');

/*mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css'); */

mix
    .styles( [
        'resources/assets/plantilla/css/font-awesome.min.css',
        'resources/assets/plantilla/css/simple-line-icons.min.css',
        'resources/assets/plantilla/css/style.css'
    ], 'public/css/plantilla_style.css' )
    .scripts( [
        'resources/assets/plantilla/js/jquery.min.js',
        'resources/assets/plantilla/js/popper.min.js',
        'resources/assets/plantilla/js/bootstrap.min.js',
        'resources/assets/plantilla/js/Chart.min.js',
        'resources/assets/plantilla/js/pace.min.js',
        'resources/assets/plantilla/js/template.js',
        'resources/assets/plantilla/js/sweetalert2.all.js'
    ], 'public/js/plantilla_script.js' )
    .js( [
        'resources/assets/js/app.js'
    ], 'public/js/app_vue.js' );
