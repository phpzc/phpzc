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
 * Class RedisCache 优先使用扩展版本redis 不存在则使用predis
 *
 *
 * @method \Redis|\Predis\Client|null SESSION() the redis db Store Session
 * @method \Redis|\Predis\Client|null COOKIE() the redis db Store COOKIE
 * @method \Redis|\Predis\Client|null CACHE() the redis db Store key value
 * @method \Redis|\Predis\Client|null ELK_LOG() the redis db Store elk log list data
 * @method \Redis|\Predis\Client|null API_LOG() the redis db Store api log data
 * @method \Redis|\Predis\Client|null TOKEN() the redis db Store token
 * @method \Redis|\Predis\Client|null LOCK() the redis db Store lock
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
     * @return \Redis|\Predis\Client|mixed
     * @throws RedisException
     */
    public static function __callStatic($funcname, $arguments)
    {
        if(array_key_exists($funcname,self::$method))
        {
            $dbNum = RedisConfig::$configDatabaseNumber[self::$method[$funcname]];

            if( !isset($map[$funcname])){


                $config = RedisConfig::getConfig();
                if(extension_loaded('redis')){

                    $obj = new \Redis();
                    if( $obj->connect($config['host'],$config['port'],2) == false ){
                        throw new RedisException('connect redis fail');
                    }

                    if(!empty($config['auth'])){
                        if( $obj->auth($config['auth']) == false){
                            throw new RedisException('redis auth fail');
                        }
                    }
                    $obj->select($dbNum);
                }else{
                    $params = [
                        'host' => $config['host'],
                        'port' => $config['port'],
                        'database' => $dbNum,
                        'password' => $config['auth'],
                    ];
                    $obj = new Client($params);
                }

                self::$map[$funcname] = $obj;

            }

            return self::$map[$funcname];

        }else{
            throw new RedisException('methods do not exists ->'.$funcname);
        }
    }
}
