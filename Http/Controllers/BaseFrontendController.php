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
        // spawn the menu service
        app('Cms\Modules\Core\Services\MenuService')->boot();

        // set the sidebar
        $this->setSidebar();
    }

    public function setSidebar($set = 'default')
    {
        $this->theme->setSidebar($set);
    }
}
