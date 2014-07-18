<?php namespace Cysha\Modules\Core\Controllers;

// use Illuminate\Routing\Controller;
use Dingo\Api\Routing\Controller;
use Debugbar;
use Theme;
use Config;
use File;
use Str;
use API;

class BaseController extends Controller
{

    /**
     * Controls the layout for a controller
     * @var string
     */
    public $layout = 'basic';

    /**
     * The theme object
     * @var object
     */
    protected $objTheme;

    /**
     * The theme to load
     * @var string
     */
    protected $themeName = null;

    /**
     * The currently loaded module
     * @var string
     */
    protected $module = null;

    /**
     * The view path to load
     * @var string
     */
    private $view = null;

    /**
     * Populates the view with contents
     * @var string
     */
    private $data = array();

    /**
     * The type of view, module, theme, app etc
     *
     * @var string
     */
    private $type = null;

    public function __construct()
    {
        // set some theme options up
        $this->objTheme = Theme::uses(Config::get('app.theme', 'default'))->layout($this->layout);
        $this->themeName = Config::get('app.theme', 'default');

        // figure out which module we are currently in
        $this->module = $this->getModule($this);

        // start a debug timer going
        Debugbar::startMeasure('module_timer', 'Module Run');
    }

    public function __destruct()
    {
        Debugbar::stopMeasure('module_timer');
    }

    /**
     * Gets the current modules name, presuming this is a CMS Module
     *
     * @param  object $class
     * @return string
     */
    public function getModule($class)
    {
        $namespace = get_class($class);
        $module = explode('\\', $namespace);

        return $module[2];
    }

    /**
     * Verifies the theme exists before being set
     *
     * @param  string $theme
     * @return bool
     */
    public function setTheme($theme = null)
    {
        if ($theme === null) {
            return false;
        }

        $themeFile = sprintf('%s/themes/%s/', public_path(), $theme);
        if (File::exists($themeFile)) {
            $this->themeName = $theme;
            $this->objTheme->uses($theme);

            return true;
        }

        return false;
    }
    /**
     * Verifies the layout exists before being set
     *
     * @param  string $layout
     * @return bool
     */
    public function setLayout($layout = null)
    {
        if ($layout === null) {
            return false;
        }

        $layoutFile = sprintf('%s/themes/%s/layouts/%s.blade.php', public_path(), $this->themeName, $layout);
        if (File::exists($layoutFile)) {
            $this->layout = $layout;
            $this->objTheme->layout($layout);

            return true;
        }

        return false;
    }

    /**
     * Returns a valid namespace string for the module
     *
     * @param  string $var    The config var
     * @param  string $module Overloads the module to use this name instead
     * @return string
     */
    public function moduleNamespace($var = null, $module = null)
    {
        $module = strtolower($module === null ? $this->module : $module);

        if ($var !== null) {
            return $module.'::'.$var;
        }

        return $module;
    }

    /**
     * Returns a config variable for this module
     *
     * @param  string $key
     * @param  string $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return Config::get($this->moduleNamespace($key), $default);
    }

    /**
     * Determines where to load the view from
     *
     * @param string $view Path to the view file
     * @param array  $data Data to pass through to the view
     * @param string $type Where the view is [Module, Theme, App, Partial, Custom]
     */
    public function setView($view, $data = array(), $type = 'module')
    {
        $type = strtolower($type);
        $supportedTypes = array('theme', 'app', 'module', 'partial', 'custom');

        if (!in_array($type, $supportedTypes) && substr($type, 0, 6) !== 'module') {
            $type = 'watch';
        } else {
            switch ($type) {
                case 'theme':
                    $type = 'scope';
                    break;

                case 'app':
                    $type = 'of';
                    break;

                case 'partial':
                    $type = 'partial';
                    break;

                case 'custom':
                    $type = 'load';
                    break;

                default:
                case ($type === 'module' || substr($type, 0, 6) === 'module'):
                    $module = null;
                    if (Str::contains($type, ':')) {
                        $type = explode(':', $type);
                        $module = $type[1];
                        $type = $type[0];
                    }

                    $type = 'of';
                    $view = $this->moduleNamespace($view, $module);
                    break;
            }
        }

        $this->view = $view;
        $this->data = $data;
        $this->type = $type;

        if ($type === 'partial') {
            return $this->objTheme->$type($this->view, $this->data);
        }

        return $this->objTheme->$type($this->view, $this->data)->render();
    }

    public function api($method, $route, $data = array())
    {
        $request = API::$method($route, $data);

        if ($request['status'] !== 200) {
            throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException($request['message']);
        }

        return $request;
    }

    public function outputMethod()
    {
        $call = debug_backtrace(); //presume last call?

        $class = $call[1]['class'];
        $method = $call[1]['function'];
        $method = new \ReflectionMethod($class, str_replace(array($class, '::'), '', $method));

        $filename = $call[0]['file'];
        $start_line = $method->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
        $end_line = $method->getEndLine();
        $length = $end_line - $start_line;

        $source = file($filename);
        $body = implode("", array_slice($source, $start_line, $length));

        echo '<pre><code>',print_r($body, true),'</code></pre>';
        echo '<hr />';
    }
}
