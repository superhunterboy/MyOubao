
<?php
class RandomNumberFromMmc
{
    //don't change the key and iv
//    private $key = "5185e8b8fd8a71fc80545e144f91faf2";
    private $key;
//    private $iv = "51727d6a52ede7267e7def085d015633";
    private $iv;


    //you can update ip address
    public $server_path;
//    private $server_path = "http://10.3.4.83/rng/retrieve.asmx?WSDL";

//    function RandomNumber() { }

    function __construct(){
        $this->key = SysConfig::readValue('rng_key');
        $this->iv = SysConfig::readValue('rng_iv');
        $this->server_path = SysConfig::readValue('rng_server_address1');
    }

    function grabNumber($platform, $project_key, $project_time, $draw_count, $lottery_type)
    {

        $param = $this->aes256cbcEncrypt("platform=$platform&project_key=$project_key&project_time=$project_time&draw_count=$draw_count");

        return $this->callApi($this->server_path, $lottery_type, $param);
    }

    function callApi($server_path, $game, $param)
    {
        include_once('nusoap.php');
        $client = new nusoap_client($server_path, true);
        $err = $client->getError();
        if ($err)
        {
            echo $err;
            return false;
        }

        $result = $client->call($game, array('param'=>$param));
//        die(print_r($result));
        $arr_idx = $game . "Result";
//        echo $result[$arr_idx] . "<br>";
//        echo $this->aes256cbcDecrypt($result[$arr_idx]) . "<br>";
        return $this->aes256cbcDecrypt($result[$arr_idx]);
    }

    function addPkcs7Padding($string, $blocksize = 32) {
        $len = strlen($string); //取得字符串长度
        $pad = $blocksize - ($len % $blocksize); //取得补码的长度
        $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
        return $string;
    }

    /**
     * 加密然后base64转码
     *
     * @param String 明文
     * @param 加密的初始向量（IV的长度必须和Blocksize一样， 且加密和解密一定要用相同的IV）
     * @param $key 密钥
     */
    function aes256cbcEncrypt($str) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $this->addPkcs7Padding($str) , MCRYPT_MODE_CBC, $this->iv));
    }

    /**
     * 除去pkcs7 padding
     *
     * @param String 解密后的结果
     *
     * @return String
     */
    function stripPkcs7Padding($string){
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if(preg_match("/$slastc{".$slast."}/", $string)){
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }

    /**
     * 解密
     *
     * @param String $encryptedText 二进制的密文
     * @param String $iv 加密时候的IV
     * @param String $key 密钥
     *
     * @return String
     */
    function aes256cbcDecrypt($encryptedText) {
        $encryptedText = base64_decode($encryptedText);
        return $this->stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $encryptedText, MCRYPT_MODE_CBC, $this->iv));
    }
}
?>