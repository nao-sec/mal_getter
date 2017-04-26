<?php

class RC4
{
    public static function calc(string $data, string $key) : string
    {
        $s = [];
        for($i = 0; $i < 256; $i++)
        {
            $s[$i] = $i;
        }

        $j = 0;
        for($i = 0; $i < 256; $i++)
        {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            list($s[$i], $s[$j]) = [$s[$j], $s[$i]];
        }

        $i = $j = 0;
        $ret = '';
        for($k = 0; $k < strlen($data); $k++)
        {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            list($s[$i], $s[$j]) = [$s[$j], $s[$i]];
            $ret .= $data[$k] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }

        return $ret;
    }
}
