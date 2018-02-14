<?php

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/', ['as' => 'welcome', 'middleware' => 'guest', function () {
        return view('welcome');
    }]);

    Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
    Route::get('/about', ['as' => 'about', 'uses' => 'HomeController@about']);

    Route::get('/login/{provider}', ['as' => 'third_party_login', 'uses' => 'ThirdPartyAuthController@redirectToProvider']);
    Route::get('/login/{provider}/callback', ['as' => 'third_party_login_callback', 'uses' => 'ThirdPartyAuthController@handleProviderCallback']);

    Route::get('quiz', ['as' => 'random_word', 'uses' => 'RandomWordsController@randomWord']);
    Route::get('quiz/next', ['as' => 'next_random_word', 'uses' => 'RandomWordsController@nextRandomWord']);
    Route::post('quiz/check', ['as' => 'check_answer', 'uses' => 'RandomWordsController@checkAnswer']);

    Route::resource('/words', 'WordsController', ['names' => [
        'index' => 'words',
        'create' => 'add_word',
        'store' => 'insert_word',
        'show' => 'view_word',
        'edit' => 'edit_word',
        'update' => 'update_word',
        'destroy' => 'delete_word',
    ]]);
});
