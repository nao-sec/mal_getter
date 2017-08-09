<?php

require_once 'Request.php';
require_once 'Share.php';
require_once 'RC4.php';

class Rig
{
    public static function analyze($url)
    {
        $ua = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)';
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
        file_put_contents(Share::$_['dir'] . Share::$_['count'] . '.html', $html);
        Share::$_['count']++;

        $html = str_replace('<script>', "\n", $html);
        $html = str_replace('</script>', "\n", $html);
        $full_html = explode("\n", $html);
        $html = [];

        for ($i=0; $i<count($full_html); $i++) {
            if (strlen($full_html[$i]) > 100) {
                $tmp = $full_html[$i];
                $tmp = str_replace('</head>', '', $tmp);
                $tmp = str_replace('<body>', '', $tmp);
                $tmp = str_replace('<script>', '', $tmp);
                $tmp = str_replace('</script>', '', $tmp);
                $tmp = str_replace('<hl>', '', $tmp);
                $tmp = str_replace('</hl>', '', $tmp);
                $tmp = str_replace('</body>', '', $tmp);
                $tmp = str_replace('</html>', '', $tmp);
                $tmp = trim($tmp);
                if (strlen($tmp) > 100) {
                    $html[] = $tmp;
                }
            }
        }

        $block_count = count($html) / 3;

        $js = [];
        for ($i=0; $i<$block_count; $i++) {
            for ($j=0; $j<2; $j++) {
                $js[$i][$j] = substr(trim($html[$i*3 + $j]), 12);
                preg_match_all('/\/\*[0-9a-zA-Z]{1,32}\*\//', $js[$i][$j], $matches);
                if (count($matches) > 0) {
                    $matches = $matches[0];
                    if (count($matches) > 0) {
                        for ($k=0; $k<count($matches); $k++) {
                            $js[$i][$j] = str_replace($matches[$k], '', $js[$i][$j]);
                        }
                    }
                }
            }
        }

        $split = [];
        for ($i=0; $i<$block_count; $i++) {
            for ($j=0; $j<2; $j++) {
                $split[$i][$j] = substr($js[$i][$j], -4, 1);
            }
        }

        for ($i=0; $i<$block_count; $i++) {
            for ($j=0; $j<2; $j++) {
                $js[$i][$j] = substr($js[$i][$j], 0, -22);
            }
        }

        $code = [];
        for ($i=0; $i<count($js); $i++) {
            $str[0] = explode($split[$i][0], $js[$i][0]);
            $str[1] = explode($split[$i][1], $js[$i][1]);

            $code[$i] = '';

            for ($j=0; $j<count($str[0]); $j++) {
                $code[$i] .= $str[1][$j];
                $code[$i] .= $str[0][count($str[0]) - $j - 1];
            }

            $pattern =
            [
                '.',
                '<',
                '>',
                '=',
                '"',
                "'",
                ')',
                '(',
                ' ',
                "\t",
                "\n"
            ];
            for ($j=0; $j<count($pattern); $j++) {
                $code[$i] = str_replace(chr($j+1), $pattern[$j], $code[$i]);
            }
        }

        for ($i=0; $i<count($code); $i++) {
            $code[$i] = preg_replace("/[^\x20-\x7E]/", " ", $code[$i]);
            $code[$i] = preg_replace('/\s{2,}/', '', $code[$i]);
            $code[$i] = explode('var s = ', $code[$i])[1];
            $code[$i] = explode('"', $code[$i])[1];
            $code[$i] = base64_decode($code[$i]);
        }

        for ($i=0; $i<count($code); $i++) {
            file_put_contents(Share::$_['dir'] . Share::$_['count'] . '_' . $i . '.txt', $code[$i]);
        }

        for ($i=0; $i<count($code); $i++) {
            preg_match_all('/"http:\/\/[0-9]+.+"/', $code[$i], $url);
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

        $key = null;
        for($i=0; $i<count($code); $i++)
        {
            preg_match_all('/key=".{8,16}"/', $code[$i], $key);
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
            // $key = 'gexywoaxor';
            // $key = 'gexykukusa';
            // $key = 'xexykukusa';
            $key = 'wexykukusw';
        }

        echo '[+] Key: ' . $key . PHP_EOL;
        echo '[+] ' . $url . PHP_EOL;

        echo '[+] Waiting';
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

        $malware = RC4::calc($malware, $key);
        $sha256 = hash('sha256', $malware);
        file_put_contents(Share::$_['dir'] . $sha256 . '.bin', $malware);
        echo '[!] ' . $sha256 . '.bin' . PHP_EOL;
    }
}
