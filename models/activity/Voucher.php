<?php

/**
 * 用户代金券
 */
class Voucher extends BaseModel {
//    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'Voucher';
    protected $table = 'vouchers';
    public static $amountAccuracy = 2;

    /**
     * the columns for list page
     * @var array
     */

    protected $fillable = [
        'title',
        'type',
        'amount',
        'bonus_rate',
        'min_cost_multiple',
        'lottery_ids_set' => '',
        'note',
        'start_time',
        'end_time',
    ];
    public static $columnForList = [
        'id',
        'title',
        'type',
        'amount',
        'bonus_rate',
        'max_bonus',
        'min_cost_multiple',
        'start_time',
        'end_time',
    ];

    public static $listColumnMaps = [
        'type'   => 'formatted_type',
    ];
    
    public static $htmlTextAreaColumns = [
        'note',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];
    public static $htmlSelectColumns = [
        'type'   => 'aTypes',
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
        'title' => 'required',
        'type' => 'required|integer|in:1,2',
        'amount' => 'min:0',
        'bonus_rate' => 'min:0',
        'max_bonus'  => 'numeric',
        'min_cost_multiple' => 'required|numeric|min:0',
        'lottery_ids_set' => '',
        'note' => '',
        'start_time' => 'required|date_format:Y-m-d H:i:s',
        'end_time' => 'required|date_format:Y-m-d H:i:s',
    ];
    
    const VIRTUAL_SPORT_AGENT = 'xunisports';
    const MIN_SPORT_ODDS = 1.5;
    
    const TYPE_FIXED = 1;
    const TYPE_FLOATING = 2;
    
    public static $validTypes = [
        self::TYPE_FIXED => 'Fixed',
        self::TYPE_FLOATING => 'Floating',
    ];
    
    protected function getFormattedTypeAttribute() {
        if (isset(self::$validTypes[$this->type])){
            self::comaileLangPack();
            return self::translate(self::$validTypes[$this->type]);
        }
        return '';
    }
    
    public function getExtraData($fDepositAmount = null){
        if ($this->type == Voucher::TYPE_FIXED){
            $fSendAmount = $this->amount;
            $fMinCost = $fSendAmount * $this->min_cost_multiple;
        }else if ($this->type == Voucher::TYPE_FLOATING){
            if ($fDepositAmount <= 0){
                return false;
            }
            $fSendAmount = $fDepositAmount * $this->bonus_rate;
            if ($this->max_bonus > 0 && $fSendAmount > $this->max_bonus){
                $fSendAmount = $this->max_bonus;
            }
            $fMinCost = ($fDepositAmount + $fSendAmount) * $this->min_cost_multiple;
        }else{
            return false;
        }
        $this->send_amount = $fSendAmount;
        $this->min_cost = $fMinCost;
    }
    
    public function sendVoucher($oUser, $fDepositAmount = null){
        $this->getExtraData($fDepositAmount);
        if ($this->send_amount <= 0){
            return false;
        }
        if (date('Y-m-d H:i:s') > $this->end_time){
            return false;
        }
        
        $sExpireTime =  date('Y-m-d H:i:s', time() + 86400 * 15);
        $aData = [
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'is_tester' => $oUser->is_tester,
            'voucher_id' => $this->id,
            'amount' => $this->send_amount,
            'min_cost' => $this->min_cost,
            'expire_time' => $sExpireTime,
        ];

        $oUserVoucher = new UserVoucher($aData);
        $bSucc = $oUserVoucher->save();
        if ($bSucc){
            $aLogData = $oUserVoucher->getAttributes();
            unset($aLogData['id']);
            $oUserVoucherLog = new UserVoucherLog($aLogData);
            $bSucc = $oUserVoucherLog->save();
        }
        return $bSucc;
    }
}
