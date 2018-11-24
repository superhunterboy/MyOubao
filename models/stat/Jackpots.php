<?php
class Jackpots extends BaseModel{
//    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'jackpots';
    protected $fillable = [
        'status',
        'init_pool_amount',
        'profit_rate',
        'max_prize_rate',
        'lotteries_limit',
        'project_amount_min',
        'number_count',
//        'access_frequency',
//        'max_waiting_time',
        'max_amount',
        'locked',
        'updated_at',
        'created_at',
        'enable_at',
        'end_at',
        'is_filter_tester'
    ];
    public static $resourceName = 'Jackpots';
    public static $columnForList = [
        'id',
        'status',
        'init_pool_amount',
        'profit_rate',
        'max_prize_rate',
        'lotteries_limit',
        'project_amount_min',
        'number_count',
//        'access_frequency',
//        'max_waiting_time',
        'max_amount',
//        'locked',
        'updated_at',
        'created_at',
        'enable_at',
        'end_at',
        'is_filter_tester'
    ];
    public static $listColumnMaps = [
         'lotteries_limit' => 'formatted_lotteries_limit',
    ];
    public static $ignoreColumnsInView = [];
    public static $ignoreColumnsInEdit = [];
    public static $htmlSelectColumns = [
//        'parent_id' => 'aParentIds',
//        'blocked' => 'aBlockedTypes',
    ];
    public static $rules = [
        'status' => 'in:0, 1',
        'init_pool_amount' => 'required|regex:/^-?[0-9]+(.[0-9]{1,2})?$/',
        'profit_rate' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'max_prize_rate' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'lotteries_limit' => 'required',
        'project_amount_min' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'number_count' => 'required|integer',
//        'access_frequency' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
//        'max_waiting_time' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'max_amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'enable_at' => 'regex:/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/',
        'end_at' => 'regex:/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/',
        'is_filter_tester' => 'in:0, 1'
//        'locked' => 'in:0, 1'
    ];



    public static function isExist($id='', $lotteries_limit){
        $aConditions = [
            'id' => ['<>',$id],
            'lotteries_limit' => ['=',$lotteries_limit]
        ];
        return $iCount = self::doWhere($aConditions)->count();
    }

    public static function lock($id){
        Cache::forever('jackpot_lock_'.$id, 1);

        return Jackpots::doWhere([
            'id' => ['=',$id]
        ])->update(['locked'=>1]);
    }

    public static function unlock($id){
        Cache::forever('jackpot_lock_'.$id, 0);

        return Jackpots::doWhere([
            'id' => ['=',$id]
        ])->update(['locked'=>0]);
    }

    public static function getAvailableJackpotByLottery($iLotteryId){
        return $aJackpots = self::doWhere(['status' => ['=',1],'lotteries_limit' => ['=',$iLotteryId]])->first();
    }

    protected function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        $aJackpotSetting = $oSavedModel->attributes;

        $iLotteryId = $aJackpotSetting['lotteries_limit'];

        Cache::forever('jackpot_'.$iLotteryId, $aJackpotSetting['id']);
        $iJackpotId = Cache::get('jackpot_'.$iLotteryId);
        Cache::forever('jackpot_status_'.$iJackpotId, $aJackpotSetting['status']);
        Cache::forever('jackpot_init_pool_amount_'.$iJackpotId, $aJackpotSetting['init_pool_amount']);
        Cache::forever('jackpot_profit_rate_'.$iJackpotId, $aJackpotSetting['profit_rate']);
        Cache::forever('jackpot_max_prize_rate_'.$iJackpotId, $aJackpotSetting['max_prize_rate']);
        Cache::forever('jackpot_project_amount_min_'.$iJackpotId, $aJackpotSetting['project_amount_min']);
        Cache::forever('jackpot_number_count_'.$iJackpotId, $aJackpotSetting['number_count']);
//        Cache::forever('jackpot_access_frequency_'.$iJackpotId, $aJackpotSetting['access_frequency']);
        Cache::forever('jackpot_max_amount_'.$iJackpotId, $aJackpotSetting['max_amount']);
        Cache::forever('jackpot_locked_'.$iJackpotId, 0);
        Cache::forever('jackpot_enable_at_'.$iJackpotId, $aJackpotSetting['enable_at']);
        Cache::forever('jackpot_end_at_'.$iJackpotId, $aJackpotSetting['end_at']);
        Cache::forever('jackpot_is_filter_tester_'.$iJackpotId, $aJackpotSetting['is_filter_tester']);

    }

    protected function getFormattedLotteriesLimitAttribute() {
        $oLottery = Lottery::find($this->attributes['lotteries_limit']);
        return $oLottery->identifier;
    }

}
