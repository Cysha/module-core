<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

use URL;
use Config;

class SiteController extends BaseConfigController
{
    public function getIndex()
    {
        $this->objTheme->setTitle('Configuration Manager');
        $this->objTheme->breadcrumb()->add('Site Config', URL::route('admin.config.index'));

        $indexRoutes = [];
        $modules = app('modules')->modules();
        if (count($modules)) {
            foreach ($modules as $module) {
                if (!$module->enabled()) {
                    continue;
                }

                $configStr = $module->name().'::module.pxcms-index';
                if (Config::has($configStr)) {
                    $indexRoutes[Config::get($configStr)] = Config::get($configStr);
                }
            }
        }

        return $this->setView('config.admin.index', [
            'indexRoutes' => $indexRoutes,
        ], 'module');
    }

}
