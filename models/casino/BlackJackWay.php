<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-7-4
 * Time: 下午3:23
 */
class BlackJackWay extends BaseModel{
    const BLACKJACK_WAY_SPLIT=1001;
    const BLACKJACK_WAY_HIT=1002;
    const BLACKJACK_WAY_STOP=1003;
    const BLACKJACK_WAY_DOUBLE=1004;
    const BLACKJACK_WAY_INSURANT=1005;
    const BLACKJACK_WAY_BET=1006;
    const BLACKJACK_WAY_PAIR=1007;
    const BLACKJACK_WAY_BANKERHIT=1008;
    protected $table = 'casino_ways';
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $titleColumn            = 'name';


    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'wn_function',
        'method_id',
        'name',

    ];
    public static $listColumnMaps = [
        'lottery_id' => 'friendly_name'
    ];

    protected $fillable = [
        'lottery_id',
        'method_id',
        'wn_function',
        'name',
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    static function getWays($lotteryId){

        return self::where('lottery_id',$lotteryId)->get();
    }


    public function prizePair($playercards,$amount,$bankercards){
       // file_put_contents('/var/mylog/t.log','double|'.$playercards.'|||'.$bankercards.'||'.$amount.'|'.$this->id."\n",FILE_APPEND);
        $playercards = explode(',',$playercards);
        if(empty($playercards) || count($playercards)<2 || $amount ==0){
            return 0;
        }
        if(BlackJackStage::checkPair($playercards)){
            return $amount*12;
        }else{
            return 0;
        }
    }

    public function prizesplit($playercards,$amount,$bankercards){

        $playercards = explode(',',$playercards);
        $bankercards = explode(',',$bankercards);

        $playerPoint = BlackJackStage::getCardsPoint($playercards);
        $bankerPoint = BlackJackStage::getCardsPoint($bankercards);

        if($playerPoint>21){
            return 0 ;
        }
        if($bankerPoint>21) {
            $multiple = 2;

        }elseif($playerPoint == $bankerPoint){
            $multiple = 1;
        }else if($playerPoint>$bankerPoint){
            $multiple = 2;
        }else{
            $multiple = 0;
        }
        $prize = $multiple*$amount;

        return $prize;
    }

    public function prizeDouble($playercards,$amount,$bankercards){
        return $this->prizeStartBet($playercards,$amount,$bankercards);
    }

    public function prizeInsurance($playercards,$amount,$bankercards){
        $playercards = explode(',',$playercards);
        $bankercards = explode(',',$bankercards);
        $bankerBlackJack = BlackJackStage::checkBlackjack($bankercards);
        if($bankerBlackJack){
            $iResult = BlackJackStage::compareCards($playercards,$bankercards);
            if($iResult==2 || $iResult ==5){
                $prize = 2*$amount;
            }else{
                $prize =0;
            }
        }else{
            $prize=0;
        }

        return $prize;
    }

    public function prizeStartBet($playercards,$amount,$bankercards){
        //file_put_contents('/var/mylog/t.log','startbet|'.$playercards.'|||'.$bankercards.'||'.$amount.'|'.$this->id."\n",FILE_APPEND);
        $playercards = explode(',',$playercards);
        $bankercards = explode(',',$bankercards);

        $playerPoint = BlackJackStage::getCardsPoint($playercards);
        $bankerPoint = BlackJackStage::getCardsPoint($bankercards);

        if($playerPoint>21){
            return 0 ;
        }if($bankerPoint>21) {
            $multiple = 2;
            if(BlackJackStage::checkBlackjack($playercards)){
                $multiple=2.5;
            }
        }else
        {

            $iResult = BlackJackStage::compareCards($playercards,$bankercards);

            $multiple = 0;
            switch($iResult){
                case 1:
                    $multiple = 2;
                    break;
                case 2:
                case 5:
                    $multiple = 0;
                    break;
                case 3:
                    $multiple = 1;
                    break;
                case 4:
                    $multiple = 2.5;
            }
        }
        $prize = $multiple*$amount;
        return $prize;
    }



}
