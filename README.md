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

$ php main.php eitest rig http://wpteam.org
[+] http://wpteam.org
[+] http://set.212poinsettiaave.com/?oq=m3Y_PcoLbRVaFK1iECFKQIynNtfUAgRo_2thkjdzUOZ1cTX_hHZUTp1u9CcUbI&q=wXfQMvXcJwDQC4bGMvrESLtNNknQA0KK2In2_dqyEoH9eGnihNzUSkr76B2aC
[+] http://set.212poinsettiaave.com/?oq=CEh3h_PorKLJWOFawjRGFfldmz4xdB1hB9f2u2kLdwRLJgpLXrkCJNQh1z6I&q=wH3QMvXcJwDPFYbGMvrETaNbNknQA0ePxpH2_drWdZqxKGni0-b5UUSk6Fq
[+] http://set.212poinsettiaave.com/?oq=8PMrKrcFbFfkiBeHKAxhyYwPUA5H9KGqhkWAzEPP1ZPW_yWEaANM9pucHbcLhR32&q=z3vQMvXcJwDQDoTIMvrESLtEMU_OGUKK2OH_783VCZ_9JHT1vvHPRAPytgWCelTY
[+] Waiting..........
[!] 2f86aa66c9a47e942f312b4ba22a935f.bin

$cd 2017-05-26_08-28-47

$ file 2f86aa66c9a47e942f312b4ba22a935f.bin
2f86aa66c9a47e942f312b4ba22a935f.bin: PE32 executable for MS Windows (GUI) Intel 80386 32-bit
```

## LICENSE
```mal_getter``` is open-sourced software licensed under the [MIT License](LICENSE)
