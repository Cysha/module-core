<?php

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
