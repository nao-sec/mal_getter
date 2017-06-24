<?php

require_once 'RC4.php';
require_once 'Request.php';

if($argc < 4)
{
    echo $argv[0] . ' [Campaign] [EK] [URL]' . PHP_EOL;
    exit(-1);
}

$campaign = strtolower($argv[1]);
$ek = strtolower($argv[2]);
$url = $argv[3];
$old_url = $url;
$count = 0;

if
(
    $campaign !== 'eitest' &&
    $campaign !== 'goodman' &&
    $campaign !== 'decimal' &&
    $campaign !== 'seamless' &&
    $campaign !== 'despicable' &&
    $campaign !== 'afu' &&
    $campaign !== 'roughted' &&
    $campaign !== 'etc'
)
{
    echo $argv[0] . ' Undefined Campaign' . PHP_EOL;
    exit(-1);
}

if($ek !== 'rig')
{
    echo $argv[0] . ' Undefined Exploit Kit' . PHP_EOL;
    exit(-1);
}

$dir = getcwd() . '/' . date('Y-m-d_H-i-s') . '/';
mkdir($dir);

echo '[+] ' . $url . PHP_EOL;
if($campaign !== 'despicable' && $campaign !== 'roughted')
{
    $response = Request::get($url);
    if($response['status'] < 200 || $response['status'] >= 400)
    {
        echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
        exit(-1);
    }
    $html = $response['body'] . '';
    file_put_contents($dir . $count . '.html', $html);
    $count++;
    $old_url = $url;
}

// EITest
if($campaign === 'eitest')
{
    $latter = explode('= "iframe"', $html)[1];
    $url = explode(' = "http://', $latter)[1];
    $url = explode('";', $url)[0];
    $url = 'http://' . $url;
}

// GoodMan
if($campaign === 'goodman')
{
    $url = explode('\'', explode('iframe src=\'', $html)[1])[0];
}

// Decimal
if($campaign === 'decimal')
{
    $url = explode('"', explode('iframe src="', $html)[1])[0];
}

// Seamless
if($campaign === 'seamless')
{
    $url = explode('"', explode('src="', $html)[1])[0];
    // $url = Request::extract($url);
}

// Despicable
if($campaign === 'despicable')
{
    $url = Request::extract($url);
}

// afu
if($campaign === 'afu')
{
    $url = explode("'", explode("URL='", $html)[1])[0];
}

// roughted
if($campaign === 'roughted')
{
    $referer = 'http://pejino.com';
    $response = Request::get($url, $referer);
    $html = $response['body'] . '';
    $url = explode('"', explode('src="', $html)[1])[0];
    $old_url = $referer;
}

// etc
if($campaign === 'etc')
{
    $url = explode('"', explode('iframe src="', $html)[1])[0];
}

echo '[+] ' . $url . PHP_EOL;

$response = Request::get($url, $old_url);
if($response['status'] < 200 || $response['status'] >= 400)
{
    echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
    exit(-1);
}
$html = $response['body'] . '';
file_put_contents($dir . $count . '.html', $html);
$count++;

$url = explode("'", explode("var NormalURL = '", $html)[1])[0];
echo '[+] ' . $url . PHP_EOL;

$html = str_replace('<script>', "\n", $html);
$html = str_replace('</script>', "\n", $html);
$full_html = explode("\n", $html);
$html = [];

for($i=0; $i<count($full_html); $i++)
{
    if(strlen($full_html[$i]) > 100)
    {
        $tmp = $full_html[$i];
        $tmp = str_replace('</head>', '', $tmp);
        $tmp = str_replace('<body>', '', $tmp);
        $tmp = str_replace('<script>', '', $tmp);
        $tmp = str_replace('</script>', '', $tmp);
        $tmp = str_replace('<hl>', '', $tmp);
        $tmp = str_replace('</hl>', '', $tmp);
        $tmp = str_replace('</body>', '', $tmp);
        $tmp = str_replace('</html>', '', $tmp);
        $html[] = trim($tmp);
    }
}

$block_count = count($html) / 3;

$js = [];
for($i=0; $i<$block_count; $i++)
{
    for($j=0; $j<2; $j++)
    {
        $js[$i][$j] = substr(trim($html[$i*3 + $j]), 12);
    }
}

$split = [];
for($i=0; $i<$block_count; $i++)
{
    for($j=0; $j<2; $j++)
    {
        $split[$i][$j] = substr($js[$i][$j], -4, 1);
    }
}

for($i=0; $i<$block_count; $i++)
{
    for($j=0; $j<2; $j++)
    {
        $js[$i][$j] = substr($js[$i][$j], 0, -22);
    }
}

$code = [];
for($i=0; $i<count($js); $i++)
{
    $str[0] = explode($split[$i][0], $js[$i][0]);
    $str[1] = explode($split[$i][1], $js[$i][1]);

    $code[$i] = '';

    for($j=0; $j<count($str[0]); $j++)
    {
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
    for($j=0; $j<count($pattern); $j++)
    {
        $code[$i] = str_replace(chr($j+1), $pattern[$j], $code[$i]);
    }
}

for($i=0; $i<count($code); $i++)
{
    $code[$i] = preg_replace("/[^\x20-\x7E]/", " ", $code[$i]);
    $code[$i] = preg_replace('/\s{2,}/', '', $code[$i]);
    $code[$i] = explode('var s = ', $code[$i])[1];
    $code[$i] = explode('"', $code[$i])[1];
    $code[$i] = base64_decode($code[$i]);
}

for($i=0; $i<count($code); $i++)
{
    file_put_contents($dir . $count . '_' . $i . '.txt', $code[$i]);
}

for($i=0; $i<count($code); $i++)
{
    if(substr_count($code[$i], 'http://') >= 1)
    {
        $old_url = $url;
        preg_match_all('/"http:\/\/.+"/', $code[$i], $url);
        $url = end($url)[0];
        $url = explode('"', $url)[1];
        break;
    }
}

echo '[+] ' . $url . PHP_EOL;

echo '[+] Waiting';
for($i=0; $i<5; $i++)
{
    echo '.';
    sleep(1);
}
echo PHP_EOL;

$malware = Request::get($url, null);
$malware = $malware['body'];

if($malware == null)
{
    echo '[!] NULL Response...' . PHP_EOL;

    echo '[+] Retrying';
    for($i=0; $i<5; $i++)
    {
        echo '.';
        sleep(1);
    }
    echo PHP_EOL;
    
    $malware = Request::get($url, null);
    $malware = $malware['body'];

    if($malware == null)
    {
        echo '[!] Failed...' . PHP_EOL;
    }
}

$key = 'gexywoaxor';
$malware = RC4::calc($malware, $key);

$md5 = md5($malware);
file_put_contents($dir . $md5 . '.bin', $malware);
echo '[!] ' . $md5 . '.bin' . PHP_EOL;
