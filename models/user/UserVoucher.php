<?php

/**
 * 用户代金券
 */
class UserVoucher extends BaseModel {
//    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'UserVoucher';
    protected $table = 'user_vouchers';
    public static $amountAccuracy = 2;

    /**
     * the columns for list page
     * @var array
     */

    protected $fillable = [
        'user_id',
        'username',
        'voucher_id',
        'amount',
        'min_cost',
        'expire_time',
    ];
    public static $columnForList = [
        'id',
        'user_id',
        'username',
        'is_tester',
        'voucher_id',
        'amount',
        'cost_amount',
        'min_cost',
        'expire_time',
        'created_at',
        'updated_at',
    ];
    public static $ignoreColumnsInEdit = [
        'user_id',
        'username',
        'is_tester',
        'voucher_id',
        'amount',
        'cost_amount',
    ];
    public static $listColumnMaps = [
        'is_tester'     => 'is_tester_formatted',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];
    public static $htmlSelectColumns = [
        'voucher_id'     => 'aVouchers',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required',
        'voucher_id' => 'integer',
        'amount' => 'numeric|min:0',
        'cost_amount' => 'numeric|min:0',
        'min_cost' => 'numeric|min:0',
        'expire_time' => 'required',
    ];
    
    public function getIsTesterFormattedAttribute(){
        return yes_no(intval($this->is_tester));
    }
    
    public static function getUserVoucher($iUserId, $iVoucherId = null){
        $oQuery = self::where('user_id', $iUserId);
        if (isset($iVoucherId)){
            $oQuery->where('voucher_id', $iVoucherId);
        }
        return $oQuery->get();
    }
    
    public static function getVoucherAmount($iUserId, $iVoucherId = null){
        $oQuery = self::where('user_id', $iUserId)
                ->where('expire_time', '>', Carbon::now()->toDateTimeString());
        if (isset($iVoucherId)){
            $oQuery->where('voucher_id', $iVoucherId);
        }
        $iAmount = $oQuery->sum('amount');
        return $iAmount;
    }
    
    public static function getMinCost($iUserId, $iVoucherId = null){
        $oQuery = self::where('user_id', $iUserId)
                ->whereRaw('min_cost > cost_amount')
                ->where('min_cost', '>', 0);
        if (isset($iVoucherId)){
            $oQuery->where('voucher_id', $iVoucherId);
        }
        $iMinCost = $oQuery->sum('min_cost');
        return $iMinCost;
    }
    
    public static function decrementAmount($iUserId, $iLotteryId, $fAmount = 0, $iVoucherId = null) {
        if ($fAmount > 0){
            $oQuery = self::where('user_id', $iUserId)
                    ->where('amount', '>', 0)
                    ->where('expire_time', '>', Carbon::now()->toDateTimeString());
            if (isset($iVoucherId)){
                $oQuery->where('voucher_id', $iVoucherId);
            }
            $oResult = $oQuery->get();
            
            $fUserAmount = 0;
            foreach($oResult as $oUserVoucher){
                $fUserAmount += $oUserVoucher->amount;
            }
            if ($fUserAmount < $fAmount){
                return false;
            }
            
            $fTmpAmount = 0;
            foreach($oResult as $oUserVoucher){
                $fDecrementAmount = $fAmount - $fTmpAmount;
                if ($oUserVoucher->amount < $fDecrementAmount){
                    $fDecrementAmount = $oUserVoucher->amount;
                }
                $iResult = self::where('id', $oUserVoucher->id)
                        ->decrement('amount', $fDecrementAmount);
                if ($iResult){
                    $aLogData = $oUserVoucher->getAttributes();
                    $aLogData['amount'] = -$fDecrementAmount;
                    $aLogData['lottery_id'] = $iLotteryId;
                    unset($aLogData['id']);
                    $oUserVoucherLog = new UserVoucherLog($aLogData);
                    $iResult = $oUserVoucherLog->save();
                }
                if (!$iResult){
                    return $iResult;
                }
                $fTmpAmount += $fDecrementAmount;
                if ($fAmount == $fTmpAmount){
                    return true;
                }
            }
        }
    }
    
    public static function getUserCost($iUserId){
        $oUserVouchers = self::getUserVoucher($iUserId);
        $fSumCost = 0;
        foreach($oUserVouchers as $oUserVoucher){
            if ($oUserVoucher->min_cost <= 0 || $oUserVoucher->min_cost < $oUserVoucher->cost_amount){
                continue;
            }
            $oVoucher = Voucher::find($oUserVoucher->voucher_id);
            $iLotteryIdsSet = $oVoucher->lottery_ids_set;
            $sStartTime = $oUserVoucher->created_at;
            $sEndTime = $oUserVoucher->expire_time;
            if ($iLotteryIdsSet){
                $aLotteryIds = explode(',', $iLotteryIdsSet);
                $fSumCost = 0;
                foreach($aLotteryIds as $sKey => $iLotteryId){
                    if ($iLotteryId > 9000){
                        $fSumCost += \JcModel\JcProject::getSumCost($iUserId, $iLotteryId, $sStartTime, $sEndTime);
                        unset($aLotteryIds[$sKey]);
                    }
                }
                if (count($aLotteryIds) > 0){
                    //todo 后续再考虑其它彩种
//             Project::getSumCost($iUserId, $iLotteryId, $sStartTime, $sEndTime);
                }
            }
            if ($oUserVoucher->cost_amount != $fSumCost){
                $oUserVoucher->cost_amount = $fSumCost;
                $oUserVoucher->save();
            }
        }
        return $fSumCost;
    }
    
}
