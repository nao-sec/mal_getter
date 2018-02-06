<?php

namespace EK;

require_once 'Request.php';
require_once 'Share.php';
require_once 'RC4.php';

class Sundown
{
    public static function analyze($url)
    {
        $old_url = \Share::$_['old_url'];
        $landing_url = $url;
        $ua = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)';
        
        $response = \Request::get($url, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '.html', $html);
        \Share::$_['count']++;

        $code = explode('<script>', $html);
        unset($code[0]);
        $code = array_values($code);

        for ($i=0; $i<count($code); $i++) {
            $code[$i] = explode('var s = ', $code[$i])[1];
            $code[$i] = explode('"', $code[$i])[1];
            $code[$i] = base64_decode($code[$i]);
        }

        for ($i=0; $i<count($code); $i++) {
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' . $i . '.txt', $code[$i]);
        }

        for ($i=0; $i<count($code); $i++) {
            preg_match_all('/"https?:\/\/.+"/', $code[$i], $url);
            if (count($url) > 0) {
                $url = $url[0];
            }
            if (count($url) > 0) {
                $url = end($url);
                $old_url = $url;
                $url = explode('"', $url)[1];
                break;
            }
        }
        if($url === $landing_url)
        {
            echo '[!] Failed to get malware URL' . PHP_EOL;
            exit(-1);
        }

        $key = null;
        for($i=0; $i<count($code); $i++)
        {
            preg_match_all('/key=".{1,}"/', $code[$i], $key);
            if(count($key) > 0)
            {
                $key = $key[0];
            }
            if(count($key) > 0)
            {
                $key = end($key);
                $key = explode('"', $key)[1];
                break;
            }
            else
            {
                $key = null;
            }
        }
        if($key == null)
        {
            for($i=0; $i<count($code); $i++)
            {
                preg_match_all('/",".{8,16}"\)/', $code[$i], $key);
                if(count($key) > 0)
                {
                    $key = $key[0];
                }
                if(count($key) > 0)
                {
                    $key = end($key);
                    $key = explode('"', $key)[2];
                    break;
                }
                else
                {
                    $key = null;
                }
            }
        }
        if($key == null)
        {
            echo '[!] Could not get encryption key' . PHP_EOL;
            exit(-1);
        }

        echo '[+] Key: ' . $key . PHP_EOL;
        echo '[+] ' . $url . PHP_EOL;

        $new_url = \Request::extract($url);
        if($new_url != '' && $new_url !== $url)
        {
            if(strpos($new_url, 'http') !== false)
            {
                $url = $new_url;
            }
            else
            {
                $url_parts = explode('/', $url);
                $base_path = implode('/', array_slice($url_parts, 0, count($url_parts)-1));
                $base_url = implode('/', array_slice($url_parts, 0, 3));
                if($new_url[0] === '/')
                {
                    $url = $base_url . $new_url;
                }
                else
                {
                    $url = $base_path . $new_url;
                }
            }
            echo '[+] ' . $url . PHP_EOL;
        }

        $curl = curl_init($url);
        $options =
        [
            CURLOPT_USERAGENT => $ua,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true
        ];
        curl_setopt_array($curl, $options);
        $malware = curl_exec($curl);
        curl_close($curl);

        if ($malware == null) {
            echo '[!] NULL Response...' . PHP_EOL;

            echo '[+] Retrying';
            for ($i=0; $i<5; $i++) {
                echo '.';
                sleep(1);
            }
            echo PHP_EOL;
            
            $curl = curl_init($url);
            $options =
            [
                CURLOPT_USERAGENT => $ua,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => true
            ];
            curl_setopt_array($curl, $options);
            $html = curl_exec($curl);
            curl_close($curl);

            if ($malware == null) {
                echo '[!] Failed...' . PHP_EOL;
                exit(-1);
            }
        }

        $malware = \RC4::calc($malware, $key);
        $sha256 = hash('sha256', $malware);
        file_put_contents(\Share::$_['dir'] . $sha256 . '.bin', $malware);
        echo '[!] ' . $sha256 . '.bin' . PHP_EOL;
    }
}
