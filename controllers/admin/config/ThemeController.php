<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

use Cysha\Modules\Core as Core;
use URL;

class ThemeController extends BaseConfigController
{
    public function getIndex()
    {
        $this->objTheme->setTitle('Theme Manager');
        $this->objTheme->breadcrumb()->add('Theme Manager', URL::route('admin.theme.index'));

        return $this->setView('config.admin.theme', array(), 'module');
    }

}
