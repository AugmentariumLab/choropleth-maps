/* jshint esversion: 10 */
const mix = require("laravel-mix");

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

mix.webpackConfig({
    resolve: {
        fallback: {
            path: require.resolve("path-browserify")
        }
    }
});

mix.js("resources/js/app.js", "public/js").css(
    "resources/css/app.css",
    "public/css"
);

mix.sass("resources/sass/auth.scss", "public/css");
mix.css("resources/css/map.css", "public/css");

mix.js("resources/js/DatasetManager.js", "public/js");
mix.css("resources/css/dataset_manager.css", "public/css");
