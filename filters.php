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

    App::error(function (Symfony\Component\HttpKernel\Exception\HttpException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');
        return $objTheme->scope('partials.theme.errors.'.$code, compact('code'))->render($code);
    });

    App::error(function (Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, 'Authentication is required to access this resource.']);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');
        return $objTheme->scope('partials.theme.errors.401', compact('code'))->render($code);
    });

    App::error(function (Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, 'A request was made to a resource using an unsupported request method.']);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');
        return $objTheme->scope('partials.theme.errors.405', compact('code'))->render($code);
    });

    App::error(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, $code) {
        if (Config::get('app.debug', false) === true) {
            Log::error('URL Not Found: '.Request::url());
        }
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');
        return $objTheme->scope('partials.theme.errors.404', compact('code'))->render($code);
    });

    App::error(function (Illuminate\Database\Eloquent\ModelNotFoundException $exception, $code) {
        if (Config::get('app.debug', false) === true) {
            Log::error('URL Not Found: '.Request::url());
        }
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');
        return $objTheme->scope('partials.theme.errors.404', compact('code'))->render($code);
    });

    App::error(function (\Cysha\Modules\Core\Helpers\Forms\FormValidationException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        return Redirect::back()->withInput()->withErrors($exception->getErrors())->withError(Lang::get('core::forms.validation.message'));
    });

    App::error(function (\Cysha\Modules\Core\Helpers\Forms\FormUnauthorizedException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        return Redirect::back()->withInput()->withError(Lang::get('core::forms.authorization.message'));
    });

    App::error(function (Exception $exception, $code) {
        if (Config::get('app.debug', false) === true) {
            Log::error($exception);
            return;
        }

        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');

        return $objTheme->scope('partials.theme.errors.whoops', compact('code'))->render($code);
    });

    /** http://www.laravel-tricks.com/tricks/using-appbefore-to-trapcatch-pdoexception-errors */
    App::before(function ($request, $response) {

        App::error(function (\PDOException $e, $code) {

            Log::error('FATAL DATABASE ERROR: ' . $code . ' = ' . $e->getMessage());

            if ((bool)\Config::get('app.debug', false) === true) {

                $message = explode(' ', $e->getMessage());
                $dbCode = rtrim($message[1], ']');
                $dbCode = trim($dbCode, '[');

                // codes specific to MySQL
                switch ($dbCode) {
                    case 1049:
                        $userMessage = 'Unknown database - probably config error:';
                    break;
                    case 2002:
                        $userMessage = 'DATABASE IS DOWN:';
                    break;
                    case 1045:
                        $userMessage = 'Incorrect DB Credentials:';
                    break;
                    default:
                        $userMessage = 'Untrapped Error:';
                    break;
                }
                $userMessage = $userMessage . '<br>' . $e->getMessage();
            } else {
                // be apologetic but never specific ;)
                $userMessage = 'We are currently experiencing a site wide issue. We are sorry for the inconvenience!';
            }

            $objTheme = Theme::uses(Config::get('core::app.themes.frontend', 'default'))->layout('col-1');
            return $objTheme->scope('partials.theme.errors.whoops', [
                'code' => $code,
                'message' => $userMessage,
            ])->render($code);
        });

    });

    /**
     * Check for the force-secure setting to see if we want HTTPS enforced
     *
     **/
    App::before(function ($request) {
        if (!Request::secure() && (bool)\Config::get('core::app.force-secure', false) === true) {
            return Redirect::secure(Request::path());
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

                // and then override it
                Config::set($item->key, $item->value);
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
            if (!is_string($output)) {
                return;
            }

            // Clean comments
            $output = preg_replace('/<!--([^\[|(<!)].*)/', '', $output);
            $output = preg_replace('/(?<!\S)\/\/\s*[^\r\n]*/', '', $output);
            // Clean Whitespace
            $output = preg_replace('/\s{2,}/', '', $output);
            $output = preg_replace('/(\r?\n)/', '', $output);

            $response->setContent($output);
        }
    });
