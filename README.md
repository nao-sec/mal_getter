# mal_getter
It is a tool for dropping malware from EK  

Currently this tool can analyze
- Decimal IP
- Despicable
- EITest
- GoodMan
- Rulan
- Seamless
- Rig Exploit Kit
- Terror Exploit Kit

It depends on Campaign and EK implementation,  
so the possibility that this will not work is very high.

## Require
- PHP 7
  - cURL
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
Campaigns  LICENSE  README.md    Share.php      composer.lock  vendor
EKs        RC4.php  Request.php  composer.json  main.php

$ php main.php seamless rig http://[Deleted]/signup1.php
[+] http://[Deleted]/signup1.php
[+] http://188.225.33.138/?OTUxMzI0MDc=&elsa=SwdhzoxeB1hGoqv4j0DXyBfKgJWCrxeOaAtGrpDGEbMziV_3zLBHeckizheBu2BYmOgtYlsgpQhR2a_I&info=MzM3MTAwOTA=&rand=xHzQMrPYbR3FFYDfKPnEUKREMU3WA0SKwY-ZhazVF5-xFDTGpbL1Fx_spVydCFyEmvJvdLMHIwKh1UfA&shld=MzA0MTk4ODI=
[+] http://188.225.33.138/?Njc3NjM3MTM=&info=MTMzOTY5NjU=&rand=xXzQMvWebRXQC53EKvncT6NEMVHRH0CL2YydmrHTefjaeVWkzrLFTF_xozKASASG6_JtdfJSDQOzj&elsa=0bVLgc0yo9cUFtF9amu3EPWwBWe0cSH_B3cYwhGrJuXEbg42Q_9m7MkecImzh-L6GhZxegtDxoR4g&fore=MjY3ODQwMg==
[+] Waiting.....
[!] 4cc6922ec861c67b29d1c60b58aa12f74540dc838e3da8cade8d3c15b308da6e.bin
```

## LICENSE
```mal_getter``` is open-sourced software licensed under the [MIT License](LICENSE)
