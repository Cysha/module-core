<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

use URL;

class SiteController extends BaseConfigController
{
    public function getIndex()
    {
        $this->objTheme->setTitle('Configuration Manager');
        $this->objTheme->breadcrumb()->add('Site Config', URL::route('admin.config.index'));

        return $this->setView('config.admin.index', array(), 'module');
    }

}
