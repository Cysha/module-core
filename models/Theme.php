<?php namespace Cysha\Modules\Core\Models;

use File;
use Config;

class Theme
{
    protected static $themes = array();

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
        self::gatherInfo();
        return self::$themes;
    }

    public static function getFrontend()
    {
        self::gatherInfo();
        return array_filter(self::$themes, function ($theme) {
            return $theme->type == 'frontend';
        });
    }

    public static function getBackend()
    {
        self::gatherInfo();
        return array_filter(self::$themes, function ($theme) {
            return $theme->type == 'backend';
        });
    }

    public static function getLayouts()
    {
        $theme = self::themeInfo(Config::get('core::app.themes.frontend', 'default'));
        $dir = key($theme);

        if (!File::isDirectory($dir)) {
            return [];
        }

        $files = File::glob($dir.'/layouts/*.blade.php');
        $files = array_map(function ($filename) {
            $fn = explode('/', $filename);
            return str_replace('.blade.php', '', end($fn));
        }, $files);

        return array_combine($files, $files) ?: [];
    }

    private static function themeInfo($name)
    {
        self::gatherInfo();
        return array_filter(self::$themes, function ($theme) use ($name) {
            return $theme->dir == $name;
        });
    }
}
