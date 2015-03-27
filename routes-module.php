<?php
$namespace .= '\Module';

Route::get('sitemap.xml', array('as' => 'pxcms.pages.sitemap', 'uses' => $namespace.'\ExtrasController@getSitemap'));

if (Config::has('core::app.pxcms-index')) {
    Route::get('/', ['as' => 'pxcms.pages.home', 'uses' => Config::get('core::app.pxcms-index')]);
}
