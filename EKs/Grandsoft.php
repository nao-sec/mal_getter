<?php

namespace EK;

require_once 'Request.php';
require_once 'Share.php';

class Grandsoft
{
    public static function analyze($url)
    {
        $response = \Request::get($url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '.html', $html);
        \Share::$_['count']++;

        $url_parts = explode('/', $url);
        $base_url = implode('/', array_slice($url_parts, 0, count($url_parts) - 1));
        $url = $base_url . '/getversionpd/null/null/null/null';

        echo '[+] ' . $url . PHP_EOL;
        $response = \Request::get($url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '.html', $html);
        \Share::$_['count']++;

        list($usec, $sec) = explode(' ', \microtime());
        mt_srand($sec + $usec * 1000000);
        $r = mt_rand();
        while ($r > 1) {
            $r /= 10;
        }
        $key = intval(8901 * $r + 100);
        $old_url = $url;
        $url = $base_url . "/2/" . $key;

        echo '[+] ' . $url . PHP_EOL;
        $response = \Request::get($url, 'grandsoft', 'Mozilla/4.0 (compatible; Win32; WinHttp.WinHttpRequest.5)');
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
            exit(-1);
        }
        $enc_binary = $response['body'] . '';

        $data = [];
        for ($i = 0; $i < strlen($enc_binary); $i++) {
            $key = ($key + 0xAA) & 0xFF;
            $key = $key ^ 0x48;
            $data[] = chr(ord($enc_binary[$i]) ^ $key);
        }
        $malware = implode('', $data);
        $sha256 = hash('sha256', $malware);
        file_put_contents(\Share::$_['dir'] . $sha256 . '.bin', $malware);
        echo '[!] ' . $sha256 . '.bin' . PHP_EOL;
    }
}
