<?php

Route::get('sitemap.xml', array('as' => 'site.map', 'uses' => $namespace.'\ExtrasController@getSitemap'));
