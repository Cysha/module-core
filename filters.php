<?php

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

    HTML::macro('nav_link', function ($route, $text, array $args = []) {
        $class = '';
        $is_active = false;

        // check if supplied route is a named route or text url
        if (Route::getRoutes()->hasNamedRoute($route)) {
            // create the href value
            $href = route($route, $args);

            // set whether the supplied route is the current url
            $is_active = ($href == Request::url());
        } else {
            // create the href value
            $href = url($route);

            // check whether the supplied url is the current on or a child of
            $is_active = Request::is($route . '*');
        }

        if ($is_active) {
            $class = ' class="active"';
        }

        return '<li' . $class . '>' . \HTML::linkRoute($route, $text, $args) . '</li>';
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
