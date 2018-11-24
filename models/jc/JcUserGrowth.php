<?php

namespace JcModel;
/**
 * 战绩统计模型
 */
class JcUserGrowth extends \BaseModel {
    public static $resourceName = 'JcUserGrowth';
    protected $table = 'jc_user_growth';

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    protected $fillable = [
        'lottery_id',
        'user_id',
        'method_group_id',
        'gold_growth',
        'silver_growth',
        'fake_gold_growth',
        'fake_silver_growth',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'user_id',
        'username',
        'method_group_id',
        'gold_growth',
        'silver_growth',
        'fake_gold_growth',
        'fake_silver_growth',
    ];

    public static $ignoreColumnsInEdit = [
        'gold_growth',
        'silver_growth',
        'last_update',
    ];
    
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'method_group_id' => 'required|integer',
        'lottery_id'              => 'required|integer',
        'user_id'             => 'required|integer',
        'gold_growth'  => 'integer',
        'silver_growth'  => 'integer',
        'fake_gold_growth'  => 'integer',
        'fake_silver_growth'  => 'integer',
    ];

    public static $htmlSelectColumns = [
        'lottery_id' => 'validLottery',
        'method_group_id' => 'validMethodGroup',
    ];
    
    
    public static $aGoldGrowthParams = [
        ['gains' => 500, 'times' => 10, 'growth' => 1],
        ['gains' => 5000, 'growth' => 10],
        ['gains' => 50000, 'growth' => 100],
        ['gains' => 500000, 'growth' => 1000],
    ];
    public static $aSilverGrowthParams = [
        ['gains' => 1000, 'times' => 10, 'growth' => 1],
        ['gains' => 10000, 'growth' => 10],
        ['gains' => 100000, 'growth' => 100],
        ['gains' => 1000000, 'growth' => 1000],
    ];
    
    protected function getUsernameAttribute($sUsername) {
        if (empty($sUsername)){
            $oUser = \User::find($this->user_id);
            if ($oUser){
                return $oUser->username;
            }
        }
        return $sUsername;
    }
    
    protected function getDisplayGoldGrowthAttribute($iGoldGrowth) {
        if (empty($iGoldGrowth)){
            if (isset($this->attributes['gold_growth'])){
                $iGoldGrowth = $this->attributes['gold_growth'];
            }
            if (isset($this->attributes['fake_gold_growth']) && $this->attributes['fake_gold_growth'] > 0){
                $iGoldGrowth = $this->attributes['fake_gold_growth'];
            }
            $this->display_gold_growth = $iGoldGrowth;
        }
        return $iGoldGrowth;
    }
    
    protected function getDisplaySilverGrowthAttribute($iSilverGrowth) {
        if (empty($iSilverGrowth)){
            if (isset($this->attributes['silver_growth'])){
                $iSilverGrowth = $this->attributes['silver_growth'];
            }
            if (isset($this->attributes['fake_silver_growth']) && $this->attributes['fake_silver_growth'] > 0){
                $iSilverGrowth = $this->attributes['fake_silver_growth'];
            }
            $this->display_silver_growth = $iSilverGrowth;
        }
        return $iSilverGrowth;
    }
    
    public static function getByLotteryIdAndUserIdAndMethodGroupId($iLotteryId, $iUserId, $iMethodGroupId){
        return self::where('user_id', $iUserId)
                ->where('lottery_id', $iLotteryId)
                ->where('method_group_id', $iMethodGroupId)
                ->first();
    }
    
    public static function getTotalGrowthByLotteryIdAndUserIds($iLotteryId, $aUserIds){
        $oQuery = self::whereIn('user_id', $aUserIds)
                ->where('lottery_id', $iLotteryId)
                ->get();
        
        $aUserGrowth = [];
        foreach($oQuery as $oRow){
            if (isset($aUserGrowth[$oRow->user_id])){
                $oUserGrowth = $aUserGrowth[$oRow->user_id];
                $oUserGrowth->display_gold_growth += $oRow->display_gold_growth;
                $oUserGrowth->display_silver_growth += $oRow->display_silver_growth;
            }else{
                $oUserGrowth = $oRow;
//                $oUserGrowth->display_gold_growth = $oRow->display_gold_growth;
//                $oUserGrowth->display_silver_growth = $oRow->display_silver_growth;
            }
            
            $aUserGrowth[$oRow->user_id] = $oUserGrowth;
        }
        return $aUserGrowth;
    }
    
    public static function getTotalGrowthByLotteryIdAndUserId($iLotteryId, $iUserId){
        $aUserGrowth = self::getTotalGrowthByLotteryIdAndUserIds($iLotteryId, [$iUserId]);
        return isset($aUserGrowth[$iUserId]) ? $aUserGrowth[$iUserId] : null;
    }
    
    public function addUserGrowth(){
        return $this->save();
    }
    
    public function saveUserGrowth(){
        if ($this->fake_gold_growth > 0){
            $iDiffGoldGrowth = $this->gold_growth - $this->getOriginal('gold_growth');
            $this->fake_gold_growth += $iDiffGoldGrowth;
        }
        if ($this->fake_silver_growth > 0){
            $iDiffSilverGrowth = $this->silver_growth - $this->getOriginal('silver_growth');
            $this->fake_silver_growth += $iDiffSilverGrowth;
        }
        return $this->save();
    }
    
    public static function countGoldGrowth($fGains, $fAmount = 0){
        $iGrowth = 0;
        foreach(self::$aGoldGrowthParams as $aGrowthParams){
            if (isset($aGrowthParams['times']) && $fAmount > 0 && $fGains / $fAmount  >= $aGrowthParams['times']){
                $iGrowth = $aGrowthParams['growth'];
            }
            if ($fGains >= $aGrowthParams['gains']){
                $iGrowth = $aGrowthParams['growth'];
            }
        }
        return $iGrowth;
    }
    
    public static function countSilverGrowth($fGains, $fAmount = 0){
        $iGrowth = 0;
        foreach(self::$aSilverGrowthParams as $aGrowthParams){
            if (isset($aGrowthParams['times']) && $fAmount > 0 && $fGains / $fAmount  >= $aGrowthParams['times']){
                $iGrowth = $aGrowthParams['growth'];
            }
            if ($fGains >= $aGrowthParams['gains']){
                $iGrowth = $aGrowthParams['growth'];
            }
        }
        return $iGrowth;
    }
}
