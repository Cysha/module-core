<?php


Route::group(['namespace' => $namespace.'\Frontend'], function () {

    Route::get('test', 'PagesController@test');
    Route::get('/', ['as' => 'pxcms.pages.home', 'uses' => 'PagesController@getHomepage']);
});
