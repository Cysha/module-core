<?php

namespace Cms\Modules\Core\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use JsonSerializable;

class Module implements Arrayable, Jsonable, JsonSerializable
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
        self::$modules = new Collection(self::$modules);

        $empty = [
            'order' => 0,
            'name' => null,
            'alias' => null,
            'authors' => [],
            'version' => null,
            'keywords' => [],
            'active' => false,
            'path' => null,
        ];

        foreach (app('modules')->toCollection() as $module) {
            $directory = $module->getPath();
            $moduleName = $module->getName();

            if (!File::exists($directory.'/module.json')) {
                self::$modules->push(
                    array_merge($empty, ['name' => $moduleName, 'path' => $directory])
                );
                continue;
            }
            $module = json_decode(file_get_contents($directory.'/module.json'));

            self::$modules->push([
                'order' => (int) $module->order,
                'name' => $module->name,
                'alias' => $module->alias,
                'authors' => $module->authors,
                'version' => $module->version,
                'keywords' => (array) $module->keywords,
                'active' => $module->active == '1' ? true : false,
                'path' => $directory,
            ]);
        }

        self::$modules = self::$modules->sortBy('order');
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
     * Returns info about a single module.
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
            throw new ModelNotFoundException();
        }

        return $filter;
    }

    public function toArray()
    {
        return self::$modules->toArray();
    }
    public function toJson($options = 0)
    {
        return self::$modules->toJson($options);
    }
    public function jsonSerialize()
    {
        return self::$modules->jsonSerialize();
    }
}
