
<?php
class RandomNumber
{

    private $key;
    private $iv;

    //you can update ip address
    public $first_grab_server_path;
    public $second_grab_server_path;


//    function RandomNumber() { }

    function __construct(){
        $this->key = SysConfig::readValue('rng_key');
        $this->iv = SysConfig::readValue('rng_iv');
    }

    function grabNumber($lot_name, $issue, $time)
    {


        $param = $this->aes256cbcEncrypt("lot_name=$lot_name&issue=$issue&time=$time");

        $iNum1 = str_replace('number=','',$this->callApi($this->first_grab_server_path, "GrabRandomNumber", $param));
        $iNum2 = str_replace('number=','',$this->callApi($this->second_grab_server_path, "GrabRandomNumber", $param));
        if($iNum1 == 'result=no match number' || $iNum2 == 'result=no match number') return false;
        return $iNum1 == $iNum2 ? $iNum1 : false;
    }

    function callApi($server, $action, $param)
    {
        include_once('nusoap.php');
        $client = new nusoap_client($server, true);
        $err = $client->getError();
        if ($err)
        {
            echo $err;
            return false;
        }

        $result = $client->call($action, array('param'=>$param));
        $arr_idx = $action . "Result";
        if(!is_array($result)) return false;//添加返回的数据是否为数组
        if(!array_key_exists($arr_idx,$result)){
            file_put_contents('/tmp/endless',$this->aes256cbcDecrypt($param).' date:'.date('Y-m-d H:i:s')."\n\r",FILE_APPEND);
            return false;
        }else{
            return $this->aes256cbcDecrypt($result[$arr_idx]);
        }


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