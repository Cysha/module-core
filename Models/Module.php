<?php namespace Cms\Modules\Core\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Module
{
    protected static $modules = [];

    /**
     * Retrieves all the info required from the modules.
     */
    public static function gatherInfo()
    {
        if (count(self::$modules)) {
            return;
        }

        foreach (app('modules')->toCollection() as $module) {
            $directory = $module->getPath();
            $moduleName = $module->getName();

            $module = json_decode(file_get_contents($directory.'/module.json'));

            self::$modules[] = (object)[
                'order' => (int) $module->order,
                'name' => $module->name,
                'alias' => $module->alias,
                'authors' => $module->authors,
                'version' => $module->version,
                'keywords' => $module->keywords,
                'active' => $module->active == '1' ? true : false,
                'path' => $directory,
            ];
        }

        uasort(self::$modules, function ($a, $b) {
            return $a->order > $b->order ? 1 : -1;
        });
        self::$modules = new Collection(self::$modules);
    }

    /**
     * Returns all the info.
     *
     * @return array
     */
    public static function all()
    {
        self::gatherInfo();
        return self::$modules;
    }

    public static function findOrFail($name)
    {
        return self::moduleInfo($name);
    }

    /**
     * Returns info about a single module
     *
     * @return array
     */
    private static function moduleInfo($name)
    {
        self::gatherInfo();

        if (!count(self::$modules)) {
            return [];
        }

        $filter = self::$modules->filter(function ($module) use ($name) {
            if (!isset($module->alias)) {
                return false;
            }
            return $module->alias === strtolower($name);
        })->first();

        if (empty($filter)) {
            throw new ModelNotFoundException;
        }

        return $filter;
    }

}
