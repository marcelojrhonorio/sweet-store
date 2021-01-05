let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .copyDirectory('resources/assets/images', 'public/images');


mix.combine([
  // './node_modules/@fontawesome/fontawesome-free/css/all.min.css',
  './node_modules/bootstrap/dist/css/bootstrap.min.css',
  './node_modules/select2/dist/css/select2.min.css',
  './public/css/app.css'
], 'public/css/store.css');

mix.combine([
  './node_modules/@fontawesome/fontawesome-free/js/all.min.js',
  './node_modules/jquery/dist/jquery.min.js',
  './node_modules/bootstrap/dist/js/bootstrap.min.js',
  './node_modules/jquery-mask-plugin/dist/jquery.mask.min.js',
  './node_modules/select2/dist/js/select2.min.js',
  './node_modules/select2/dist/js/i18n/pt-BR.js',
  './node_modules/popper.js/dist/umd/popper.min.js',
  './public/js/app.js',
  './resources/assets/js/pixels.js',
], 'public/js/store.js');
