var elixir = require('laravel-elixir');

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
	mix.styles(['ie10-viewport-bug-workaround.css', 'theme.css', 'bootstrap-social.css'], 'public/css/styles.css');
	mix.scripts(['ie10-viewport-bug-workaround.js', 'code.js'], 'public/js/scripts.js');
	mix.copy('resources/assets/js/form.js', 'public/js/form.js');
    mix.version(['css/styles.css', 'js/scripts.js', 'js/form.js']);
});
