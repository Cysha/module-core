<?php namespace Cysha\Modules\Core\Controllers;

use App;
use Auth;
use Config;
use File;
use Route;
use URL;
use Menu;
use Theme;

class BaseAdminController extends BaseController
{
    /**
     * Controls the layout for a controller
     * @var string
     */
    public $layout = 'default';

    /**
     * The theme object
     * @var object
     */
    public $objTheme;

    /**
     * The theme to load
     * @var string
     */
    protected $themeName = null;

    public function __construct()
    {
        // reset the themeName to whatever is in the config
        $this->themeName = Config::get('core::app.themes.backend', 'default-admin');

        // THEN do the parents constructor which will set the theme and layout
        parent::__construct();

        // then add the control panel stuff
        $this->addPageAssets();
        $this->adminMenu();
    }

    public function setActions(array $actions)
    {
        $this->actions = $actions;

        $this->objTheme->setActions($actions);
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
            $this->objTheme->asset()->add($routeName, str_replace(public_path().'/', '', $path), array('base'));
        }
    }

    /**
     * Sets up the admin panel
     */
    public function adminMenu()
    {
        $this->objTheme->breadcrumb()->add('ACP Dashboard', URL::route('pxcms.admin.index'));

        // generate the admin menu handler
        $acp = Menu::handler('acp');
        $acp->add(URL::route('pxcms.admin.index'), '<i class="fa fa-dashboard"></i> Dashboard');

        // loop through each of the menus, merge them and then process them
        $acp = array();
        $inline = array();

        foreach (App::make('modules')->modules()->items as $module) {
            $name = $module->name();

            // process any acp_menus this module might have
            if (Config::has($name.'::admin.acp_menu')) {
                foreach (Config::get($name.'::admin.acp_menu') as $section => $menu) {
                    $acp[$section] = !empty($acp[$section]) ? array_merge($acp[$section], $menu) : $menu;
                }
            }

            // process any config menus this module might have
            if (Config::has($name.'::admin.config_menu')) {
                foreach (Config::get($name.'::admin.config_menu') as $section => $menu) {
                    $inline[$section] = !empty($inline[$section]) ? array_merge($inline[$section], $menu) : $menu;
                }
            }

        }

        // add the menus to the system
        $this->processMenu($acp, 'acp');
        $this->processMenu($inline, 'acp.config_menu');
    }

    /**
     * Processes the arrays into a menu set
     *
     * @param  array  $menus
     * @param  string $handler
     * @return bool
     */
    public function processMenu(array $menus, $handler = 'acp')
    {
        if (empty($menus)) {
            return false;
        }

        $acp = Menu::handler($handler);

        foreach ($menus as $section => $link) {
            if (empty($link)) {
                continue;
            }

            if (!is_array($link) || !count($link)) {
                $this->addSection($acp, $link, $section);
                continue;
            }

            $section = trim(e($section));
            $s = Menu::items('section-'.$section);

            $children = false;
            foreach ($link as $url => $anchor) {
                $this->addSection($s, $anchor, $url);
                $children = true;
            }

            if ($children === true) {
                $acp->add('#', $section, $s);
            }
        }

        return true;
    }

    /**
     * Checks the permissions on the routes
     *
     * @param object $menu
     * @param string $link
     * @param string $section
     */
    public function addSection(&$menu, $link, $section)
    {
        if (is_number($section)) {
            $menu->add('#', $link)->addClass('divider');
        } else {
            if (Auth::user()->can($section) === false) {
                return;
            }
            $menu->add(URL::Route($section), $link);
        }
    }

}
