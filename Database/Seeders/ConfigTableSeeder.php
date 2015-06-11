<?php namespace Cms\Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Cms\Modules\Core;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        with(new Core\Models\DBConfig)->truncate();

        $environment = app()->environment();
        $array = [
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app',
                'item'        => 'site-name',
                'value'       => 'PhoenixCMS',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'app',
                'item'        => 'timezone',
                'value'       => 'Europe/London',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app',
                'item'        => 'google-analytics',
                'value'       => null,
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app',
                'item'        => 'force-secure',
                'value'       => 'false',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app',
                'item'        => 'minify-html',
                'value'       => 'false',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app.themes',
                'item'        => 'frontend',
                'value'       => 'default',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app.themes',
                'item'        => 'backend',
                'value'       => 'default_admin',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app',
                'item'        => 'debug',
                'value'       => 'false',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'cms.core.app',
                'item'        => 'maintenance',
                'value'       => 'false',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'api',
                'item'        => 'vendor',
                'value'       => 'cysha',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'api',
                'item'        => 'strict',
                'value'       => 'false',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'api',
                'item'        => 'debug',
                'value'       => 'false',
            ],
            [
                'environment' => $environment,
                'namespace'   => null,
                'group'       => 'api',
                'item'        => 'prefix',
                'value'       => 'api/',
            ],
        ];

        foreach ($array as $r) {
            with(new Core\Models\DBConfig)->fill($r)->save();
        }

    }

}
