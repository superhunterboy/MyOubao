<?php
use Illuminate\Support\Facades\Redis as Redis;
class BlackJackEncode{
    protected $oLottery;
    protected $oTable;
    protected $redis;
    protected $oUser;
    protected $redis_key;
    protected $encodeNums=NULL;
    protected $redis_finish_count_key;
    private $maxHitCount = 164;
    function __construct($oLottery,$oTable,$oUser)
    {
        $this->oUser = $oUser;
        $this->oLottery = $oLottery;
        $this->oTable = $oTable;
        $this->redis = Redis::connection('default');;
        $this->redis_key = $oLottery->model_name.':'.$oUser->id.':'.$oLottery->id.':'.$oTable->id;
        $this->redis_finish_count_key = $oLottery->model_name.':'.$oUser->id.':'.$oLottery->id.':'.$oTable->id.':position';
    }
    public function isEmptyCards(){

        $this->encodeNums = $this->redis->lrange($this->redis_key,0,-1);
        if(empty($this->encodeNums))
            return true;
        else
            return false;
    }
    public function initCards($force = false){
        if($this->isEmptyCards() || $force){
            $iResult = $this->getCodeFromMmc();
            if(!$iResult){
                return BlackJack::BLACKJACK_ENCODE_INIT_ERROR;
            }

            if(empty($this->encodeNums) || is_null($this->encodeNums)){
                //$this->encodeNums = [ 308,406,110,407,202,104,310,111,401,109,104,408,205,209,209,108,410,107,413,313,413,101,409,408,205,110,106,105,312,402,311,204,105,404,306,106,405,410,310,407,201,311,109,108,113,307,408,204,103,201,310,108,412,202,313,208,410,402,101,301,209,405,102,303,112,401,413,406,305,313,113,308,101,410,307,304,413,213,413,111,102,213,309,205,210,302,411,303,406,213,412,312,202,402,411,403,113,313,409,401,112,109,306,211,106,101,305,403,311,105,405,110,412,212,109,107,411,201,304,309,303,103,201,208,407,311,109,203,113,410,207,302,212,306,301,302,212,110,305,206,308,401,403,211,106,101,309,212,104,411,307,102,308,301,305,210,404,201,412,104,309,110,102,407,411,104,106,405,206,408,103,403,211,303,402,111,404,304,302,404,311,410,210,106,409,207,407,310,204,212,112,309,305,105,103,209,407,406,203,111,303,111,207,404,212,211,206,308,210,208,207,206,101,304,309,207,408,307,306,108,411,112,204,303,409,405,307,205,102,211,204,412,210,312,203,207,108,403,201,211,110,202,109,213,401,304,103,209,111,310,406,111,402,203,102,202,301,107,406,203,107,103,302,305,208,312,204,408,403,412,112,209,112,409,213,105,105,306,107,312,313,210,407,203,202,301,205,413,312,113,304,313,307,311,208,306];
            }

            foreach($this->encodeNums as $num){
                $this->redis->rpush($this->redis_key,$num);
            }
            if($this->redis->exists($this->redis_finish_count_key)){
                $this->redis->set($this->redis_finish_count_key,0);
            }
        }
        $this->writeLog(implode(',',$this->encodeNums));
        return $this->encodeNums;
    }

    private function getCodeFromMmc(){
        $obj_rng = new RandomNumberFromMmc();
        $i = 0;
        do{
            $i++;
            $grab_result = $obj_rng->grabNumber('BM', 'BLACKJACK', date('Y-m-d H:i:s'), 1, 'BLACKJACK');
            if(!$grab_result) sleep(1);
        }while(!$grab_result && $i < 3);
        if(!$grab_result){
            return false;
        }

        $grab_result = str_replace('result=', '', $grab_result);
        $encodes = explode(',',$grab_result);
        $this->writeLog($grab_result);
        foreach($encodes as $index => $code){
            $cardIndex = (int)($code-1)%52+1;
            $cardSuit = (int)(($cardIndex-1)/13)+1;
            $cardPoint = ($cardIndex-1)%13 +1;
            $cardPoint = $cardPoint>=10?$cardPoint:'0'.$cardPoint;

            $card = $cardSuit.$cardPoint;
            $this->encodeNums[$index] = $card ;
        }

        return true;
    }


    public function saveEncodeCards($encodeCards){
        foreach($encodeCards as $num){
            $this->redis->rpush($this->redis_key,$num);
        }
    }
    public function getCards($position=NULL){
        if(is_null($position)){
            $card = $this->redis->lpop($this->redis_key);
            $this->setFinishedEncodeCount($iCount);
        }else{
            $card = $this->redis->lindex($this->redis_key,$position);
        }

        return $card ;
    }
    public function getAllCards(){
        return $this->redis->lrange($this->redis_key,0,-1);
    }

    public function delEncodeNum($val){
        $result = $this->redis->lRem($this->redis_key, 1, $val);
        $this->setFinishedEncodeCount($iCount);
        return $result;
    }

    public function setFinishedEncodeCount(&$count){
        $this->redis->incr($this->redis_finish_count_key);
        $count = $this->redis->get($this->redis_finish_count_key);
        if($count>$this->maxHitCount){
            $this->delEncodeNums();
            $this->initCards(true);
        }
    }
    public function delEncodeNums(){
        $this->redis->del($this->redis_key);
    }

    public function writeLog($data,$fileName='')
    {

        $dir = '/tmp/blackjack/'.date('Y-m-d');
        if($fileName == '')
            $sFile = $dir.'/encode.log-'.$this->oUser->id.'-'.date('H');
        else
            $sFile = $dir.'/'.$fileName.'.log-'.$this->oUser->id.'-'.date('H');
        $arr = explode('/', $dir);
        $aimDir = '';
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                mkdir($aimDir);
            }
        }
        if(!file_exists($sFile)){
            touch($sFile);
            chmod($sFile, 0755);
        }
        $bSucc = file_put_contents($sFile, '['.date('H:i:s').']  '.$data.'|'.$this->redis_key."\n",FILE_APPEND);

        return $bSucc;
        // $sKey = $aReturn['successful'] ? 'success' : 'error';
        // return $this->goBackToIndex($sKey, $aReturn['message']);
    }
}