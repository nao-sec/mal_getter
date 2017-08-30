<?php

namespace Campaign;

require_once 'Request.php';
require_once 'Share.php';

class Seamless
{
    public static function analyze(string $url) : string
    {
        $response = \Request::get($url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '.html', $html);
        \Share::$_['count']++;
        $url = explode('"', explode('src="', $html)[1])[0];
        return $url;
    }
}
