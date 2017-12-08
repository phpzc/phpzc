<?php
/**
 * Created by PhpStorm.
 * User: zhangcheng
 * Date: 2017/12/8
 * Time: 下午3:20
 */

namespace Phpzc\Openssl;


class Crypt
{
    private $pubKey = null;
    private $priKey = null;

    /**
     * 构造函数
     *
     * @param string 公钥（验签和加密时传入） pem格式文本
     * @param string 私钥（签名和解密时传入） pem格式文本
     */
    public function __construct($private_key, $public_key)
    {
        $this->priKey = openssl_get_privatekey($private_key);
        $this->pubKey = openssl_get_publickey($public_key);
    }


    /**
     * 生成签名
     * 生成时使用SHA256算法加密
     * @param string 签名材料
     * @param string 签名编码（base64/hex/bin）
     * @return 签名值
     */
    public function sign($data, $code = 'base64'){
        $ret = false;
        if (openssl_sign($data, $ret, $this->priKey, OPENSSL_ALGO_SHA256)){
            $ret = $this->_encode($ret, $code);
        }
        return $ret;
    }

    /**
     * 生成签名 针对url传输 使用base64 替换url中会冲突字符 +/= 为 -_%
     * 生成时使用SHA256算法加密
     * @param string 签名材料
     * @param string 签名编码（base64/hex/bin）
     * @return 签名值
     */
    public function signForUrl($data)
    {
        $ret = false;
        if (openssl_sign($data, $ret, $this->priKey, OPENSSL_ALGO_SHA256)){
            $ret = $this->_encode($ret, 'base64');
            $ret = strtr($ret,'+/=', '-_%');
        }
        return $ret;
    }

    private function _encode($data, $code){
        switch (strtolower($code)){
            case 'base64':
                $data = base64_encode(''.$data);
                break;
            case 'hex':
                $data = bin2hex($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    /**
     * 验证签名
     * 验证时使用SHA256算法解密
     * @param string 签名材料
     * @param string 签名值
     * @param string 签名编码（base64/hex/bin）
     * @return bool
     */
    public function verify($data, $sign, $code = 'base64'){
        $ret = false;
        $sign = $this->_decode($sign, $code);
        if ($sign !== false) {
            switch (openssl_verify($data, $sign, $this->pubKey, "SHA256")){
                case 1: $ret = true; break;
                case 0:
                case -1:
                default: $ret = false;
            }
        }

        return $ret;
    }

    private function _decode($data, $code){
        switch (strtolower($code)){
            case 'base64':
                $data = base64_decode($data);
                break;
            case 'hex':
                $data = $this->_hex2bin($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    private function _hex2bin($hex = false){
        $ret = $hex !== false && preg_match('/^[0-9a-fA-F]+$/i', $hex) ? pack("H*", $hex) : false;
        return $ret;
    }



    /**
     *
     *       对应java版 AES/CBC/PKCS5Padding
     *
     * @param $encryptStr 待加密字符串
     * @param $encryptKey AES加密key
     * @param $encryptIv  密钥偏移向量
     *
     * @return string
     */
    public static function encrypt($encryptStr,$encryptKey,$encryptIv) {

        return base64_encode(openssl_encrypt($encryptStr,'aes-128-cbc',$encryptKey,OPENSSL_RAW_DATA,$encryptIv));

    }

    /**
     * 解密
     *      对应java版 AES/CBC/PKCS5Padding
     * @param $encryptStr 待解密字符串
     * @param $encryptKey AES加密key
     * @param $encryptIv  密钥偏移向量
     *
     * @return bool|string
     */
    public static function decrypt($encryptStr,$encryptKey,$encryptIv) {

        $encryptedData = base64_decode($encryptStr);
        $encryptedData = openssl_decrypt($encryptedData, 'aes-128-cbc',$encryptKey,OPENSSL_RAW_DATA,$encryptIv);
        return $encryptedData;
    }
}