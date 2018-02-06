<?php

namespace Campaign;

require_once 'Request.php';
require_once 'Share.php';

class Bloodlust
{
    public static function analyze(string $url) : string
    {
        \Share::$_['old_url'] = $url;
        $response = \Request::get($url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        if(strpos($html, '<iframe src="'))
        {
            $url = explode('<iframe src="', $html)[1];
            $url = explode('"', $url);
            if(count($url) > 1)
            {
                $url = $url[0];
                file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '.html', $html);
                \Share::$_['count']++;
                return $url;
            }
        }
        echo '[!] Not exist iframe' . PHP_EOL;
        exit(-1);
    }
}
