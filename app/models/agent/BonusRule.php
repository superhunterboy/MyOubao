<?php

/**
 * Description of BonusRule
 *
 * @author frank
 */
class BonusRule extends BaseModel {
    protected $table            = 'bonus_rules';
    public static $resourceName = 'BonusRule';
    public static $treeable     = false;
    public static $sequencable  = false;
    protected $softDelete       = false;

    protected $fillable         = [
        'bonus_rate',
        'turnover',
        'deficit',
        'is_or',
    ];

    public static $columnForList = [
        'bonus_rate',
        'turnover',
        'deficit',
        'is_or',
    ];

    public static $listColumnMaps      = [
        'bonus_rate'          => 'rate_formatted',
        'turnover'      => 'turnover_formatted',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'bonus_rate'     => 'required|numeric|min:1|max:50',
        'turnover'        => 'required|integer',
        'deficit'        => 'required|integer',
        'is_or'          =>'in:0, 1',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'bonus_rate' => 'desc',
    ];

//    protected function beforeValidate(){
//        $this->rate < 1 or $this->rate /= 100;
//        $this->turnover >= 100000 or $this->turnover *= 10000;
//        return parent::beforeValidate();
//    }

    public static function getRuleObject($fRate) {
        $oRule = self::where('rate', '=', $fRate)->get()->first();
        is_object($oRule) or $oRule = new BonusRule(['rate' => $fRate]);
        return $oRule;
    }

    public static function getRuleObjectByProfit($fProfit) {
        $oBonusRule = self::where('turnover', '<=', $fProfit)->orderBy('turnover','desc')->first();
        is_object($oBonusRule) or $oBonusRule = null;
        return $oBonusRule;
    }

    public static function updateRule($fRate, $iTurnOver) {
        $oRule           = self::getRuleObject($fRate);
        $oRule->turnover = $iTurnOver;
        return $oRule->save();
    }

    protected function getRateFormattedAttribute() {
        return $this->attributes['bonus_rate'] * 100 . '%';
    }

    protected function getTurnoverFormattedAttribute() {
        return $this->attributes['turnover'] . ' 万';
    }

    /**
     * 获取分红比例
     * @param $turnover
     * @param $profit
     * @param $prizeGroup
     * @return int
     */
    public function getRate($turnover, $profit, $prizeGroup, $iType = 0){

        $iBonusRate = 0;
        $userBonusRate = [1958=>18, 1960=>20];
        if(!in_array($prizeGroup, array_keys($userBonusRate)) || $profit >= 0) return $iBonusRate;

        $absProfit = abs($profit);
        $aBonusRules = self::where('type',$iType)->orderby('deficit', 'desc')->get();

        foreach($aBonusRules as $aBonusRule)
        {
            if($profit < 0)
            {
                if($aBonusRule->is_or){
                    if($turnover >= $aBonusRule->turnover || $absProfit >= $aBonusRule->deficit)
                        $iBonusRate = $aBonusRule->bonus_rate;
                }else{
                    if($turnover >= $aBonusRule->turnover && $absProfit >= $aBonusRule->deficit){
                        $iBonusRate = $aBonusRule->bonus_rate;
                    }
                }
            }
            if($iBonusRate>0) break;
        }

        return $iBonusRate;
    }

    /**
     * 获取保底分红比例
     * @param $prizeGroup
     * @param $profit
     * @return int
     */
    public function getDeficitRate($prizeGroup, $profit, $iType = 0){
        $iBonusRate = 0;
        $userBonusRate = [1958=>18, 1960=>20];
        if(!in_array($prizeGroup, array_keys($userBonusRate)) || $profit >=0) {
            return $iBonusRate;
        }

        $absProfit = abs($profit);
        $aBonusRules = self::where('type',$iType)->get();

        foreach($aBonusRules as $aBonusRule){
            if($aBonusRule->deficit != 0 && $absProfit >= $aBonusRule->deficit){
                $iBonusRate = $aBonusRule->bonus_rate;
                break;
            }
        }

        if($iType == 0){
            return $iBonusRate > 0 ? $iBonusRate : $userBonusRate[$prizeGroup];
        }elseif($iType == 1){
            return $iBonusRate > 0 ? $iBonusRate : 15;
        }elseif($iType == 2){
            return $iBonusRate > 0 ? $iBonusRate : 30;
        }
    }
}
