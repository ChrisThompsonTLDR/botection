<?php

Route::get('/', ['as' => 'home', 'uses' => 'PageController@home']);

Route::get('/r/{subreddit}', ['as' => 'sub', 'uses' => 'PageController@sub']);
Route::get('/r/{subreddit}/comments/{id}', ['as' => 'thread', 'uses' => 'PageController@thread']);

Auth::routes();
Route::group(['middleware' => 'auth'], function() {
    Route::get('authorize', ['as' => 'oauth.authorize', 'uses' => 'OauthController@authorize_it']);
    Route::get('callback',  ['as' => 'oauth.callback',  'uses' => 'OauthController@callback']);

    Route::post('watch', ['as' => 'watch.store', 'uses' => 'WatchController@store']);
});