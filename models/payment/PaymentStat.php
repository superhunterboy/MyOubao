<?php

/**
 * 支付金额记录
 *
 * @author white
 */
class PaymentStat extends BaseModel {

    protected $table                 = 'payment_stats';
    public static $resourceName      = 'PaymentStat';
    protected $fillable              = [
        'date',
        'platform_id',
        'platform',
        'platform_identifier',
        'total_count',
        'amount',
        'created_at',
        'updated_at',
    ];

    public static $rules             = [
        'date'              => 'required|date',
        'platform_id'    => 'required|integer',
        'platform'       => 'required|max:50',
        'total_count' => 'required|integer|min:0',
        'amount' => 'required|numeric|min:0',
    ];
    public $orderColumns             = [
        'platform_id' => 'asc',
        'date' => 'desc',
    ];
    public static $mainParamColumn   = 'platform_id';
    public static $titleColumn       = 'date';

    public static function getObject($oPaymentAccount,$sDate) {
        $obj = self::where('platform_id','=',$oPaymentAccount->platform_id)->where('date', '=', $sDate)->get()->first();

        if (!is_object($obj)) {
            $data = [
                'platform_id' => $oPaymentAccount->platform_id,
                'platform' => $oPaymentAccount->platform,
                'platform_identifier' => $oPaymentAccount->platform_identifier,
                'date' => $sDate,
            ];
            $obj  = new static($data);
        }
        return $obj;
    }

    public function addAmount($fAmount){
        $this->amount += $fAmount;
        $this->total_count ++;
        return $this->save();
    }
}
