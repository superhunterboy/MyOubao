<?php
/**
 * Created by PhpStorm.
 * User: endless
 * Date: 15-12-9
 * Time: 下午3:14
 */

namespace JcModel;


class ManJcBetsDetail extends JcBetsDetail
{
    public static function getBetsDetailByBetId($bet_id){
        return self::where('bet_id','=',$bet_id)->get(['*']);
    }

    public static function getSingleSum($bet_id){
        return self::where('bet_id',$bet_id)
                ->whereIn('status',[self::STATUS_LOST, self::STATUS_WON, self::STATUS_PRIZE_SENT])
                ->where('total_matches','=',1)
                ->sum('amount');
    }

    public static function getMultipleSum($bet_id){
        return self::where('bet_id',$bet_id)
                ->whereIn('status',[self::STATUS_LOST, self::STATUS_WON, self::STATUS_PRIZE_SENT])
                ->where('total_matches','>',1)
                ->sum('amount');
    }
    public static function getListByBetId($iBetId, $iPageSize = 20, $aColumns = ['*']){
        return self::where('bet_id', $iBetId)
//            ->orderby('id', 'asc')
            ->orderby('prize', 'desc')
            ->paginate($iPageSize, $aColumns);
    }
    protected function getFormattedStatusAttribute() {
        if (isset(self::$validStatuses[$this->attributes['status']])){
            return __('_manjcbet.' . strtolower(self::$validStatuses[$this->attributes['status']]));
        }
        return '';
    }


}