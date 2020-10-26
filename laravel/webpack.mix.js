const mix = require('laravel-mix');

mix.config.publicPath = '../';
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

mix.js('resources/assets/js/app.js', 'js')
    .js('resources/assets/js/appGame.js', 'js/appGame.js')
    // .js('resources/assets/js/gameMachine.js', 'js/gameMachine.js')
    .js('resources/assets/js/gameMachineScreen.js', 'js/gameMachineScreen.js')
    .js('resources/assets/js/home.js', 'js/home.min.js')
    .js('resources/assets/js/panel.js', 'js/panel.min.js')
    .js('resources/assets/js/panel.distri.js', 'js/panel.distri.min.js')
    .sass('resources/assets/sass/app.scss', 'css')
    .sass('resources/assets/sass/screen.scss', 'css/screen.machine.css')
    .styles('resources/assets/css/panel.custom.css', '../css/panel.custom.min.css')
    .styles('resources/assets/css/index.edit.css', '../css/index.edit.css')
    .options({
        //processCssUrls: false,
    });