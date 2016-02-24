<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

// Auth::loginUsingId(9);

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
 */

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/test', function () {
        return public_path();
    });

    Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
    Route::get('/login/{provider}', ['as' => 'third_party_login', 'uses' => 'ThirdPartyAuthController@redirectToProvider']);
    Route::get('/login/{provider}/callback', ['as' => 'third_party_login_callback', 'uses' => 'ThirdPartyAuthController@handleProviderCallback']);

    Route::get('words/random', ['as' => 'random_word', 'uses' => 'WordsController@randomWord']);
    Route::resource('/words', 'WordsController', ['names' => [
        'index' => 'words',
        'create' => 'add_word',
        'store' => 'insert_word',
        'show' => 'view_word',
        'edit' => 'edit_word',
        'update' => 'update_word',
        'destroy' => 'delete_word',
    ]]);

    // main (statistics)+
    // random+
    // find
    // add+
    // edit+
    // words+
    // delete+
    // view+
});
