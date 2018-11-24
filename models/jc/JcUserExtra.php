<?php

namespace JcModel;
/**
 * 用户竞彩数据模型
 */
class JcUserExtra extends \BaseModel {
    public static $resourceName = 'JcUserExtra';
    
    
    protected $fillable = [
        'lottery_id',
        'user_id',
        'method_group_id',
    ];
    
    protected function getBetCountAttribute($iBetCount) {
        if (!isset($iBetCount)){
            $iBetCount = JcBet::getBetCount($this->lottery_id, $this->user_id);
        }
        return $iBetCount;
    }
    protected function getWonCountAttribute($iWonCount) {
        if (!isset($iWonCount)){
            $iWonCount = JcBet::getWonCount($this->lottery_id, $this->user_id);
        }
        return $iWonCount;
    }
    protected function getWonPrizeAttribute($fWonPrize) {
        if (!isset($fWonPrize)){
            $fWonPrize = JcBet::getWonPrize($this->lottery_id, $this->user_id);
        }
        return $fWonPrize;
    }
    protected function getSuccessPercentAttribute($fSuccessPercent) {
        if (!isset($fSuccessPercent)){
            $iGroupBuyCount = JcGroupBuy::getCountByLotteryIdAndUserId($this->lottery_id, $this->user_id);
            if ($iGroupBuyCount){
                $iGroupBuySuccessCount = JcGroupBuy::getSuccessCountByLotteryIdAndUserId($this->lottery_id, $this->user_id);
                $fSuccessPercent =  $iGroupBuySuccessCount / $iGroupBuyCount * 100;
            }else{
                $fSuccessPercent = 0;
            }
            $fSuccessPercent = number_format($fSuccessPercent, 2);
            $this->sucess_percent = $fSuccessPercent;
        }
        return $fSuccessPercent;
    }


    public function getGrowthAttribute($oGrowth){
        if (!isset($oGrowth)){
            if ($this->method_group_id > 0){
                $oGrowth = JcUserGrowth::getByLotteryIdAndUserIdAndMethodGroupId($this->lottery_id, $this->user_id, $this->method_group_id);
            }else{
                $oGrowth =  JcUserGrowth::getTotalGrowthByLotteryIdAndUserId($this->lottery_id, $this->user_id);
            }
            $this->growth = $oGrowth;
        }
        return $oGrowth;
    }
    public function getDisplayGoldGrowthAttribute($iGrowth){
        if (!isset($iGrowth)){
            if (isset($this->growth->display_gold_growth)){
                $iGrowth =  $this->growth->display_gold_growth;
                $this->display_gold_growth = $iGrowth;
            }
        }
        return $iGrowth;
    }
    public function getDisplaySilverGrowthAttribute($iGrowth){
        if (!isset($iGrowth)){
            if (isset($this->growth->display_silver_growth)){
                $iGrowth =  $this->growth->display_silver_growth;
                $this->display_silver_growth = $iGrowth;
            }
        }
        return $iGrowth;
    }
    
}
