<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-7-4
 * Time: 下午3:23
 */
class BlackJackTable extends BaseModel{
    protected $table = 'casino_tables';
    protected $max_multiple=3;
    protected $min_multiple=1;
    protected $iRequestPrize=0;
    protected $iAmount = 0;
    static $stage = array(1,2,3);
    static $cacheLevel = self::CACHE_LEVEL_FIRST;


    const ERRNO_LOTTERY_MISSING = -504;
    const ERRNO_LOTTERY_CLOSED = -505;


    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [

        'table_name',
        'max_bet',
        'min_bet',
        'pair_max_bet',
        'pair_min_bet',
        'jacpot_id',
        'small_max_prize',
        'max_prize',
        'open'
    ];
    public static $listColumnMaps = [

    ];

    protected $fillable = [

        'table_name',
        'max_bet',
        'min_bet',
        'pair_max_bet',
        'pair_min_bet',
        'jacpot_id',
        'small_max_prize',
        'max_prize',
        'open',
    ];
    public static $rules = [

        'table_name'=>'required',
        'jacpot_id'=>'required',
        'max_bet'=>'required',
        'min_bet'=>'required',
        'pair_max_bet'=>'required',
        'pair_min_bet'=>'required',
        'small_max_prize'=>'required',
        'max_prize'=>'required',
        'open'=>'required',

    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    public function initTable($aBetData){
        $this->aBetData = $aBetData;
        $this->iAmount = $iTotalAmount = $this->getBetAmount();
        $multiple=$iTotalAmount<$this->small_max_prize?$this->min_multiple:$this->max_multiple;
        $iRequestPrize = $iTotalAmount*$multiple;
        $this->iRequestPrize = min($this->max_prize,$iRequestPrize);
    }
    public function getBetAmount(){

        $stages = $this->aBetData['stage'];
        $totalAmount = 0;
        if(!empty($stages) && is_array($stages))
            foreach($stages as $stage){
                if(!empty($stage))
                    foreach($stage as $wayId => $amount){

                        $totalAmount += $amount;
                    }
            }
        return $totalAmount;
    }

    public function getRequestPrize(){
        return $this->iRequestPrize;
    }
    public function getAmount(){
        return $this->iAmount;
    }

    public function checkAmount(){
        $stages = $this->aBetData['stage'];
        if(!empty($stages) && is_array($stages))
            foreach($stages as $stage){
                if(!empty($stage))
                    foreach($stage as $wayId => $amount){
                        if($wayId == BlackJackWay::BLACKJACK_WAY_BET){
                            if($amount > $this->max_bet || $amount <$this->min_bet){
                                return false;
                            }

                        }else if($wayId == BlackJackWay::BLACKJACK_WAY_PAIR){
                            if($amount>$this->pair_max_bet || $amount<$this->pair_min_bet){
                                return false;
                            }
                        }
                    }
            }
        return true;
    }

}
