<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2018/8/30
 * Time: 上午10:19
 */

namespace Phpzc\Session;

use Phpzc\Redis\RedisCache;

class RedisSession implements \SessionHandlerInterface
{
    public static $instance = null;

    protected $lifeTime = 3600;

    public function __construct()
    {

    }

    public static function setExpireTime($time)
    {
        ini_set('session.gc_maxlifetime',$time);

        if(static::$instance){
            static::$instance->lifeTime = $time;
        }
    }


    /**
     * @var \Redis
     */
    public static $redis = null;

    /**
     * @param int  $time session时间
     * @param bool $start 开启session
     */
    public static function init($time = 0,$start = true)
    {

        if($time > 0)
        {
            static::setExpireTime($time);
        }

        if(static::$redis == null)
        {
            static::$redis = RedisCache::SESSION();

            static::$instance = new static();


            session_set_save_handler(static::$instance);
            if($start){
                session_start();
            }

        }


    }

    public function open($savePath, $sessName)
    {
        $this->lifeTime = ini_get('session.gc_maxlifetime');
        return true;
    }

    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        static::$redis->close();
        static::$redis = null;
        return true;
    }

    public function read($sessID)
    {
        //每次读取session重新设置时间
        static::$redis->expire($sessID, $this->lifeTime);

        return static::$redis->get($sessID);
    }

    public function write($sessID, $sessData)
    {
        return static::$redis->setex($sessID, $this->lifeTime,$sessData);
    }

    public function destroy($sessID)
    {
        return static::$redis->delete($sessID);
    }

    public function gc($sessMaxLifeTime)
    {
        return true;
    }




}
