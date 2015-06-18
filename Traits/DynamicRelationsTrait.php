<?php namespace Cms\Modules\Core\Traits;

trait DynamicRelationsTrait
{

    /**
     * This function will be called when a function is missing on this model
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $currentClass = class_basename($this);
        // grab the module & model
        list(,,$currentModule,,$currentModel) = explode('\\', get_class($this));

        $modelConfig = get_array_column(config('cms'), 'models');
        // if we dont have anything here, just return
        if (empty($modelConfig)) {
            return parent::__call($method, $parameters);
        }

        $newFunctions = [];
        // loop over the $modelConfig as $modules
        foreach ($modelConfig as $modules) {
            foreach ($modules as $module => $models) {
                foreach ($models as $model => $functions) {
                    foreach ($functions as $fname => $function) {
                        $newFunctions[$model.'_'.$fname] = $function;
                    }
                }
            }
        }

        // check to see if any of the modules are trying to be clever
        $function = false;
        if (array_key_exists($currentClass.'_'.$method, $newFunctions)) {
            $function = $newFunctions[$currentClass.'_'.$method];
        }

        if ($function !== false) {
            $params = [$this];
            foreach ($parameters as $p) {
                $params[] = $p;
            }

            // test if $function is serialized
            $test_unserialize = unserialize($function);
            if (is_object($test_unserialize)) {
                $function = $test_unserialize;
            }

            // we do run run run
            return call_user_func_array($function, $params);
        }

        // No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }
}
