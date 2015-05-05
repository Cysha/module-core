<?php


Route::group(['namespace' => $namespace.'\Frontend'], function () {

    Route::get('test', 'PagesController@test');
    Route::get('test', ['as' => 'pxcms.user.register', 'uses' => 'PagesController@test']);
    Route::get('test', ['as' => 'pxcms.user.login', 'uses' => 'PagesController@test']);
    Route::get('/', ['as' => 'pxcms.pages.home', 'uses' => 'PagesController@getHomepage']);
});
