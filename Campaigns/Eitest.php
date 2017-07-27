<?php

require_once 'Request.php';
require_once 'Share.php';

class Eitest
{
    public static function analyze(string $url) : string
    {
        $response = Request::get($url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(Share::$_['dir'] . Share::$_['count'] . '.html', $html);
        Share::$_['count']++;
        $latter = explode('= "iframe"', $html)[1];
        $url = explode(' = "http://', $latter)[1];
        $url = explode('";', $url)[0];
        $url = 'http://' . $url;
        return $url;
    }
}
