<?php

namespace EK;

require_once 'Request.php';
require_once 'Share.php';
require_once 'RC4.php';

class Terror
{
    public static function analyze($url)
    {
        $old_url = \Share::$_['old_url'];
        $landing_url = $url;

        $response = \Request::get($landing_url, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '.html', $html);
        \Share::$_['count']++;

        $html = str_replace('</iframe>', '</iframe>' . PHP_EOL, $html);
        preg_match_all("/<iframe src='.+'><\/iframe>/", $html, $iframes);
        if(count($iframes) > 0)
        {
            $iframes = $iframes[0];
        }
        if(count($iframes) < 1)
        {
            echo '[!] iframe is not found...' . PHP_EOL;
            exit(-1);
        }

        $malware = [];
        for($i=0; $i<count($iframes); $i++)
        {
            $iframe = $iframes[$i];
            $iframe = str_replace("<iframe src='", "", $iframe);
            $iframe = str_replace("'></iframe>", "", $iframe);
            echo '[+] iframe: ' . $iframe . PHP_EOL;

            $response = \Request::get($iframe, $landing_url);
            if ($response['status'] < 200 || $response['status'] >= 400) {
                echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
                exit(-1);
            }
            $html = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' . $i . '.txt', $html);

            if(strpos($html, "exp('http://") !== false)
            {
                $temp = explode("')", explode("exp('http://", $html)[1])[0];
                $temp = explode(',', $temp);
                $malware['url'] = $temp[0];
                $malware['key'] = $temp[1];
            }

            if(strpos($html, 'key=') !== false && strpos($html, 'url=') !== false)
            {
                $malware['url'] = explode('"', explode('url="', $html)[1])[0];
                $malware['key'] = explode('"', explode('key="', $html)[1])[0];
            }

            if(strpos($html, '.swf') !== false)
            {
                preg_match_all('/data=".+\.swf"/', $html, $swf_filenames);
                if(count($swf_filenames) > 0)
                {
                    $swf_filenames = $swf_filenames[0];
                }
                if(count($swf_filenames) > 0)
                {
                    for($j=0; $j<count($swf_filenames); $j++)
                    {
                        $swf_filenames[$j] = str_replace('"', '', $swf_filenames[$j]);
                        $swf_filenames[$j] = str_replace('data=', '', $swf_filenames[$j]);
                        $base_url = str_replace(basename($iframe), "", $iframe);
                        echo '[+] swf: ' . $base_url . $swf_filenames[$j] . PHP_EOL;
                        $response = \Request::get($base_url . $swf_filenames[$j], $iframe);
                        if ($response['status'] < 200 || $response['status'] >= 400) {
                            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
                            exit(-1);
                        }
                        $swf = $response['body'] . '';
                        file_put_contents(\Share::$_['dir'] . (\Share::$_['count']+1) . '_' . $j . '.swf', $swf);
                    }
                }
            }
        }
        $response = \Request::get($malware['url'], $landing_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
            exit(-1);
        }
        $binary = $response['body'] . '';
        $malware = \RC4::calc($binary, $malware['key']);
        $sha256 = hash('sha256', $malware);
        file_put_contents(\Share::$_['dir'] . $sha256 . '.bin', $malware);
        echo '[!] ' . $sha256 . '.bin' . PHP_EOL;
    }
}
