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

if (!function_exists('date_array')) {
    /**
     * Compiles a list of dates to return
     *
     * @return array
     */
    function date_array($date, $name = null)
    {
        return [
            'default' => $date,
            'atom'    => date_carbon($date, DateTime::ATOM),
            'ago'     => date_ago($date),
            'fuzzy'   => date_fuzzy($date),
        ];
    }
}

if (!function_exists('date_carbon')) {
    function date_carbon($value, $format = null)
    {
        if (!Config::has('core::module.date-format')) {
            return $value;
        }

        if ($format === null) {
            $format = Config::get('core::module.date-format');
        }

        return \Carbon\Carbon::parse($value)->format($format);
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
        return \Carbon\Carbon::createFromTimeStamp(strtotime($string))->diffForHumans();
    }
}

if (!function_exists('date_fuzzy')) {
    /* http://daveyshafik.com/archives/28101-datetime-timestamp-parsing.html */
    function date_fuzzy($date, $inputFormat = null, $outputDateFormat = null, $outputTimeFormat = null)
    {
        $inputFormat      = $inputFormat ?: DateTime::ATOM;
        $outputDateFormat = $outputDateFormat ?: 'F dS Y';
        $outputTimeFormat = $outputTimeFormat ?: 'H:ia';

        $dateTime = (is_int($date) ? with(new Carbon\Carbon)->createFromTimestamp($date) : new DateTime($date));

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
