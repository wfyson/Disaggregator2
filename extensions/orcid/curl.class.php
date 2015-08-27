<?php

if(!function_exists('curl_init'))
{
    trigger_error("PHP CURL extension seems to be missing", E_USER_ERROR);
}
class CurlHelper
{
    public static function get($url)
    {
        $curl_handle=curl_init();
        
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl_handle, CURLOPT_USERAGENT, 'KOLOLA_AUTH');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        
        return $query;
    }
}