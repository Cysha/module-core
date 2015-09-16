<?php

    Form::macro('DBSelect', function ($id, $collection, $options = ['id' => 'name']) {
        $key = key($options);
        $rows = $collection->lists($options[$key], $key);

        return Former::select($id)->options($rows);
    });

    Form::macro('Config', function ($key, $type = 'text', $default = null) {
        if (in_array($type, ['radios', 'radio'])) {
            return Former::$type($key)->check(config($key, $default));
        }

        return Former::$type($key)->value(config($key, $default));
    });


    HTML::macro('nav_link', function ($route, $text, array $args = []) {
        $class = '';
        $action = Route::current();
        $action = $action->getAction();

        if (isset($action['as'])) {
            $class = $action['as'] === $route ? ' class="active"' : '';
        }

        return '<li' . $class . '>' . HTML::linkRoute($route, $text, $args) . '</li>';
    });
