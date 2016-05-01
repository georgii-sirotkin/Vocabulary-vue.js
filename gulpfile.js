var elixir = require('laravel-elixir');

const PUBLIC_DIR = '../html';

elixir.config.publicDir = PUBLIC_DIR;
elixir.config.publicPath = PUBLIC_DIR;

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
	mix.styles(['ie10-viewport-bug-workaround.css', 'theme.css', 'bootstrap-social.css'], PUBLIC_DIR + '/css/styles.css');
	mix.scripts(['ie10-viewport-bug-workaround.js', 'code.js'], PUBLIC_DIR + '/js/scripts.js');
	mix.copy('resources/assets/js/form.js', PUBLIC_DIR + '/js/form.js');
    mix.version(['css/styles.css', 'js/scripts.js', 'js/form.js']);
});
