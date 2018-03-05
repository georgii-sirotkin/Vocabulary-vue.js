<?php

Route::auth();

Route::middleware('guest')->group(function () {
    // third party auth
    Route::get('/login/{provider}', ['as' => 'third_party_login', 'uses' => 'ThirdPartyAuthController@redirectToProvider']);
    Route::get('/login/{provider}/callback', ['as' => 'third_party_login_callback', 'uses' => 'ThirdPartyAuthController@handleProviderCallback']);

    Route::view('/', 'welcome')->middleware('guest')->name('welcome');
});

Route::middleware('auth')->group(function () {
    Route::view('/home', 'home')->name('home');
    Route::view('/about', 'about')->name('about');

    Route::get('quiz', ['as' => 'random_word', 'uses' => 'RandomWordsController@randomWord']);
    Route::get('quiz/next', ['as' => 'next_random_word', 'uses' => 'RandomWordsController@nextRandomWord']);
    Route::post('quiz/check', ['as' => 'check_answer', 'uses' => 'RandomWordsController@checkAnswer']);

    Route::resource('/words', 'WordsController');
});
