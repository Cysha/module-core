<?php namespace Cysha\Modules\Core;

use Config;
use URL;
use Menu;

class BaseAdminController extends BaseController
{
    /**
     * Controls the layout for a controller
     * @var string
     */
    public $layout = 'default';

    /**
     * The theme to load
     * @var string
     */
    protected $themeName = null;

    protected $menu;
    protected $app;

    public function __construct(App $app, Menu $menu)
    {

        $this->menu = $menu;
        $this->app = $app;

        // reset the themeName to whatever is in the config
        $this->themeName = Config::get('core::app.themes.backend', 'default-admin');

        // THEN do the parents constructor which will set the theme and layout
        parent::__construct();

        // then add the control panel stuff
        $this->addPageAssets();
        $this->adminMenu();
    }

    public function setTitle($title)
    {
        $this->title = $title;
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
        $this->theme->breadcrumb()->add('ACP Dashboard', $this->url->route('pxcms.admin.index'));

        // generate the admin menu handler
        $acp = $this->menu->handler('acp');
        $acp->add($this->url->route('pxcms.admin.index'), '<i class="fa fa-dashboard"></i> Dashboard');

        // loop through each of the menus, merge them and then process them
        $acp = array();
        $inline = array();

        foreach ($this->app->make('modules')->modules()->items as $module) {
            $name = $module->name();

            // process any acp_menus this module might have
            if (Config::has($name.'::admin.acp_menu')) {
                foreach (Config::get($name.'::admin.acp_menu') as $section => $menu) {
                    $acp[$section] = isset($acp[$section]) ? array_merge($acp[$section], $menu) : $menu;
                }
            }

            // process any config menus this module might have
            if (Config::has($name.'::admin.config_menu')) {
                foreach (Config::get($name.'::admin.config_menu') as $section => $menu) {
                    $inline[$section] = isset($inline[$section]) ? array_merge($inline[$section], $menu) : $menu;
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

        $acp = $this->menu->handler($handler);

        foreach ($menus as $section => $link) {
            // single item
            if (!is_array($link) || !count($link)) {
                $this->addSection($acp, $link, $section);
                continue;
            }

            $section = trim(e($section));
            $s = $this->menu->items('section-'.$section);

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
    public function addMenuSection(&$menu, $link, $section)
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