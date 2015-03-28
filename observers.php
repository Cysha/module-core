<?php

Event::listen('core.config.saved', function ($key, $value) {
    if ($key !== 'core::app.debug') {
        return;
    }

    $path = Config::get('core::app.debugfile');
    if ($value == 'true') {
        File::put($path, 'DEBUG Enabled - '.time());
    } else {
        File::delete($path);
    }
});


Event::listen('core.errors.api', function ($exception, $code, $message=null) {
    if (Request::is(Config::get('core::routes.paths.api', 'api').'/*')) {
        $return = [
            'message'       => !empty($message) ? $message : $exception->getMessage(),
            'status_code'   => $code,
        ];

        if (Config::get('app.debug') === true) {
            $return['exception'] = $exception->getMessage();
        }

        return $return;
    }

    return false;
});



    App::error(function (Symfony\Component\HttpKernel\Exception\HttpException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
        return $objTheme->scope('partials.theme.errors.'.$code, compact('code'))->render($code);
    });

    App::error(function (Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, 'Authentication is required to access this resource.']);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
        return $objTheme->scope('partials.theme.errors.401', compact('code'))->render($code);
    });

    App::error(function (Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $exception, $code) {
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, 'A request was made to a resource using an unsupported request method.']);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
        return $objTheme->scope('partials.theme.errors.405', compact('code'))->render($code);
    });

    App::error(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, $code) {
        if (\Config::get('app.debug', false) === true) {
            Log::error('URL Not Found: '.Request::url());
        }
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
        return $objTheme->scope('partials.theme.errors.404', compact('code'))->render($code);
    });

    App::error(function (Illuminate\Database\Eloquent\ModelNotFoundException $exception, $code) {
        if (\Config::get('app.debug', false) === true) {
            Log::error('URL Not Found: '.Request::url());
        }
        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
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
        if (\Config::get('app.debug', false) !== false) {
            Log::error($exception);
            return;
        }

        $apiCheck = \Event::fire('core.errors.api', [$exception, $code, $exception->getMessage()]);
        if (is_array($apiCheck) && !empty($apiCheck)) {
            return Response::json(head($apiCheck), $code);
        }

        $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
        return $objTheme->scope('partials.theme.errors.whoops', [
            'code' => $code,
            'message' => $exception->getMessage(),
        ])->render($code);
    });

    /** http://www.laravel-tricks.com/tricks/using-appbefore-to-trapcatch-pdoexception-errors */
    App::before(function ($request, $response) {

        App::error(function (\PDOException $e, $code) {

            Log::error('FATAL DATABASE ERROR: ' . $code . ' = ' . $e->getMessage());

            if (\Config::get('app.debug', false) === true) {

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

            $objTheme = Theme::uses(getCurrentTheme())->layout('col-1');
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
        if (Request::secure() === false && (bool) \Config::get('core::app.force-secure', false) === true) {
            return Redirect::secure(Request::path());
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
