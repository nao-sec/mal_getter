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

if($campaign !== 'eitest' && $campaign !== 'goodman' && $campaign !== 'decimal' && $campaign !== 'seamless')
{
    echo $argv[0] . ' Undefined Campaign' . PHP_EOL;
    exit(-1);
}

if($ek !== 'rig')
{
    echo $argv[0] . ' Undefined Exploit Kit' . PHP_EOL;
    exit(-1);
}

$filename = date('Y-m-d_H-i-s');

echo '[*] ' . $url . PHP_EOL;
$response = Request::get($url);
if($response['status'] < 200 || $response['status'] >= 400)
{
    echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
    exit(-1);
}
$html = $response['body'] . '';
file_put_contents($filename . '_0.html', $html);
$old_url = $url;

// EITest
if($campaign === 'eitest')
{
    $url = explode(' = "http://', $html)[1];
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
}

echo '[*] ' . $url . PHP_EOL;

$response = Request::get($url, $old_url);
if($response['status'] < 200 || $response['status'] >= 400)
{
    echo '[!] HTTP Status: ' . $response['status'] . PHP_EOL;
    exit(-1);
}
$html = $response['body'] . '';
file_put_contents($filename . '_1.html', $html);

$html = explode("\n", $html);

// IE 6
$js[0][0] = substr(trim($html[3]), 34);
$js[0][1] = substr(trim($html[4]), 12);
$js[1][0] = substr(trim($html[10]), 27);
$js[1][1] = substr(trim($html[11]), 12);
$js[2][0] = substr(trim($html[17]), 27);
$js[2][1] = substr(trim($html[18]), 12);

// IE 8
// $js[0][0] = substr(trim($html[5]), 27);
// $js[0][1] = substr(trim($html[6]), 12);
// $js[1][0] = substr(trim($html[10]), 27);
// $js[1][1] = substr(trim($html[11]), 12);
// $js[2][0] = substr(trim($html[15]), 27);
// $js[2][1] = substr(trim($html[16]), 12);

$split[0][0] = substr($js[0][0], -4, 1);
$split[0][1] = substr($js[0][1], -4, 1);
$split[1][0] = substr($js[1][0], -4, 1);
$split[1][1] = substr($js[1][1], -4, 1);
$split[2][0] = substr($js[2][0], -4, 1);
$split[2][1] = substr($js[2][1], -4, 1);

$js[0][0] = substr($js[0][0], 0, -22);
$js[0][1] = substr($js[0][1], 0, -22);
$js[1][0] = substr($js[1][0], 0, -22);
$js[1][1] = substr($js[1][1], 0, -22);
$js[2][0] = substr($js[2][0], 0, -22);
$js[2][1] = substr($js[2][1], 0, -22);

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
    $code[$i] = explode('var s = "', $code[$i])[1];
    $code[$i] = explode('";var e={}', $code[$i])[0];
    $code[$i] = base64_decode($code[$i]);
}

for($i=0; $i<count($code); $i++)
{
    if(substr_count($code[$i], 'http://') == 1)
    {
        $old_url = $url;
        preg_match_all('/"http:\/\/.+"/', $code[$i], $url);
        $url = end($url)[0];
        $url = explode('"', $url)[1];
        break;
    }
}

echo '[*] ' . $url . PHP_EOL;

$malware = Request::get($url, $old_url);
$malware = $malware['body'];

$key = 'gexywoaxor';
$malware = RC4::calc($malware, $key);

file_put_contents($filename . '.bin', $malware);
echo '[!] ' . $filename . '.bin' . PHP_EOL;
