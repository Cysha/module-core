<?php

namespace Cms\Modules\Core\Http\Controllers;

class BaseFrontendController extends BaseController
{
    /**
     * Controls the layout for a controller.
     *
     * @var string
     */
    public $layout = '3-column';

    public $sidebar = null;

    public function boot()
    {
        // spawn the menu service
        app('Cms\Modules\Core\Services\MenuService')->boot();

        // set the sidebar
        if ($this->sidebar === null) {
            $this->setSidebar('default');
        }

        parent::boot();
    }

    public function setSidebar($set = 'default')
    {
        $this->sidebar = $set;
        $this->theme->setSidebar($set);
    }
}
