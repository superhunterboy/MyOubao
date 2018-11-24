<?php

require_once('AES/Aes.php');
require_once('AES/AesCtr.php');

class Encrypt {

    /**
     * 密钥信息，加密模块初始化时使用
     * @var type String
     */
    private  static $aConfigs = [
        'default' => [
            'key' => 'J8LFQIU7X8YTFBR9',
            'iv' => 'E3TY470PHGTEV5YB',
        ],
        'db' => [
            'key' => 'VWE31WFAXA35W2OG',
            'iv' => 'RQY68EBRKSS5RFT1',
        ],
    ];
    
    private static function _encode($text, $type = 'default'){
        $aConfig = self::_getConfig($type);
        
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $aConfig['key'], $text, MCRYPT_MODE_CBC, $aConfig['iv']);
        return base64_encode($encrypted);
    }

    private static function _decode($text, $type = 'default'){
        $aConfig = self::_getConfig($type);
        
        $text = base64_decode($text);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $aConfig['key'], $text, MCRYPT_MODE_CBC, $aConfig['iv']);
        return trim($decrypted, "\0");
    }

    public static function db_encode($text) {
        return self::_encode($text, 'db');
    }
    
    public static function db_decode($text) {
        return self::_decode($text ,'db');
    }
    
    public static function encode($text) {
        return self::_encode($text);
    }
    
    public static function decode($text) {
        return self::_decode($text);
    }
    
    private static function _getConfig($id = 'default'){
        if (!isset(self::$aConfigs[$id])){
            throw new Exception('非法的加密格式');
        }
        if (strlen(self::$aConfigs[$id]['key']) != 16 || strlen(self::$aConfigs[$id]['iv']) != 16){
            throw new Exception('非法的密钥格式');
        }
        return self::$aConfigs[$id];
    }

}
