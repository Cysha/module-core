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
                    $value = Config::get($configStr);
                    if (!is_array($value)) {
                        $value = [$value => $value];
                    }

                    $indexRoutes = array_merge($indexRoutes, $value);
                }
            }
        }

        return $this->setView('config.admin.index', [
            'indexRoutes' => $indexRoutes,
        ], 'module');
    }

}
