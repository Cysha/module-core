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
                'namespace'   => 'core',
                'group'       => 'app',
                'item'        => 'site-name',
                'value'       => 'PhoenixCMS',
            ],
            [
                'environment' => $environment,
                'namespace'   => '',
                'group'       => 'app',
                'item'        => 'timezone',
                'value'       => 'Europe/London',
            ],
            [
                'environment' => $environment,
                'namespace'   => 'core',
                'group'       => 'app',
                'item'        => 'google-analytics',
                'value'       => null,
            ],
            [
                'environment' => $environment,
                'namespace'   => 'core',
                'group'       => 'app',
                'item'        => 'force-secure',
                'value'       => false,
            ],
            [
                'environment' => $environment,
                'namespace'   => 'core',
                'group'       => 'app.themes',
                'item'        => 'frontend',
                'value'       => 'default',
            ],
            [
                'environment' => $environment,
                'namespace'   => 'core',
                'group'       => 'app.themes',
                'item'        => 'backend',
                'value'       => 'default-admin',
            ],
            [
                'environment' => $environment,
                'namespace'   => 'core',
                'group'       => 'app',
                'item'        => 'debug',
                'value'       => false,
            ],
            [
                'environment' => $environment,
                'namespace'   => 'pages',
                'group'       => 'editor',
                'item'        => 'theme',
                'value'       => 'monokai',
            ],
            [
                'environment' => $environment,
                'namespace'   => 'pages',
                'group'       => 'editor',
                'item'        => 'indent',
                'value'       => 4,
            ],
            [
                'environment' => $environment,
                'namespace'   => 'pages',
                'group'       => 'module',
                'item'        => 'execute-php',
                'value'       => false,
            ]
        ];

        foreach ($array as $r) {
            Core\Models\DBConfig::create($r);
        }

    }

}
