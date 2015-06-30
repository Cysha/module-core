<?php namespace Cms\Modules\Core\Services;

use Illuminate\Contracts\Config\Repository as Config;
use Pingpong\Modules\Repository as Module;
use Menu;
use Lock;

class MenuService
{
   /**
     * @var Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var Modules
     */
    protected $modules;

    public function __construct(Config $config, Module $modules)
    {
        $this->config = $config;
        $this->modules = $modules;
    }

    /**
     * Registers the menus
     */
    public function boot()
    {
        $menus = $this->getMenuData();
        if (empty($menus)) {
            return;
        }

        // run over them with a good sorting
        foreach ($menus as $key => $menu) {
            $menus[$key] = $this->sortMenu($menus[$key]);
        }


        // and then process em
        foreach (array_keys($menus) as $key) {
            $this->processMenu($menus[$key], $key);
        }
    }

    public function sortMenu($menus)
    {

        // sort the parent categories
        uasort($menus, function ($a, $b) {
            return array_get($a, 'order', 1) > array_get($b, 'order', 1);
        });

        // run over the menus
        foreach ($menus as $key => $menu) {
            if (isset($menu['children']) && is_array($menu['children'])) {
                $menus[$key] = $this->sortMenu($menus[$key]['children']);
            }
        }

        return $menus;
    }

    /**
     * Get the menu data from the configs
     */
    public function getMenuData()
    {
        if (cache_has('menu', 'cms.menu.processing')) {
            $menu = cache_get('menu', 'cms.menu.processing', []);
            if (!empty($menu)) {
                return $menu;
            }
            cache_flush('menu', 'cms.menu.processing');
        }

        return cache('menu', 'cms.menu.processing', 10, function () {
            $menus = [];
            // loop through each of the menus, merge them into the menus arr
            foreach (get_array_column(config('cms'), 'menus') as $moduleName => $moduleMenu) {
                $module = app('modules')->find($moduleName);
                // quick check to make sure the module isnt enabled
                if ($module === null || !$module->enabled()) {
                    continue;
                }

                foreach ($moduleMenu as $section => $menu) {
                    $menus[$section] = !empty($menus[$section]) ? array_merge_recursive($menus[$section], $menu) : $menu;
                }
            }

            return $menus;
        });
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

            if ($section === '_root') {
                foreach ($links as $info) {
                    if (!is_array($info)) {
                        continue;
                    }

                    // add them directly to the main instance
                    $this->addSection($menuInstance, $info);
                }
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
     *
     * @return bool
     */
    private function addSection(&$menu, $link)
    {
        if (($type = array_get($link, 'type', null)) === 'divider') {
            $menu->add('#', array_get($link, 'text'))->addClass('divider');
            return true;
        }

        // check for permissions on this link
        if (($perm = array_get($link, 'permission', null)) !== null && hasPermission($perm) === false) {
            \Debug::console(['Permission Denied', $perm]);
            return false;
        }

        // figure out where to link this nav item to
        $url = '#';
        if (($route = array_get($link, 'route', null)) !== null) {

            // if its an array throw it at route()
            if (is_array($route)) {
                list($route, $arguments) = $route;

                $url = route($route, transform_button_args($arguments));
            } else {
                // else just call it normally
                $url = route($route);
            }

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
