<?php

namespace EK;

require_once 'Request.php';
require_once 'Share.php';

class Kaixin
{
    public static function analyze($url)
    {
        $old_url = \Share::$_['old_url'];
        $landing_url = $url;
        if($landing_url[strlen($landing_url)-1] !== '/')
        {
            $landing_url .= '/';
        }
        $malware_url = '';

        $response = \Request::get($landing_url, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
            exit(-1);
        }
        $html = $response['body'] . '';
        file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_index.html', $html);
        \Share::$_['count']++;

        $html = explode('<script type="text/javascript">', $html)[1];
        $js = explode('</script>', $html)[0];

        // jar exploit
        $benz = explode("'", explode("benz='", $js)[1])[0] . '.jar';
        echo '[+] ' . $landing_url . $benz . PHP_EOL;
        $response = \Request::get($landing_url . $benz, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $jar = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' .  $benz, $jar);
        }

        $audi = explode("'", explode("audi='", $js)[1])[0] . '.jar';
        echo '[+] ' . $landing_url . $audi . PHP_EOL;
        $response = \Request::get($landing_url . $audi, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $jar = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' .  $audi, $jar);
        }

        $jaguar = explode("'", explode("jaguar='", $js)[1])[0] . '.jar';
        echo '[+] ' . $landing_url . $jaguar . PHP_EOL;
        $response = \Request::get($landing_url . $jaguar, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $jar = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' .  $jaguar, $jar);
        }

        \Share::$_['count']++;

        // CVE-2016-0189
        $ferrari = explode("'", explode("ferrari='", $js)[1])[0] . '.html';
        echo '[+] ' . $landing_url . $ferrari . PHP_EOL;
        $response = \Request::get($landing_url . $ferrari, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $html = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' .  $ferrari, $html);
            $html = explode('}', explode('function nblink(){', $html)[1])[0];
            $int_vals = explode(')', explode('(', $html)[1])[0];
            $int_vals = explode(',', $int_vals);
            for($i=0; $i<count($int_vals); $i++)
            {
                $malware_url .= chr(intval($int_vals[$i]) - 178);
            }
        }

        // 404 Not Found
        $bugatti = explode("'", explode("bugatti='", $js)[1])[0] . '.html';
        echo '[+] ' . $landing_url . $bugatti . PHP_EOL;
        $response = \Request::get($landing_url . $bugatti, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $html = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' .  $bugatti, $html);
        }

        // CVE-2016-7200 & CVE-2016-7201
        $bentley = explode("'", explode("bentley='", $js)[1])[0] . '.html';
        echo '[+] ' . $landing_url . $bentley . PHP_EOL;
        $response = \Request::get($landing_url . $bentley, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $html = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' .  $bentley, $html);
        }

        // swf loader
        $maserati = explode("'", explode("maserati='", $js)[1])[0] . '.html';
        echo '[+] ' . $landing_url . $maserati . PHP_EOL;
        $response = \Request::get($landing_url . $maserati, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $html = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' . $maserati, $html);

            // swf exploit
            $html = explode('<script type="text/javascript">', $html)[1];
            $js = explode('</script>', $html)[0];

            // WIP :)
        }

        // malware
        echo '[+] ' . $malware_url . PHP_EOL;
        $response = \Request::get($malware_url, $old_url);
        if ($response['status'] < 200 || $response['status'] >= 400) {
            echo '[!] Server Error...' . PHP_EOL;
        }
        else
        {
            $exe = $response['body'] . '';
            file_put_contents(\Share::$_['dir'] . \Share::$_['count'] . '_' . basename($malware_url) . '.bin', $exe);
        }
    }
}
