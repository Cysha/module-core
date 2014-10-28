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

