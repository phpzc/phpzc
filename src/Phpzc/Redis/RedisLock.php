<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2018/8/2
 * Time: 下午12:07
 */

namespace Phpzc\Redis;

use Predis\Client;

class RedisLock
{

    /**
     * @return Client
     */
    public static function getCache()
    {
        return RedisCache::CACHE();
    }

    /**
     * 取锁成功 返回加锁设置的值
     * @param string $key
     * @param $lockValue
     * @param int $timeout
     * @return bool
     */
    public static function getUniqueLock($key,$lockValue,$timeout = 30)
    {
        $redisKey = __CLASS__.'@'.$key;

        $cache = self::getCache();

        $result = $cache->set($redisKey, $lockValue, 'EX', $timeout, 'NX');

        return 'OK' === (string)$result;
    }

    /**
     * 获得锁的请求的程序 主动释放
     * @param string $key
     * @param $lockValue  上次加锁时 指定的数值
     *
     * @return bool
     */
    public static function releaseLock($key,$lockValue)
    {
        $redisKey = __CLASS__.'@'.$key;

        $cache = self::getCache();

        $lua = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('del', KEYS[1]) else return 0 end";

        $result = $cache->eval($lua, 1, $redisKey, $lockValue);
        return 1 === $result;

    }
}
