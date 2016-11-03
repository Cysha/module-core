<?php

namespace Cms\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Cms\Modules\Core;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        with(new Core\Models\DBConfig())->truncate();

        $environment = app()->environment();
        $array = [
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'site-name',
                'value' => 'PhoenixCMS',
            ],
            [
                'environment' => $environment,
                'group' => 'app',
                'item' => 'timezone',
                'value' => 'Europe/London',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'google-analytics',
                'value' => null,
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'force-secure',
                'value' => 'false',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'minify-html',
                'value' => 'false',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app.themes',
                'item' => 'frontend',
                'value' => 'default',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app.themes',
                'item' => 'backend',
                'value' => 'adminlte',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'debug',
                'value' => 'false',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'maintenance',
                'value' => 'false',
            ],
            [
                'environment' => $environment,
                'group' => 'api',
                'item' => 'vendor',
                'value' => 'cysha',
            ],
            [
                'environment' => $environment,
                'group' => 'api',
                'item' => 'strict',
                'value' => 'false',
            ],
            [
                'environment' => $environment,
                'group' => 'api',
                'item' => 'debug',
                'value' => 'false',
            ],
            [
                'environment' => $environment,
                'group' => 'api',
                'item' => 'prefix',
                'value' => 'api/',
            ],
            [
                'environment' => $environment,
                'group' => 'cms.core.app',
                'item' => 'csrf-except',
                'value' => '["api/*"]',
            ],
        ];

        foreach ($array as $r) {
            with(new Core\Models\DBConfig())->fill($r)->save();
        }
    }
}
