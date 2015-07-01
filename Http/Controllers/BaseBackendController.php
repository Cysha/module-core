<?php namespace Cms\Modules\Core\Http\Controllers;

use Cms\Modules\Core\Services\MenuService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;

class BaseBackendController extends BaseController
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
        // spawn the menu service
        app('Cms\Modules\Core\Services\MenuService')->boot();

        // reset the themeName to whatever is in the config
        $this->setTheme(config('cms.core.app.themes.backend', 'default_admin'));

        // then add the control panel stuff
        $this->addPageAssets();
    }

    public function setActions(array $actions)
    {
        $this->actions = $actions;

        $this->theme->setActions($actions);
    }

    /**
     * Determines whether we have a file in the right place for this module
     */
    public function addPageAssets()
    {
        if (!is_object(Route::current())) {
            return;
        }
        $routeName = Route::current()->getName();

        $path = sprintf('%s/themes/%s/assets/css/%s.css', public_path(), $this->themeName, $routeName);

        if (File::exists($path)) {
            $this->theme->asset()->add($routeName, str_replace(public_path().'/', '', $path), array('base'));
        }
    }

    /**
     * Will send a message back to the browser, if ajax will return as json
     *
     * @param  string  $message
     * @param  integer $status
     * @param  Request $input
     *
     * @return json|Redirect
     */
    protected function sendMessage($message, $status = 200)
    {
        if (Request::ajax()) {
            return [
                'status' => $status,
                'message' => $message,
            ];
        }

        return redirect()->back($status)->withInfo($message);
    }
}
