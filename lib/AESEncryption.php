<?php

require_once('AES/PadCrypt.php');

/**
 * 该类时AES加密和解密的工具类，默认采用AES256加密，可以扩展其他位数的加密方式
 * 该类依赖于 PHP's mcrypt扩展和用于填充原文的Padrypt类
 * 
 * PHP's mcrypt扩展可参考：http://us.php.net/mcrypt
 * 
 * 用法:
 * $key 	= "bac09c63f34c9845c707228b20cac5e0";
 * $iv 		= "47c743d1b21de03034e0842352ae6b98";
 * $message = "Meet me at 11 o'clock behind the monument.";
 * 
 * $AES              = new AES_Encryption($key, $iv);
 * $encrypted        = $AES->encrypt($message);
 * $decrypted        = $AES->decrypt($encrypted);
 * 
 */
class AESEncryption {

    /**
     * 密钥信息，加密模块初始化时使用
     * @var type String
     */
    private $key;

    /**
     * 初始化矢量，加密模块初始化时使用
     * @var type String
     */
    private $initVector;

    /**
     * 算法名称 ： rijndael-128，rijndael-192，rijndael-256(默认)
     * @var type String
     */
    private $algorithmName = 'rijndael-256';

    /**
     * 加密模式,有："ecb"，"cfb"，"cbc"，"nofb"，"ofb" 和 "stream"
     * @var type String
     */
    private $mode;

    /**
     * 加密描述符
     * @var type Resource
     */
    private $cipher;

    /**
     * 密钥字节数
     * @var type integer
     */
    private $encryption = null;

    /**
     * 允许的bit位数
     * @var type array
     */
    private $allowed_bits = array(128, 192, 256);

    /**
     * 允许的加密模式
     * @var type array
     */
    private $allowed_modes = array('ecb', 'cfb', 'cbc', 'nofb', 'ofb');

    /**
     * 需要用到初始矢量的加密模式
     * @var type array
     */
    private $vector_modes = array('cfb', 'cbc', 'nofb', 'ofb');

    /**
     * 填充方式
     * @var type array
     */
    private $allowed_paddings = array(
        'ANSI_X.923' => 'ANSI_X923',
        'ISO_10126' => 'ISO_10126',
        'PKCS7' => 'PKCS7',
        'BIT' => 'BIT',
        'ZERO' => 'ZERO',
    );

    /**
     * String $key        密钥，用来加密和解密
     * String $initVector 初始矢量，只有在ECB加密模式下可忽略
     * String $padding    填充方式，默认ZERO
     * String $mode       加密模式，默认CBC
     */
    public function __construct($key, $initVector = '', $padding = 'PKCS7', $mode = 'cbc') {
        $mode = strtolower($mode);
        $padding = strtoupper($padding);

        if (!class_exists('PadCrypt')) {
            throw new Exception('需要加载PadCrypt类');
        }

        if (!function_exists('mcrypt_module_open')) {
            throw new Exception('需要加载mcryp扩展');
        }

        if (strlen($initVector) != 32 && in_array($mode, $this->vector_modes)) {
            throw new Exception('在CBC, CFB, NOFB，OFB和STREAM模式下，$initVector 应该是 32 bytes.');
        } elseif (!in_array($mode, $this->vector_modes) && !empty($initVector)) {
            throw new Exception('加密模式与初始矢量不匹配.');
        }

        $this->encryption = strlen($key) * 8;

        if (!in_array($this->encryption, $this->allowed_bits)) {
            throw new Exception('密钥的字节数必须是128，196或者256 bytes.');
        }

        $this->key = $key;
        $this->initVector = $initVector;

        if (!in_array($mode, $this->allowed_modes)) {
            throw new Exception('加密模式应为如下中的一个: ' . implode(', ', $this->allowed_modes));
        }

        if (!array_key_exists($padding, $this->allowed_paddings)) {
            throw new Exception('填充模式应为如下中的一个: ' . implode(', ', $this->allowed_paddings));
        }

        $this->mode = $mode;
        $this->padding = $padding;
        //MCRYPT_RIJNDAEL_256
        $this->cipher = mcrypt_module_open($this->algorithmName, '', $this->mode, '');
        $this->block_size = mcrypt_get_block_size($this->algorithmName, $this->mode);
    }

    /**
     * 加密数据，这里默认采用AES-256的加密方式，主要包括如下几步：
     * 1.mcrypt_generic_init：初始化加密模块，创建加密需要的信息到缓存中
     * 2.mcrypt_generic：加密数据
     * 3.mcrypt_generic_deinit:终止加密，清除缓存中的初始化信息，但不关闭该模块
     * 4.mcrypt_module_close：关闭加密模块
     * 
     * @param type $text
     * @return type 加密后的信息
     */
    public function encrypt($text) {
        mcrypt_generic_init($this->cipher, $this->key, $this->initVector);
        $encrypted_text = mcrypt_generic($this->cipher, $this->pad($text, $this->block_size));
        mcrypt_generic_deinit($this->cipher);
        return base64_encode($encrypted_text);
    }

    /**
     * 解密数据，这里默认采用AES-256的解密方式，主要包括如下几步：
     * 1.mcrypt_generic_init：初始化加密模块，创建加密需要的信息到缓存中
     * 2.mdecrypt_generic：解密数据
     * 3.mcrypt_generic_deinit:终止解密，清除缓存中的初始化信息，但不关闭该模块
     * 4.mcrypt_module_close：关闭解密模块
     * 
     * @param type $text
     * @return type 解密后的信息
     */
    public function decrypt($text) {
        mcrypt_generic_init($this->cipher, $this->key, $this->initVector);
        $decrypted_text = mdecrypt_generic($this->cipher, base64_decode($text));
        mcrypt_generic_deinit($this->cipher);
        return $this->unpad($decrypted_text);
    }

    /**
     * 获取加密算法的所有配置信息，主要包括：
     * 1.key：密钥
     * 2.init_vector：初始化矢量
     * 3.padding：填充方式
     * 4.mode：加密模式
     * 5.encryption：密钥bit数
     * 6.block_size：指定加密算法与模式下的块大小
     * @return type array
     */
    public function getConfiguration() {
        return array(
            'key' => $this->key,
            'init_vector' => $this->initVector,
            'padding' => $this->padding,
            'mode' => $this->mode,
            'encryption' => $this->encryption . ' Bit',
            'block_size' => $this->block_size,
        );
    }

    /**
     * @param type $text    原文内容
     * @param type $block_size  指定加密算法与模式下的块大小
     * @return type 填充块大小后的字符串，这样原文就是块大小的整数倍
     */
    private function pad($text, $block_size) {
        return call_user_func_array(array('PadCrypt', 'pad_' . $this->allowed_paddings[$this->padding]), array($text, $block_size));
    }

    private function unpad($text) {
        return call_user_func_array(array('padCrypt', 'unpad_' . $this->allowed_paddings[$this->padding]), array($text));
    }

    public function __destruct() {
        mcrypt_module_close($this->cipher);
    }

    public static function getAESEncryptionInstance() {
        $sKey = Config::get('mobile_aes.key');
        //获取初始矢量
        $sIv = Config::get('mobile_aes.init_vector');
        //获取填充方式
        $sPadding = Config::get('mobile_aes.padding');
        //获取加密模式
        $sMode = Config::get('mobile_aes.mode');
        //对webservice端传递参数进行aes256加密
        $oAes = new AESEncryption($sKey, $sIv, $sPadding, $sMode);
        return $oAes;
    }

}
