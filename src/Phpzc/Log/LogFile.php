<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2018/1/29
 * Time: 上午10:57
 */

namespace Phpzc\Log;


class LogFile
{
    static $fileName = 'LogFile.txt';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';

    public static function setFileName($fileName)
    {
        self::$fileName = $fileName;
    }

    public static function prefix()
    {
        $time = new \DateTime('now');

        return '['.$time->getTimestamp().'][TimeZone:'.$time->getTimezone()->getName().']['.$time->format('Y-m-d H:i:s.u').']';
    }

    public static function log($str,$level = self::INFO)
    {
        $logPrefix = self::prefix();
        $logLevel = '['.$level.']';
        return file_put_contents(self::$fileName,$logPrefix.$logLevel.$str.PHP_EOL,FILE_APPEND);
    }
}
