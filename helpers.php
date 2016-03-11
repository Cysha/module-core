<?php

if (!function_exists('artisan_call')) {
    function artisan_call($command, array $parameters = array())
    {
        $buffer = new \Symfony\Component\Console\Output\BufferedOutput();

        \Artisan::call($command, $parameters, $buffer);

        return $buffer->fetch();
    }
}

if (!function_exists('is_number')) {
    function is_number($number)
    {
        return ctype_digit((string) $number);
    }
}

if (!function_exists('truncate')) {
    /* http://stackoverflow.com/a/9219884 */
    function truncate($text, $chars = 25)
    {
        $text = $text.' ';
        $text = substr($text, 0, $chars);
        $text = substr($text, 0, strrpos($text, ' '));
        $text = $text.'...';

        return $text;
    }
}

if (!function_exists('partial')) {
    function partial($view)
    {
        $theme = Request::is(config('cms.core.app.paths.backend').'*')
            ? config('cms.core.app.themes.backend')
            : config('cms.core.app.themes.frontend');
        $viewStr = 'theme.'.$theme.'::views.modules.'.str_replace('::', '.', $view);
        if (view()->exists($viewStr)) {
            $view = $viewStr;
        }

        return $view;
    }
}

if (!function_exists('date_array')) {
    /**
     * Compiles a list of dates to return.
     *
     * @return array
     */
    function date_array($date)
    {
        $return = [
            'default' => $date,
            'atom' => date_carbon($date, DateTime::ATOM),
            'ago' => date_ago($date),
            'fuzzy' => date_fuzzy($date),
        ];

        $return['element'] = sprintf(
            '<time datetime="%s" title="%s">%s</time>',
            array_get($return, 'atom'),
            array_get($return, 'fuzzy'),
            in_array($date, [null, '0000-00-00 00:00:00']) ? 'Never' : array_get($return, 'ago')
        );

        return $return;
    }
}

if (!function_exists('date_carbon')) {
    function date_carbon($value, $format = null)
    {
        if (!Config::has('cms.core.config.date-format')) {
            return $value;
        } else {
            if ($format === null) {
                $format = config('cms.core.config.date-format');
            }
        }

        if (is_number($value)) {
            $carbon = \Carbon\Carbon::createFromTimeStamp($value)
                ->timezone(config('app.timezone'));
        } else {
            $carbon = \Carbon\Carbon::parse($value)
                ->timezone(config('app.timezone'));
        }

        if ($format !== null) {
            $carbon = $carbon->format($format);
        }

        return $carbon;
    }
}

if (!function_exists('date_now')) {
    function date_now()
    {
        // returns current time/date
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('date_ago')) {
    function date_ago($string)
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($string))
            ->timezone(config('app.timezone'))
            ->diffForHumans();
    }
}

if (!function_exists('date_difference')) {
    function date_difference($string)
    {
        return \Carbon\Carbon::now()
            ->subSeconds($string)
            ->timezone(config('app.timezone'))
            ->diffForHumans();
    }
}

if (!function_exists('date_fuzzy')) {
    /* http://daveyshafik.com/archives/28101-datetime-timestamp-parsing.html */
    function date_fuzzy($date, $inputFormat = null, $outputDateFormat = null, $outputTimeFormat = null)
    {
        $inputFormat = $inputFormat ?: DateTime::ATOM;
        $outputDateFormat = $outputDateFormat ?: 'F dS Y';
        $outputTimeFormat = $outputTimeFormat ?: 'H:ia';

        $dateTime = (is_int($date) ? with(new Carbon\Carbon())->createFromTimestamp($date) : new DateTime($date));

        // Failed to parse, probably invalid date
        if (!$dateTime) {
            return false;
        }

        // Get Timezone so we can use it for the other dates
        $timezone = $dateTime->getTimeZone();

        // Fuzzy Date ranges
        $lastWeekStart = new DateTime('2 weeks ago sunday 11:59:59', $timezone);

        $yesterdayStart = new DateTime('yesterday midnight', $timezone);

        $todayStart = new DateTime('today midnight', $timezone);
        $todayEnd = new DateTime('today 23:59:59', $timezone);

        // $tomorrowStart = new DateTime('tomorrow midnight', $timezone);
        $tomorrowEnd = new DateTime('tomorrow 23:59:59', $timezone);

        $thisWeekStart = new DateTime('1 week ago sunday 11:59:59', $timezone);
        $thisWeekEnd = new DateTime('sunday 11:59:59', $timezone);

        $nextWeekEnd = new DateTime('1 week sunday midnight', $timezone);

        $prefix = '';

        // We have to start with the oldest ones first
        if ($dateTime < $lastWeekStart) {
            // Older than 1 week
            $prefix = 'on';
            $fuzzyDate = ucwords($dateTime->format($outputDateFormat));
        } elseif ($dateTime > $lastWeekStart && $dateTime < $thisWeekStart) {
            // Some time in the previous week
            $prefix = 'Last';
            $fuzzyDate = ucwords($dateTime->format('l'));
        } elseif ($dateTime > $thisWeekStart && $dateTime < $yesterdayStart) {
            // Some time in the this week
            $fuzzyDate = ucwords($dateTime->format('l'));
        } elseif ($dateTime > $yesterdayStart && $dateTime < $todayStart) {
            // Yesterday
            $fuzzyDate = 'Yesterday';
        } elseif ($dateTime < $todayEnd) {
            // Today
            $fuzzyDate = 'Today';
        } elseif ($dateTime < $tomorrowEnd) {
            // Tomorrow
            $fuzzyDate = 'Tomorrow';
        } elseif ($dateTime < $thisWeekEnd) {
            // Sometime in the current week
            $prefix = 'This';
            $fuzzyDate = ucwords($dateTime->format('l'));
        } elseif ($dateTime < $nextWeekEnd) {
            // Some time in the following week
            $prefix = 'Next';
            $fuzzyDate = ucwords($dateTime->format('l'));
        } else {
            // More than 2 weeks out.
            $prefix = 'on';
            $fuzzyDate = ucwords($dateTime->format($outputDateFormat));
        }

        // Midnight or an actual time
        if ($dateTime->format('Hi') != '0000') {
            $fuzzyTime = $dateTime->format($outputTimeFormat);
        } else {
            $fuzzyTime = 'midnight';
        }

        $format = '%s %s at %s';

        return trim(sprintf($format, $prefix, $fuzzyDate, $fuzzyTime));
    }
}

if (!function_exists('get_array_column')) {
    function get_array_column($array, $key)
    {
        return array_filter(array_map(function ($row) use ($key) {
            return array_get($row, $key, null);
        }, $array));
    }
}

if (!function_exists('getCurrentTheme')) {
    function getCurrentTheme()
    {
        if (Request::is('admin/*')) {
            return config('cms.core.app.themes.backend', 'default_admin');
        }

        return config('cms.core.app.themes.frontend', 'default');
    }
}

if (!function_exists('escape')) {
    function escape($value)
    {
        return Purifier::clean($value);
    }
}

if (!function_exists('cache')) {
    function cache($tag, $key, $length, $callback)
    {
        if (Cache::getFacadeRoot() instanceof TaggableStore) {
            return Cache::tags($tag)->remember($key, $length, $callback);
        }

        if (is_array($tag)) {
            $tag = implode('.', $tag).'.';
        }

        return Cache::remember($tag.$key, $length, $callback);
    }
}

if (!function_exists('cache_forever')) {
    function cache_forever($tag, $key, $callback)
    {
        if (Cache::getFacadeRoot() instanceof TaggableStore) {
            return Cache::tags($tag)->rememberForever($key, $callback);
        }

        if (is_array($tag)) {
            $tag = implode('.', $tag).'.';
        }

        return Cache::rememberForever($tag.$key, $callback);
    }
}

if (!function_exists('cache_flush')) {
    function cache_flush($tag)
    {
        if (Cache::getFacadeRoot() instanceof TaggableStore) {
            Cache::tags($tag)->flush();

            return;
        }

        artisan_call('cache:clear');
    }
}

if (!function_exists('cache_has')) {
    function cache_has($tag, $key)
    {
        if (Cache::getFacadeRoot() instanceof TaggableStore) {
            return Cache::tags($tag)->has($key);
        }

        if (is_array($tag)) {
            $tag = implode('.', $tag).'.';
        }

        return Cache::has($tag.$key);
    }
}

if (!function_exists('cache_get')) {
    function cache_get($tag, $key)
    {
        if (Cache::getFacadeRoot() instanceof TaggableStore) {
            return Cache::tags($tag)->get($key);
        }

        if (is_array($tag)) {
            $tag = implode('.', $tag).'.';
        }

        return Cache::get($tag.$key);
    }
}

if (!function_exists('transform_button_args')) {
    /**
     * Run a transformer for 'segment:x' calls for the button helper.
     *
     * @param array $args
     *
     * @return array
     */
    function transform_button_args($args)
    {
        if (!count($args)) {
            return $args;
        }

        foreach ($args as $key => $value) {
            if (substr($value, 0, 7) == 'segment') {
                list(, $value) = explode(':', $value);
                $args[$key] = Request::segment($value);
            }
        }

        return $args;
    }
}
