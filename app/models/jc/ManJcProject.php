<?php
namespace JcModel;
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-7
 * Time: 上午10:51
 */
class ManJcProject extends JcProject
{
    
    public static $columnForList = [
        'id',
        'serial_number',
        'lottery_id',
        'username',
        'bet_id',
        'group_id',
        'method_group_id',
        'amount',
        'prize',
        'status',
        'prize_status',
        'commission_status',
        'type',
        'buy_type',
        'created_at',
        'updated_at',
    ];
    
    public $orderColumns = [
        'id' => 'desc',
    ];

    public static $listColumnMaps = [
        'serial_number' => 'formatted_serial_number',
        'status' => 'formatted_status',
        'prize_status' => 'formatted_prize_status',
        'commission_status' => 'formatted_commission_status',
        'type' => 'formatted_type',
        'buy_type' => 'formatted_buy_type',
    ];
    
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'validLotteries',
        'status' => 'aStatusDesc',
        'coefficient' => 'aCoefficients',
        'prize_status' => 'validPrizeStatus',
        'commission_status' => 'validCommissionStatus',
        'type' => 'validType',
        'buy_type' => 'validType',
        'method_group_id' => 'validMethodGroups',
    ];

    public static function getUnCalculateByGroupId($iGroupId, $iLimit = 0){
        $oQueryWhere = self::where('group_id', $iGroupId);
        $oQueryWhere->where('status', self::STATUS_NORMAL);
        if ($iLimit > 0){
            $oQueryWhere->limit($iLimit);
        }
        return $oQueryWhere->get();
    }
    
    public static function getUnPrizeListByGroupId($iGroupId, $iLimit = 0){
        $oQueryWhere = self::where('group_id', $iGroupId);
        $oQueryWhere->where('status', self::STATUS_WON);
        if ($iLimit > 0){
            $oQueryWhere->limit($iLimit);
        }
        return $oQueryWhere->get();
    }
    
    public static function getUncalculateCommisionByGroupId($iGroupId = 0){
        $oQuery = self::where('group_id',$iGroupId)->where('commission_status','=',self::COMMISSION_STATUS_NORMAL)->get();
        return $oQuery;
    }
    public static function getUncalculateCommisionByIds($ids){
        $oQuery = self::whereIn('id',$ids)
                ->whereIn('status', [self::STATUS_NORMAL, self::STATUS_LOST, self::STATUS_WON, self::STATUS_PRIZE_SENT])
                ->where('commission_status','=',self::COMMISSION_STATUS_NORMAL)
                ->get();
        return $oQuery;
    }

    public function setCommissionCalculated(){
        $this->commission_status = self::COMMISSION_STATUS_CALCULATED;
        return $this->save();
    }
    
    public static function sumTotalAmountByGroupId($iGroupId = 0){
        return self::where('group_id', $iGroupId)->where('status', self::STATUS_NORMAL)->sum('amount');
    }
    
    public static function countDropedByUserIdAndDate($iUserId = 0, $dDate = null){
        $dStartTime = date('Y-m-d', strtotime($dDate));
        $dEndTime = date('Y-m-d', strtotime($dDate) + 86400);
        return self::where('user_id', $iUserId)
                ->whereIn('type', [\JcModel\ManJcProject::TYPE_SELF_BUY, \JcModel\ManJcProject::TYPE_GROUP_BUY])
                ->whereIn('status', [\JcModel\ManJcProject::STATUS_DROPED, \JcModel\ManJcProject::STATUS_DROPED_BY_SYSTEM])
                ->where('created_at', '>=', $dStartTime)
                ->where('created_at', '<', $dEndTime)
                ->count();
    }

}
