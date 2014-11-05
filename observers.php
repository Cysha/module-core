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


Event::listen('core.errors.api', function($exception, $code, $message=null) {
    if (Request::is(Config::get('core::routes.paths.api', 'api').'/*')) {
        $return = [
            'message'       => !empty($message) ? $message : $exception->getMessage(),
            'status_code'   => $code,
        ];

        if (Config::get('app.debug') === true) {
            $return['exception'] = $exception->getMessage();
        }

        return Response::json($return, $code);
    }

    return false;
});