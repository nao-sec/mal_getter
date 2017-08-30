<?php

namespace Campaign;

require_once 'Request.php';
require_once 'Share.php';

class Terror
{
    public static function analyze(string $url) : string
    {
        $url = \Request::extract($url);
        return $url;
    }
}
