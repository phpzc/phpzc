<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2017/10/24
 * Time: 上午9:47
 */
namespace Phpzc\Curl;


class Curl
{

    /**
     * @var \Phpzc\Curl\Curl
     */
    private static $ch_instance = null;

    /**
     * @var 唯一curl handler ,resource a cURL handle on success, false on errors.
     */
    private $ch;

    private function __construct()
    {

    }

    /**
     * @return Curl
     */
    public static function getSingleInstance()
    {
        if(Curl::$ch_instance == null)
        {
            Curl::$ch_instance = new Curl();
            Curl::$ch_instance->ch = curl_init();
        }

        return Curl::$ch_instance;
    }

    /**
     * @return mixed
     */
    public static function getCurlHandler()
    {
        return Curl::getSingleInstance()->ch;
    }


    /**
     * 上传文件数据
     * @param $filedName 字段名称
     * @param $fileName 本地文件名
     *
     * @return array
     */
    public function getFilePostData($filedName,$fileName)
    {
        return [ strval($filedName) => new \CURLFile($fileName)];
    }


    function __destruct()
    {
        if($this->ch)
        {
            curl_close($this->ch);
        }
    }


}