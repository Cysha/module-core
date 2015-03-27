<?php namespace Cysha\Modules\Core\Seeds;

use Cysha\Modules\Core as Core;
use App;

class ConfigSeeder extends \Seeder
{
    public function run()
    {
        with(new Core\Models\DBConfig)->truncate();

        $environment = App::environment();
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
