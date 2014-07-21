<?php namespace Cysha\Modules\Core\Models;

use File;

class Theme
{
    protected static $themes = array();

    public function __construct()
    {
        self::gatherInfo();
    }

    public static function gatherInfo()
    {
        if (count(self::$themes)) {
            return;
        }

        // get a list of theme directories
        $directories = File::directories(public_path().'/themes/');
        foreach ($directories as $dir) {

            if (!File::isFile($dir.'/config.php')) {
                continue;
            }
            $options = (include($dir.'/config.php'));


            $options['dir'] = str_replace('\\', '/', $dir);
            $options['dir'] = explode('/', $options['dir']);
            $options['dir'] = end($options['dir']);

            self::$themes[$dir] = (object)array_only($options, ['name', 'author', 'site', 'type', 'dir', 'version']);
        }

    }

    public static function all()
    {
        return self::$themes;
    }

    public static function getFrontend()
    {
        return array_filter(self::$themes, function ($theme) {
            return $theme->type == 'frontend';
        });
    }

    public static function getBackend()
    {
        return array_filter(self::$themes, function ($theme) {
            return $theme->type == 'backend';
        });
    }

    private static function themeInfo($name)
    {
        return isset(self::$themes[ $name ]) ? self::$themes[ $name ] : false;
    }
}
