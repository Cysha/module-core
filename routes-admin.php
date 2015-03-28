<?php

// URI: /admin
Route::group(['prefix' => 'admin', 'namespace' => 'Cysha\Modules\Core\Controllers\Admin'], function () {
    // URI: /admin/config
    Route::group(['prefix' => 'config', 'namespace' => 'Config'], function () {

        // URI: /admin/config/theme/
        Route::group(['prefix' => 'theme'], function () {
            Route::get('switch/{theme}', ['as' => 'admin.theme.switch', 'uses' => 'ThemeController@getSwitch', 'before' => 'permissions']);
            Route::get('/', ['as' => 'admin.theme.index', 'uses' => 'ThemeController@getIndex', 'before' => 'permissions']);
        });

        Route::post('save', ['as' => 'admin.config.store', 'uses' => 'SiteController@postStoreConfig', 'before' => 'permissions:admin.config.site']);
        Route::get('/', ['as' => 'admin.config.index', 'uses' => 'SiteController@getIndex', 'before' => 'permissions:admin.config.site']);
    });

});
