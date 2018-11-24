<?php
namespace JcModel;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-7
 * Time: 上午10:51
 */
class ManJcGroupBuy extends JcGroupBuy
{

    public static $columnForList = [
        'id',
        'serial_number',
        'username',
        'amount',
        'buy_amount',
        'buy_percent',
        'guarantee_amount',
        'guarantee_percent',
        'show_type',
        'end_time',
        'status',
        'prize_status',
        'commission_status',
        'fee_rate',
        'fee_amount',
        'sequence',
        'prize',
        'created_at',
    ];
    
    public static $sequencable = true;
    
    public $orderColumns = [
        'id' => 'desc',
    ];
    
    public static $listColumnMaps = [
        'show_type' => 'formatted_show_type',
        'status' => 'formatted_status',
        'prize_status' => 'formatted_prize_status',
        'commission_status' => 'formatted_commission_status',
    ];

    protected function getFormattedPrizeStatusAttribute() {
        if (isset(self::$validPrizeStatus[$this->attributes['prize_status']])){
            self::comaileLangPack();
            return self::translate(self::$validPrizeStatus[$this->attributes['prize_status']]);
        }
        return '';
    }
    
    protected function getFormattedCommissionStatusAttribute() {
        if (isset(self::$validCommissionStatus[$this->attributes['commission_status']])){
            self::comaileLangPack();
            return self::translate(self::$validCommissionStatus[$this->attributes['commission_status']]);
        }
        return '';
    }

    public static function getSellingList(){
        return self::whereIn('status',[self::STATUS_NORMAL, self::STATUS_AVAILABLE])->where('is_finished', 0)->get();
    }

}