<?php

Route::get('sitemap.xml', array('as' => 'site.map', 'uses' => $namespace.'\ExtrasController@getSitemap'));
Route::get('/', ['as' => 'home', 'uses' => $namespace.'\ExtrasController@getHomepage']);
