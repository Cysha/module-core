<?php

// Route::when(Config::get('core::routes.paths.api').'/*', 'auth.basic');

/*
|--------------------------------------------------------------------------
| View Events
|--------------------------------------------------------------------------
*/
    View::composer('theme.*::layouts.*', function ($view) {
        $currentRoute = function () {
            if (Route::currentRouteAction() === null) {
                return '';
            }

            $route = explode('@', Route::currentRouteAction());
            if (strstr($route[0], '\\')) {
                $route[0] = explode('\\', $route[0]);
                $route[0] = end($route[0]);
            }

            return strtolower(($route[0] ?: '').' '.($route[1] ?: ''));
        };
        $view->with('currentRoute', $currentRoute());
    });

/*
|--------------------------------------------------------------------------
| Form Filters
|--------------------------------------------------------------------------
*/
    Form::macro('DBSelect', function ($id, $collection, $options = ['id' => 'name']) {
        $key = key($options);
        $rows = $collection->lists($options[$key], $key);

        if (class_exists('Former')) {
            return Former::select($id)->options($rows);
        } else {
            return Form::select($id, $rows);
        }
    });

    Form::macro('Config', function ($key, $type = 'text', $default = null) {

        if (in_array($type, ['radios', 'radio'])) {
            return Former::$type($key)->check(Config::get($key, $default));
        }

        return Former::$type($key)->value(Config::get($key, $default));
    });

    /*
    |--------------------------------------------------------------------------
    | Delete form macro
    |--------------------------------------------------------------------------
    |
    | This macro creates a form with only a submit button.
    | We'll use it to generate forms that will post to a certain url with the DELETE method,
    | following REST principles.
    |
    | Call it like this: {{Form::delete('resource/'. $resource->id, 'Delete')}}
    | or pass more parameters at will
    |
    | http://laravelsnippets.com/snippets/delete-form-macro
    */
    Form::macro('toUrl', function ($method, $url, $button_label = 'Delete', $form_parameters = [], $button_options = []) {
        if (!in_array($method, ['get', 'post', 'put', 'delete', 'patch'])) {
            $method = 'get';
        }

        if (empty($form_parameters)) {
            $form_parameters = [
                'method' => Str::upper($method),
                'class'  => $method.'-form',
                'url'    => $url
            ];
        } else {
            $form_parameters['url']    = $url;
            $form_parameters['method'] = Str::upper($method);
        }

        return Form::open($form_parameters)
                . Form::submit($button_label, $button_options)
                . Form::close();
    });

/*
|--------------------------------------------------------------------------
| Validation Filters
|--------------------------------------------------------------------------
*/
    Validator::extend('alpha_spaces', function ($attribute, $value) {
        return preg_match('/^[\pL\s]+$/u', $value);
    });

/*
|--------------------------------------------------------------------------
| Application Filters
|--------------------------------------------------------------------------
*/

    /**
     * Application Permissions Filter
     *
     **/
    Route::filter('permissions', function ($route, $request, $userRule = null) {

        // no special route name passed, use the current name route
        if (is_null($userRule)) {
            $prefix = $module = $rule = null;
            $explode = explode('.', Route::current()->getName());

            if (isset($explode[0])) { $prefix = $explode[0]; }
            if (isset($explode[1])) { $module = $explode[1]; }
            if (isset($explode[2])) { $rule = $explode[2]; }

            switch ($rule) {
                case 'index':
                case 'show':
                    $userRule = $module.'.view';
                break;

                case 'create':
                case 'store':
                    $userRule = $module.'.create';
                break;

                case 'edit':
                case 'update':
                    $userRule = $module.'.update';
                break;

                case 'destroy':
                    $userRule = $module.'.delete';
                break;

                default:
                    $userRule = Route::current()->getName();
                break;
            }
        }

        if (Auth::user()->can($userRule) === false && $userRule !== 'admin.dashboard.index') {
            return Redirect::route('admin.dashboard.index')->withError(Lang::get('admin::permissions.access_denied'));
        } elseif (Auth::user()->can($userRule) === false && $userRule === 'admin.dashboard.index') {
            return Redirect::to('/')->withError(Lang::get('admin::permissions.access_denied'));
        }

    });

    /**
     * 404 Error Catching...writes to the log what url 404'd on
     */
    App::error(function (Exception $exception, $code) {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            Log::error('NotFoundHttpException Route: '.Request::url());
        }

        if (Config::get('app.debug', false) === true) {
            Log::error($exception);
        }
    });

    /**
     * Grab the database config vars, make them overload the Config
     *
     **/
    App::before(function ($request) {
        if (!Schema::hasTable('config')) {
            return;
        }

        $table = Cache::rememberForever('core.config_table', function () {
            return Cysha\Modules\Core\Models\DBConfig::orderBy('environment', 'asc')->get();
        });

        if ($table->count() == 0) {
            return;
        }

        foreach (['*', App::Environment()] as $env) {
            foreach ($table as $item) {
                // check if we have the right environment
                if ($item->environment != $env) {
                    continue;
                }

                // see if we can gather the settings info
                $key = implode('.', [$item->group, $item->item]);
                if ($item->namespace !== null) {
                    $key = $item->namespace.'::'.$key;
                }


                //fix an issue with no group on the setting
                $key = str_replace('::.', '::', $key);

                // and then override it
                Config::set($key, $item->value);
            }
        }
    });

    /**
     * Minify the html output if the environment != local
     *
     **/
    App::after(function ($request, $response) {
        if (App::Environment() == 'local') {
            return;
        }

        if ($response instanceof Illuminate\Http\Response) {
            $output = $response->getOriginalContent();
            // Clean comments
            $output = preg_replace('/<!--([^\[|(<!)].*)/', '', $output);
            $output = preg_replace('/(?<!\S)\/\/\s*[^\r\n]*/', '', $output);
            // Clean Whitespace
            $output = preg_replace('/\s{2,}/', '', $output);
            $output = preg_replace('/(\r?\n)/', '', $output);

            $response->setContent($output);
        }
    });
