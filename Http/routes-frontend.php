<?php

Route::get('test', 'PagesController@test');
Route::get('/', ['as' => 'pxcms.pages.home', 'uses' => 'PagesController@getHomepage']);
