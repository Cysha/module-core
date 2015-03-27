<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

use URL;
use Config;

class SiteController extends BaseConfigController
{
    public function getIndex()
    {
        $this->objTheme->setTitle('Configuration Manager');
        $this->objTheme->breadcrumb()->add('Site Config', URL::route('admin.config.index'));

        return $this->setView('config.admin.index', [
            'indexRoutes' => $this->getIndexRoutes(),
        ], 'module');
    }



    private function getIndexRoutes()
    {
        // grab a list of all the index routes
        $indexRoutes = [];

        // grab the module list
        $modules = app('modules')->modules();
        if (count($modules)) {
            foreach ($modules as $module) {
                // make sure the module is enabled
                if (!$module->enabled()) {
                    continue;
                }

                // test for the pre-defined config string
                $configStr = $module->name().'::module.pxcms-index';
                if (Config::has($configStr)) {
                    $configVar = Config::get($configStr);

                    // add it to an array if not already
                    if (!is_array($configVar)) {
                        $configVar = [$configVar];
                    }

                    // assign info to vars
                    $route = key($configVar);
                    $name = head($configVar);

                    // if route is numeric, means we dont have a human readable name
                    if (is_numeric($route)) {
                        $route = $name;
                        $name = 'Homepage Route';
                    }

                    // add this route to the array to pass back
                    $indexRoutes = array_merge($indexRoutes, [$route => '['.$module->name().'] '.$name]);
                }
            }
        }
        return $indexRoutes;
    }
}
