<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2017/10/24
 * Time: 上午9:47
 */
namespace Phpzc\Redis;

use Predis\Client;

/**
 * Class RedisCache 使用predis
 *
 *
 * @method Client SESSION() the redis db Store Session
 * @method Client COOKIE() the redis db Store COOKIE
 * @method Client CACHE() the redis db Store key value
 * @method Client ELK_LOG() the redis db Store elk log list data
 * @method Client API_LOG() the redis db Store api log data
 * @method Client TOKEN() the redis db Store token
 * @method Client LOCK() the redis db Store lock
 *
 * @package Phpzc\Redis
 */
class RedisCache
{

    static $method = [
        'SESSION'=>'session',
        'COOKIE'=>'cookie',
        'CACHE'=>'cache',
        'ELK_LOG'=>'elk_log',
        'API_LOG' => 'api_log',
        'TOKEN' => 'token',
        'LOCK' => 'lock',
    ];

    static $map = [];

    /**
     * 静态调用魔术方法 自动选择数据库号码 返回redis实例对象 失败抛出异常
     *  可用的方法定义在 $method数组中
     * @param $funcname
     * @param $arguments
     *
     * @return Client
     * @throws RedisException
     */
    public static function __callStatic($funcname, $arguments)
    {
        if(array_key_exists($funcname,self::$method))
        {
            $dbNum = RedisConfig::$configDatabaseNumber[self::$method[$funcname]];

            if( !isset($map[$funcname])){


                $config = RedisConfig::getConfig();

                $params = [
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'database' => $dbNum,
                    'password' => $config['auth'],
                ];
                $obj = new Client($params);


                self::$map[$funcname] = $obj;

            }

            return self::$map[$funcname];

        }else{
            throw new RedisException('methods do not exists ->'.$funcname);
        }
    }
}
