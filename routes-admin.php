<?php

// URI: /admin
Route::group(array('prefix' => 'admin'), function () {
    $namespace = 'Cysha\Modules\Core\Controllers\Admin';

    // URI: /admin/config
    Route::group(array('prefix' => 'config'), function () use ($namespace) {
        $namespace .= '\Config';

        // URI: /admin/config/theme/
        Route::group(array('prefix' => 'theme'), function () use ($namespace) {
            Route::get('switch/{theme}', array('as' => 'admin.theme.switch', 'uses' => $namespace.'\ThemeController@getSwitch', 'before' => 'permissions'));
            Route::get('/', array('as' => 'admin.theme.index',  'uses' => $namespace.'\ThemeController@getIndex', 'before' => 'permissions'));
        });

        Route::post('save', array('as' => 'admin.config.store', 'uses' => $namespace.'\SiteController@postStoreConfig', 'before' => 'permissions:admin.config.site'));
        Route::get('/', array('as' => 'admin.config.index', 'uses' => $namespace.'\SiteController@getIndex', 'before' => 'permissions:admin.config.site'));
    });

});
