<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Cms\Modules\Core;
use Cache;
use Config;
use Schema;
use DB;

class CmsConfigProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (Cache::has('core.config_table')) {
            $table = Cache::get('core.config_table');
        } else {
            // test for db connectivity
            try {
                DB::connection()->getDatabaseName();
            } catch (\PDOException $e) {
                return;
            }

            // make sure the config table is installed
            if (!Schema::hasTable(with(new Core\Models\DBConfig)->table)) {
                return;
            }

            // cache the config table
            $table = Cache::rememberForever('core.config_table', function () {
                return Core\Models\DBConfig::orderBy('environment', 'asc')->get();
            });
        }

        if ($table->count() == 0) {
            return;
        }

        // run over the environments and set the config vars
        foreach (['*', app()->environment()] as $env) {
            foreach ($table as $item) {
                // check if we have the right environment
                if ($item->environment != $env) {
                    continue;
                }

                // and then override it
                Config::set($item->key, $item->value);
            }
        }

    }
}
