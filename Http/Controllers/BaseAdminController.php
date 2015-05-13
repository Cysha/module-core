<?php namespace Cms\Modules\Core\Http\Controllers;

use Route;
use File;
use Menu;
use Config;

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
        $this->setTheme(config('cms.core.app.themes.backend', 'default_admin'));

        // then add the control panel stuff
        $this->addPageAssets();
        $this->adminMenu();
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


    /**
     * Sets up the admin panel
     */
    public function adminMenu()
    {
        $this->theme->breadcrumb()->add('ACP Dashboard', route('pxcms.admin.index'));

        // generate the admin menu handler
        $acp = Menu::handler('acp');
        $acp->add(route('pxcms.admin.index'), '<i class="fa fa-dashboard"></i> Dashboard');

        // loop through each of the menus, merge them and then process them
        $acp = array();
        $inline = array();

        foreach (app('modules')->getOrdered() as $module) {
            $name = $module->getName();

            // process any acp_menus this module might have
            $configStr = sprintf('cms.%s.admin.acp_menu', $name);
            if (Config::has($configStr)) {
                foreach (config($configStr) as $section => $menu) {
                    $acp[$section] = !empty($acp[$section]) ? array_merge($acp[$section], $menu) : $menu;
                }
            }

            // process any config menus this module might have
            $configStr = sprintf('cms.%s.admin.config_menu', $name);
            if (Config::has($configStr)) {
                foreach (config($configStr) as $section => $menu) {
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
            //if (Auth::user()->can($section) === false) {
            //    return;
            //}
            $menu->add(route($section), $link);
        }
    }
}
