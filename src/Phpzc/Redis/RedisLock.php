<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2018/8/2
 * Time: 下午12:07
 */

namespace Phpzc\Redis;


class RedisLock
{
    public static function getCache()
    {
        return RedisCache::CACHE();
    }

    /**
     * 取锁成功 返回加锁设置的值
     * @param $key
     *
     * @return bool|int
     */
    public static function getUniqueLock($key)
    {
        $redisKey = 'redis_lock_'.$key;

        $cache = self::getCache();

        $expireTime = time() + 30;

        if($cache->set($redisKey,$expireTime, 30))
        {

            return $expireTime;
        }else{

            return false;
        }

    }

    /**
     * 获得锁的请求的程序 主动释放
     * @param $key
     * @param $lock_value  上次加锁时 指定的数值
     *
     * @return int
     */
    public static function releaseLock($key,$lock_value)
    {
        $redisKey = 'redis_lock_'.$key;

        $cache = self::getCache();


        //lua代码 原子删除
        $lua_code=<<<EOT
        if redis.call("get",KEYS[1]) == ARGV[1] then
            return redis.call("del",KEYS[1])
        else
            return 0
        end
EOT;

        return $cache->eval($lua_code,[$redisKey,$lock_value],1);
    }
}