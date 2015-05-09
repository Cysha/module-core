<?php namespace Cms\Modules\Core\Providers;

class CoreModuleServiceProvider extends BaseModuleProvider
{

    /**
     * Register the defined middleware.
     *
     * @var array
     */
    protected $middleware = [
        'Core' => [
        ],
    ];

    /**
     * The commands to register.
     *
     * @var array
     */
    protected $commands = [
        'Core' => [
            'dump-autoload'  => 'DumpAutoloadCommand',
            'cms:install'    => 'CmsInstallCommand',
            'themes:gulp'    => 'ThemeGulpCommand',
            'themes:publish' => 'ThemePublishCommand',
        ],
    ];

}
