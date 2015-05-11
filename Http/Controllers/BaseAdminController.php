<?php namespace Cms\Modules\Core\Http\Controllers;

use Route;
use File;

class BaseAdminController extends BaseController
{
    /**
     * Controls the layout for a controller
     * @var string
     */
    public $layout = '1-column';

    /**
     * The theme object
     * @var object
     */
    public $theme;

    /**
     * The theme to load
     * @var string
     */
    protected $themeName = null;

    public function boot()
    {
        // reset the themeName to whatever is in the config
        $this->setTheme(config('core::app.themes.backend', 'default_admin'));

        // then add the control panel stuff
        $this->addPageAssets();
        //$this->adminMenu();
    }

    public function setActions(array $actions)
    {
        $this->actions = $actions;

        $this->theme->setActions($actions);
    }

    /**
     * Determines whether we have a file in the right place for this module
     *
     */
    public function addPageAssets()
    {
        $routeName = Route::current()->getName() ?: null;

        $path = sprintf('%s/themes/%s/assets/css/%s.css', public_path(), $this->themeName, $routeName);

        if (File::exists($path)) {
            $this->theme->asset()->add($routeName, str_replace(public_path().'/', '', $path), array('base'));
        }
    }

}
