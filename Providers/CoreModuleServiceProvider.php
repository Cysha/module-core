<?php

namespace Cms\Modules\Core\Providers;

use Nwidart\Modules\Module;

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
            'dump-autoload' => 'DumpAutoloadCommand',

            'cms:install' => 'CmsInstallCommand',
            'cms:update' => 'CmsUpdateCommand',
            'cms:module:make' => 'CmsModuleMakeCommand',

            'themes:gulp' => 'ThemeGulpCommand',
            'themes:publish' => 'ThemePublishCommand',

            'module:publish-config' => 'ModulePublishConfigCommand',
        ],
    ];

    /**
     * Register view composers.
     *
     * @var array
     */
    protected $composers = [
        'Core' => [
            'CurrentRoute' => '*',
            'EditorPagedown' => ['core::editors.pagedown-bootstrap'],
            'Sidebars@left' => ['theme.*::views.partials.theme.sidebar-left'],
            'Sidebars@right' => ['theme.*::views.partials.theme.sidebar-right'],
        ],
    ];

    /**
     * Register repository bindings to the IoC.
     *
     * @var array
     */
    protected $bindings = [

    ];

    public function register()
    {
        parent::register();
        $this->registerModuleResourceNamespaces();
    }

    /**
     * Register the modules aliases.
     */
    private function registerModuleResourceNamespaces()
    {
        foreach ($this->app['modules']->enabled() as $module) {
            $this->registerViewNamespace($module);
            $this->registerLanguageNamespace($module);
            $this->registerConfigNamespace($module);
        }
    }

    /**
     * Register the view namespaces for the modules.
     *
     * @param Module $module
     */
    protected function registerViewNamespace(Module $module)
    {
        $this->app['view']->addNamespace(
            $module->getName(),
            $module->getPath().'/Resources/views'
        );
    }

    /**
     * Register the language namespaces for the modules.
     *
     * @param Module $module
     */
    protected function registerLanguageNamespace(Module $module)
    {
        $this->app['translator']->addNamespace(
            $module->getName(),
            $module->getPath().'/Resources/lang'
        );
    }

    /**
     * Register the config namespace.
     *
     * @param Module $module
     */
    private function registerConfigNamespace(Module $module)
    {
        $files = $this->app['files']->files($module->getPath().'/Config');

        $package = $module->getName();

        foreach ($files as $file) {
            $filename = $this->getConfigFilename($file, $package);

            $this->mergeConfigFrom($file, $filename);

            $this->publishes([
                $file => config_path($filename.'.php'),
            ]);
        }
    }

    /**
     * @param $file
     * @param $module
     *
     * @return string
     */
    private function getConfigFilename($file, $module)
    {
        $name = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($file));

        return sprintf('cms.%s.%s', $module, $name);
    }
}
