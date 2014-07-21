<?php

Route::get('sitemap.xml', array('as' => 'pxcms.pages.sitemap', 'uses' => $namespace.'\ExtrasController@getSitemap'));
Route::get('/', ['as' => 'pxcms.pages.home', 'uses' => $namespace.'\ExtrasController@getHomepage']);
