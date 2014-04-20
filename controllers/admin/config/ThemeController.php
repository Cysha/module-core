<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

use Cysha\Modules\Core as Core;

class ThemeController extends BaseConfigController
{
    public function getIndex()
    {
        $this->theme->setTitle('Theme Manager');
        $this->theme->breadcrumb()->add('Theme Manager', $this->url->route('admin.theme.index'));

        return $this->setView('config.admin.theme', array(), 'module');
    }

}
