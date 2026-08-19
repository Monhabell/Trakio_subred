const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .styles([
       'resources/css/dig/dig_product_light.css',
       'resources/css/dig/dig_product_dark.css',
       'resources/css/modal_user.css'
   ], 'public/css/all.css')
   .version();
