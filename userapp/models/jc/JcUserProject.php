<?php

namespace JcModel;
/**
 * 注单模型
 */
class JcUserProject extends JcProject {
    
    public static function getList($aConditions, $iPageSize = 15, $aColumns = ['*']){
        return self::doWhere($aConditions)->with('userBet')->with('userGroupBuy')->orderby('id', 'desc')->paginate($iPageSize, $aColumns);
    }
    
    public static function getFollowListByGroupId($iGroupId, $aConditions = [], $iPageSize = 15, $aColumns = ['*']){
        return self::doWhere($aConditions)->where('group_id', $iGroupId)->orderby('id', 'desc')->paginate($iPageSize, $aColumns);
    }
    
    public static function getCountByGroupId($iGroupId){
        return self::where('group_id', $iGroupId)->count();
    }

    public static function checkIsFollowed($iGroupId, $iUserId){
        $oQuery = self::where('group_id', $iGroupId)->where('user_id', $iUserId)->first(['id']);
        return !empty($oQuery);
    }
    
    public function userBet(){
        return $this->belongsTo('\JcModel\JcUserBet', 'bet_id');
    }
    public function userGroupBuy(){
        return $this->belongsTo('\JcModel\JcUserGroupBuy', 'group_id');
    }
    
    public function checkDrop(){
        if ($this->group_id <= 0){
            return false;
        }
        $oGroupBuy = $this->userGroupBuy;
        if (empty($oGroupBuy)){
            $oGroupBuy = JcGroupBuy::find($this->group_id);
        }
        $iUserId = \Session::get('user_id');
        $fRate = \SysConfig::readValue('jc_group_buy_limit_drop');
        if (
            $oGroupBuy->status == JcGroupBuy::STATUS_NORMAL &&
            $oGroupBuy->buy_amount < $oGroupBuy->amount * $fRate && 
            $iUserId == $this->user_id && 
            $this->status == self::STATUS_NORMAL && 
            $this->type == self::TYPE_GROUP_BUY_FOLLOW && 
            $this->buy_type == self::BUY_TYPE_FOLLOW
        ){
            return true;
        }
        return false;
    }
}
