<?php namespace Cysha\Modules\Core\Controllers\Admin\Config;

class SiteController extends BaseConfigController
{
    public function getIndex()
    {
        $this->theme->setTitle('Configuration Manager');
        $this->theme->breadcrumb()->add('Site Config', $this->url->route('admin.config.index'));

        return $this->setView('config.admin.index', array(), 'module');
    }

}
