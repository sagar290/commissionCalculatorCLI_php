<?php


// if key exist or return null
if(!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
    
        if (isset($array[$key])) {
            return $array[$key];
        }
    
        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
    
        return $array;
    }
}


if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

if (!function_exists('app')) {
    function app($class)
    {
        return (new \Sagar290\CommissionCalc\App())->resolve($class);
    }
}