# mal_getter
It is a tool for dropping malware from EK  

Currently this tool can analyze EITest, GoodMan, Decimal IP, Seamless and RigEK.  
It depends on Campaign and EK implementation,  
so the possibility that this will not work is very high.

## Require
- PHP 7
- Composer

## Install
```sh
$ git clone https://github.com/koike/mal_getter.git
$ cd mal_getter
$ composer install
```

## Usage
```sh
$ php main.php [Campaign] [EK] [Compromised URL]
```

## Example
```
$ ls
composer.json  composer.lock  main.php  RC4.php  Request.php  vendor

$ php main.php eitest rig http://2watchmygf.com
[*] http://2watchmygf.com
[*] http://more.plus255tv.com/?oq=xfZ5K7BVbATphRSAKgcwz9teW1gUpav4i0TTzRTNgZKB-hHfZQhA9qKlJLF_mhj2&ct=martery&qtuif=4815&q=w3bQMvXcJxrQFYbGMv3DSKNbNkbWHViPxo2G9MildZyqZGX_k7vDfF-qoVjcCgWR
[*] http://more.plus255tv.com/?ct=soul&oq=F8_cvKONYNVLhihTRKFBilYleAFhF8q-qjEaEyBWbhMWG-xCEUQNM9puSJOx72w&qtuif=3457&q=wXnQMvXcJwDQCobGMvrESLtENknQA0KK2Iv2_dqyEoH9c2nihNzUSkrw6B2aCm2
[!] 2017-04-26_06-19-14.bin

$ file 2017-04-26_06-19-14.bin
2017-04-26_06-19-14.bin: PE32 executable for MS Windows (GUI) Intel 80386 32-bit
```

## LICENSE
```mal_getter``` is open-sourced software licensed under the [MIT License](LICENSE)
