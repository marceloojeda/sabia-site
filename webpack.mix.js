const mix = require('laravel-mix');

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

mix.js([
    'resources/js/app.js',
    'resources/js/mascara.js',
    'resources/js/adm.js',
    'resources/js/billet.js',
    'resources/js/html-to-image.js',
    'resources/js/chart.min.js',
    'resources/js/head_home.js',
], 'public/js')
    .sass('resources/sass/app.scss', 'public/css');
