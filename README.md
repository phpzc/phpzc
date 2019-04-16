# phpzc
PHP Commonly used class library

- curl tool
- redis tool
- session tool
- openssl tool

## install
```
composer require phpzc/phpzc
```

## Redis Session Usage

```
include_once __DIR__.'/vendor/autoload.php';

use \Phpzc\Redis\RedisConfig;
use \Phpzc\Session\RedisSession;

//redis config
RedisConfig::setConfig('127.0.0.1',6379,'',0);
//session run session_start
RedisSession::init(7*24*3600,true);
//you can use $_SESSION
...
```

## LICENSE
GNU GENERAL PUBLIC LICENSE
