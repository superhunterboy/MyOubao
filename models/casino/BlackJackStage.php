<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-7-4
 * Time: 下午2:44
 */

class BlackJackStage extends CasinoBasic{

    const BJ_STAGE_INIT_ERR=-301;
    const BJ_STAGE_SUCCESS=300;
    protected $stageFields = ['cards','amount','pair_amount','is_pair','double_amount','father_table_id','split_table_id','insurance_amount','stop','bust','blackjack'];
    public $redis_key = NULL;
    public $stageInfo=NULL;
    function __construct($oLottery, $oTable,$oUser)
    {
        parent::__construct($oLottery, $oTable,$oUser);
        $this->redis_key = $oLottery->model_name.':'.$oUser->id.':'.$oLottery->id.':'.$oTable->id.':stage';
    }
    public function initStage($aBetData,$jacpotEnough,&$jumpCard){

        //初始化开奖号
        try {
            $stages = $aBetData['stage'];
            //var_dump($jacpotEnough);
            $jumpCard=[];
            if (!empty($stages)) {
                $oPlayerCards = [];
                foreach ($stages as $tableid => $stage) {
                    $oPlayerCards[$tableid] = $stage;
                    $oPlayerCards[$tableid]['cards'][0] = $this->oEncode->getCards();
                }
                $oBankerStage = [];
                $oBankerStage['cards'][0] = $this->oEncode->getCards();
                foreach ($stages as $tableid => $stage) {
                    $jumpCard[$tableid]=[];
                    if ($jacpotEnough) {
                        $oPlayerCards[$tableid]['cards'][1] = $this->oEncode->getCards();
                       // pr($tableid.'1');
                    } else {
                        $second_pos = 0;
                        $hasNum=true;
                        do {

                            unset($oPlayerCards[$tableid][1]);
                            $jumpCard[$tableid][] = $oPlayerCards[$tableid]['cards'][1] = $this->oEncode->getCards($second_pos);

                            if (empty($oPlayerCards[$tableid]['cards'][1])) {
                                $hasNum = false;
                                $jumpCard[$tableid][] = $oPlayerCards[$tableid]['cards'][1] = $this->oEncode->getCards();
                                break;
                            }
//                            pr($tableid.'---'.$oPlayerCards[$tableid]['cards'][1]);
//                            pr($stage);
                            if (isset($stage[BlackJackWay::BLACKJACK_WAY_PAIR]) && $stage[BlackJackWay::BLACKJACK_WAY_PAIR] > 0) {

                                $isPair = $this->checkPair($oPlayerCards[$tableid]['cards']);

                            } else {
                                $isPair = false;
                            }
                            $is21 = $this->check21($oPlayerCards[$tableid]['cards']);

                            //var_dump($isPair,$is21);
                            $second_pos++;
                        } while ($isPair || $is21);
                        if($hasNum){
                            $del = $this->oEncode->delEncodeNum($oPlayerCards[$tableid]['cards'][1]);
                        }
                    }
                }
                if($this->getCardsPoint($oBankerStage['cards'][0]) == 10){
                    $bankerNewCard = $this->oEncode->getCards(0);
                    if($this->getCardsPoint($bankerNewCard) == 11){
                        $this->oEncode->delEncodeNum($bankerNewCard);
                        $oBankerStage['cards'][1] = $bankerNewCard;
                    }
                }

                $this->stageInfo =  ['banker'=>$oBankerStage,'player'=>$oPlayerCards];
            }
        }catch(Exception $e){

            return self::BJ_STAGE_INIT_ERR;
        }
        return self::BJ_STAGE_SUCCESS;
    }


    public function compilePlayerStageData(){

            foreach ($this->stageInfo['player'] as &$stage) {
                $cards = $stage['cards'];
                unset($stage['cards']);
                $aAmount = [];
                foreach ($stage as $wayId => $amount) {
                    $aAmount[$wayId] = $amount;
                    unset($stage[$wayId]);
                }

                $stage['cards'] = implode(',', $cards);
                $stage['amount'] = $aAmount;
                $oCasinoWay = BlackJackWay::find(BlackJackWay::BLACKJACK_WAY_PAIR);
                $pairAmount = isset($aAmount[BlackJackWay::BLACKJACK_WAY_PAIR])?$aAmount[BlackJackWay::BLACKJACK_WAY_PAIR]:0;
                $stage['is_pair'] = $oCasinoWay->{'prize'.$oCasinoWay->wn_function}($stage['cards'],$pairAmount,'');

                $stage['father_table_id'] = 0;
                $stage['double'] = BlackJack::BLACKJACK_STATUS_DOUBLE_ABLE;
                $stage['split_table_id'] = 0;
                $stage['stop'] = $this->check21($cards) ? 1 : 0;
                $stage['bust'] = $this->checkBust($cards) ? 1 : 0;
                $stage['blackJack'] = $this->check21($cards) ? 1 : 0;
                $firstCardsPoint = $this->getCardsPoint($this->stageInfo['banker']['cards'][0]);
                $stage['insurance'] = $firstCardsPoint==11?BlackJack::BLACKJACK_STATUS_INSURANCE_ABLE:BlackJack::BLACKJACK_STATUS_INSURANCE_UNABLE;
                $stage['hitAbleCount'] = 10;
            }
    }
     static public function createSplitStage($cards ,$stageInfo,$aExtra=array()){

        $aAmount=[];
        foreach ($stageInfo as $wayId => $amount) {
            if(!in_array($wayId,array(BlackJackWay::BLACKJACK_WAY_BET)))
            $aAmount[$wayId] = $amount;
            unset($stageInfo[$wayId]);
        }
        $stage['cards'] = $cards;
        $stage['amount'] = $aAmount;
        $stage['is_pair'] = 0;
        $stage['double'] = BLACKJACK::BLACKJACK_STATUS_DOUBLE_ABLE;
        $stage['split_table_id'] = BLACKJACK::BLACKJACK_STATUS_SLIP_UNABLE;
        $stage['stop'] = BLACKJACK::BLACKJACK_STATUS_SLIP_ABLE;
        $stage['bust'] = 0;
        $stage['blackJack'] = 0;
        $stage['insurance'] = BLACKJACK::BLACKJACK_STATUS_INSURANCE_UNABLE;
        $stage['hitAbleCount'] = 10;

        $stage+=$aExtra;
        return $stage;
    }

    public function saveStage($value){
        $stage = json_encode($value);
        return $this->redis->hSet($this->redis_key,'stage',$stage);
    }

    public function getStage($key=NULL){
        $stage = $this->redis->hget($this->redis_key,'stage');

        return json_decode($stage,true);
    }
    public function saveGameInfo($gameInfo){
        if(isset($gameInfo['gameInfo']['manProjectId'])){
            //$result = BlackJackProject::where('id',$gameInfo['gameInfo']['manProjectId'])->update(['game_info'=>json_encode($gameInfo)]);
            $this->writeLog(json_encode($gameInfo));
        }
        $this->redis->multi();
        $this->saveStage($gameInfo);
        return $re = $this->redis->exec();

    }
    public function delStage(){
       return $r =  $this->redis->del($this->redis_key);
    }
    static public function getRealCard($card){
        return  substr($card,-2);
    }

    static public function checkPair(&$cards){
        $first = self::getRealCard($cards[0]);

        $second = self::getRealCard($cards[1]);
        if(substr($first,-2) == substr($second,-2))
            return true;
        else
            return false;

    }
    static function check21($oPlayerCards){

        $cards_point = self::getCardsPoint($oPlayerCards);

        return  $cards_point==21? true: false;
    }
    static function checkBust($oPlayerCards){
        $cards_point = self::getCardsPoint($oPlayerCards);
        return  $cards_point>21? true: false;
    }
    static public function getCardsPoint($cards){
        if(!is_array($cards))
            $cards = array($cards);
        $cards_point = [];
        $blackJackIndex=false;

        foreach($cards as $index => $card){

            $card = self::getCardNum($card);
            if($card == 1){
                $blackJackIndex = $index;
            }
            $cards_point[]= $card;
        }
        $blackJackCardSum = 0;

        if($blackJackIndex !== false){
            $blackJackCard = $cards_point;
            $blackJackCard[$blackJackIndex] = 11;

            if(($sum=array_sum($blackJackCard))<=21){
                $blackJackCardSum = $sum;
            }
        }
        return max(array_sum($cards_point),$blackJackCardSum);
    }

    static public function checkBlackjack($cards){
        $cardPoint = self::getCardsPoint($cards);
        if($cardPoint==21 && count($cards) ==2){
            return true;
        }else{
            return false;
        }
    }
   static public function compareCards($playercard,$banercard){

       $card1BlackJack = self::checkBlackjack($playercard);
//       var_dump($card1BlackJack);
//       pr($playercard);

       $card2BlackJack = self::checkBlackjack($banercard);
//       var_dump($card2BlackJack);
//       pr($banercard);
       if($card1BlackJack && $card2BlackJack){
           return 3;
       }else if($card1BlackJack && !$card2BlackJack){
           return 4;
       }else if(!$card1BlackJack && $card2BlackJack){
           return 5;
       }else{
           $card1Point = self::getCardsPoint($playercard);
           $card2Point = self::getCardsPoint($banercard);
           if($card1Point == $card2Point){
               return 3;
           }else if($card1Point>$card2Point){
               return 1;
           }else{
               return 2;
           }
       }
    }

    static public function getCardNum($card){
        $card = substr($card,-2);
        return ((int)$card/10)>1?10:(int)$card;;
    }

    public  function writeLog($data)
    {

        $dir = '/tmp/blackjack/'.date('Y-m-d');
        $sFile = $dir.'/gameInfo.log-'.$this->oUser->id.'-'.date('H');;
        $arr = explode('/', $dir);
        $aimDir = '';
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                mkdir($aimDir);
                @chmod($aimDir, 0777);
            }
        }
        if(!file_exists($sFile)){
            touch($sFile);
            chmod($sFile, 0777);
        }
        $bSucc = file_put_contents($sFile, '['.date('H:i:s').']  '.$data.'|'.$this->redis_key."\n",FILE_APPEND);

        return $bSucc;
    }
}