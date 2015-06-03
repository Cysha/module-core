<?php namespace Cms\Modules\Core\Http\Controllers;

class BaseFrontendController extends BaseController
{

    /**
     * Controls the layout for a controller
     * @var string
     */
    public $layout = '3-column';

    public function boot()
    {
        parent::boot();

        $this->setSidebar();
    }

    public function setSidebar($set = 'default')
    {
        $this->theme->setSidebar($set);
    }
}
