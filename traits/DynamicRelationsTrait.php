<?php namespace Cysha\Modules\Core\Traits;

trait DynamicRelationsTrait
{

    /**
     * This function will be called when a function is missing on this model
     * Oh the magic.gif here XD
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $relations = [];

        $class = class_basename($this);

        // cycle through all the modules
        foreach (\File::directories(app_path().'/modules/') as $dir) {
            $module = last(explode(DIRECTORY_SEPARATOR, $dir));

            $config = sprintf('%1$s::models', $module);
            if (\Config::has($config)) {
                $relations = array_merge($relations, \Config::get($config));
            }
        }

        // if we dont have anything here, just return
        if (empty($relations)) {
            return parent::__call($method, $parameters);
        }

        // grab the module & model
        list(,,$module,,$model) = explode('\\', get_class($this));

        // check to see if any of the modules are trying to be clever
        $function = array_get($relations, sprintf('%s.%s.%s', $module, $model, $method), false);
        if ($function !== false) {
            return $function($this);
        }

        // No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }
}
