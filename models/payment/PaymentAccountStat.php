<?php

/**
 * 支付金额记录
 *
 * @author white
 */
class PaymentAccountStat extends BaseModel {

    protected $table                 = 'payment_account_stats';
    public static $resourceName      = 'PaymentAccountStat';
    protected $fillable              = [
        'date',
        'platform_id',
        'platform',
        'platform_identifier',
        'account_id',
        'account',
        'ceiling',
        'total_count',
        'amount',
        'created_at',
        'updated_at',
    ];

    public static $rules             = [
        'date'              => 'required|date',
        'platform_id'    => 'required|integer',
        'platform'       => 'required|max:50',
        'account_id' => 'required|integer|min:0',
        'account' => 'required|max:32',
        'total_count' => 'required|integer|min:0',
        'amount' => 'required|numeric|min:0',
        'ceiling' => 'integer|min:0'
    ];
    public $orderColumns             = [
        'platform_id' => 'asc',
        'date' => 'desc',
        'account_id' => 'asc',
    ];
    public static $mainParamColumn   = 'platform_id';
    public static $titleColumn       = 'date';
    public $resetAccount = false;
    private $Account;
    private $addedAmount = 0;
    
    protected function afterSave($oSavedModel) {
        parent::afterSave($oSavedModel);
        if ($oSavedModel->resetAccount){
            $this->Account->resetCurrentTotalAmount($this->amount);
        }
        else{
            $this->Account->updateCurrentTotalAmount($this->addedAmount);
        }
        $oPaymentStat = PaymentStat::getObject($this->Account, $this->date);
        $oPaymentStat->addAmount($this->addedAmount);
    }
    public static function getObject($oPaymentAccount,$sDate) {
        $obj = self::where('account_id','=',$oPaymentAccount->id)->where('date', '=', $sDate)->get()->first();

        if (!is_object($obj)) {
            $data = [
                'platform_id' => $oPaymentAccount->platform_id,
                'platform' => $oPaymentAccount->platform,
                'platform_identifier' => $oPaymentAccount->platform_identifier,
                'date' => $sDate,
                'account_id' => $oPaymentAccount->id,
                'account' => $oPaymentAccount->account,
                'ceiling' => $oPaymentAccount->ceiling,
            ];
            $obj  = new static($data);
        }
        $obj->Account = $oPaymentAccount;
        return $obj;
    }

    public function addAmount($fAmount){
        $this->resetAccount = empty($this->id);
        $this->amount += $this->addedAmount = $fAmount;
        $this->total_count ++;
        return $this->save();
    }
}
