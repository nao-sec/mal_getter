<?php

require_once 'Request.php';
require_once 'Share.php';
require_once 'RC4.php';

if ($argc < 4) {
    echo '[!] ' . $argv[0] . ' [Campaign] [EK] [URL]' . PHP_EOL;
    exit(-1);
}

$campaign = ucfirst(strtolower($argv[1]));
$ek = ucfirst(strtolower($argv[2]));
$url = $argv[3];
$old_url = $url;
Share::$_['count'] = 0;

$supported_campaigns = [];
foreach (glob(getcwd() . '/Campaigns/*.php') as $file) {
    if (is_file($file)) {
        $supported_campaigns[] =  pathinfo($file)['filename'];
    }
}

$supported_eks = [];
foreach (glob(getcwd() . '/EKs/*.php') as $file) {
    if (is_file($file)) {
        $supported_eks[] =  pathinfo($file)['filename'];
    }
}

if (!in_array($campaign, $supported_campaigns)) {
    echo $argv[0] . ' Undefined Campaign' . PHP_EOL;
    exit(-1);
}
require_once getcwd() . '/Campaigns/' . $campaign . '.php';

if (!in_array($ek, $supported_eks)) {
    echo $argv[0] . ' Undefined Exploit Kit' . PHP_EOL;
    exit(-1);
}
require_once getcwd() . '/EKs/' . $ek . '.php';

Share::$_['dir'] = getcwd() . '/' . date('Y-m-d_H-i-s') . '/';
mkdir(Share::$_['dir']);

if($campaign !== 'Kaixin')
{
    echo '[+] ' . $url . PHP_EOL;
}
$ek_url = ('Campaign\\' . $campaign)::analyze($url);
if ($campaign !== 'Kaixin' && $ek_url === $url) {
    echo '[!] Failed to get EK URL' . PHP_EOL;
    exit(-1);
}
Share::$_['old_url'] = $url;
echo '[+] ' . $ek_url . PHP_EOL;
('EK\\' . $ek)::analyze($ek_url);
