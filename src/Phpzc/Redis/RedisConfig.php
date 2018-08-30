<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2017/10/24
 * Time: 上午9:47
 */
namespace Phpzc\Redis;


/**
 * redis用途 编号 配置
 * Class RedisConfig
 *
 * @package Phpzc\Redis
 */
class RedisConfig
{
    public static $config = [
        'host'=>'127.0.0.1',
        'port'=>6379,
        'auth'=>'',
        'db'=>0,
    ];


    /**
     * 获取redis配置
     * @return mixed
     */
    public static function getConfig()
    {
        return static::$config;
    }

    /**
     * 设置redis配置
     * @param        $host
     * @param        $port
     * @param string $auth
     * @param int    $db
     */
    public static function setConfig($host,$port,$auth='',$db = 0)
    {
        static::$config['host'] = $host;
        static::$config['port'] = $port;
        static::$config['auth'] = $auth;
        static::$config['db'] = $db;
    }


    static $configDatabaseNumber = [
        'session' => 1,
        'cookie' => 2,
        'cache' => 3,
        'elk_log' => 4,
        'api_log' => 4,
        'token' => 5,
        'lock' => 6,
    ];

}