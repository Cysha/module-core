<?php namespace Cms\Modules\Core\Http\Controllers;

use Route;
use File;
use Menu;
use Config;
use Lock;

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
     * Sets up the admin panel
     */
    public function adminMenu()
    {
        $this->theme->breadcrumb()->add('ACP Dashboard', route('pxcms.admin.index'));

        // generate the admin menu handler
        $acp = Menu::handler('acp');
        $acp->add(route('pxcms.admin.index'), '<i class="fa fa-dashboard"></i> Dashboard');

        // loop through each of the menus, merge them and then process them
        $acp = [];
        $inline = [];

        foreach (app('modules')->getOrdered() as $module) {
            $name = $module->getName();

            // process any acp_menus this module might have
            $configStr = sprintf('cms.%s.menus.acp_menu', $name);
            if (Config::has($configStr)) {
                foreach (config($configStr) as $section => $menu) {
                    $acp[$section] = !empty($acp[$section]) ? array_merge($acp[$section], $menu) : $menu;
                }
            }

            // process any config menus this module might have
            $configStr = sprintf('cms.%s.menus.config_menu', $name);
            if (Config::has($configStr)) {
                foreach (config($configStr) as $section => $menu) {
                    $inline[$section] = !empty($inline[$section]) ? array_merge($inline[$section], $menu) : $menu;
                }
            }
        }

        // sort the menu items
        foreach ($acp as $section => $items) {
            usort($acp[$section], function ($a, $b) {
                return array_get($a, 'order', 1)>array_get($b, 'order', 1);
            });
        }
        usort($inline, function ($a, $b) {
            return array_get($a, 'order', 1)>array_get($b, 'order', 1);
        });

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

        // create a handler for this menu
        $menuInstance = Menu::handler($handler);

        // roll over the menu links
        foreach ($menus as $section => $links) {
            if (empty($links)) {
                continue;
            }

            // create a new subHandler
            $subHandler = Menu::items($handler.'-'.trim(e($section)));

            // if the links arent part of a sub menu
            if (is_number($section)) {
                // add them directly to the main instance
                $this->addSection($menuInstance, $links);
                continue;
            }

            // check see if the links are in a sub menu
            $children = false;
            foreach ($links as $info) {
                // make sure everything happened as expected
                if ($this->addSection($subHandler, $info)) {
                    $children = true;
                }
            }

            // make sure we have something to add
            if ($children === true) {
                // add the sub menu to the main instance
                $menuInstance->add('#', $section, $subHandler);
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
    public function addSection(&$menu, $link)
    {
        //echo \Debug::dump(func_get_args(), __METHOD__);
        if (($type = array_get($link, 'type', null)) === 'divider') {
            $menu->add('#', array_get($link, 'text'))->addClass('divider');
            return true;
        }

        // check for permissions on this link
        if (($perm = array_get($link, 'permission', null)) !== null && strpos($perm, '@') !== false) {
            $perm = explode('@', $perm);
            if (Lock::cannot($perm[0], $perm[1])) {
                return false;
            }
        }

        // figure out where to link this nav item to
        $url = '#';
        if (($route = array_get($link, 'route', null)) !== null) {
            $url = route($route);
        } elseif (($direct = array_get($link, 'url', null)) !== null) {
            $url = $direct;
        }

        // add the text and if needed an icon
        $text = array_get($link, 'text');
        if (($icon = array_get($link, 'icon', null)) !== null) {
            $text = sprintf('<i class="fa fa-fw %s"></i> %s', $icon, $text);
        }

        // add the item to the menu
        $menu->add($url, $text);

        // if we have an activePattern, replace any identifiers, and add it
        if (($activePattern = array_get($link, 'activePattern', null)) !== null) {
            $activePattern = str_replace(
                ['{api}', '{frontend}', '{backend}'],
                array_map(function ($ele) {
                    return substr($ele, 0, -1);
                }, config('cms.core.app.paths')),
                $activePattern
            );

            $menu->activePattern($activePattern);
        }
        return true;
    }
}
