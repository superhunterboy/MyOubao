<?php

namespace JcModel;
/**
 * 合买模型
 */
class JcUserGroupBuy extends JcGroupBuy {
    
    const ERRNO_GROUP_BUY_IS_NOT_EXISTS = -10301;
    const ERRNO_SAVE_FAILED = -10302;
    const ERRNO_OVER_TOTAL_AMOUNT = -10303;
    const ERRNO_DROP_FAIL_OVER_LIMIT = -10304;
    const ERRNO_BIND_BET_FAILED = -10305;
    const ERRNO_BUY_AMOUNT = -10306;
    const ERRNO_SUCCESSED = -10307;
    const ERRNO_GUARANTEE_AMOUNT = -10308;
    const ERRNO_GROUP_BUY_PERMISSION_DENIED = -10309;
    const ERRNO_DROP_FAILED = -10310;
    const ERRNO_GUARANTEE_OVER_COUNT = -10311;

    /**
     * 合买大厅列表
        先排置顶方案； 注：（按进度从大到小排，满员后自动取消置顶）显示排序 >100
        再排未满员 合买方案； 注：（按进度从大到小排）显示排序  0-100
        先排中奖方案； 注：（按中奖金额从大到小排） 
        再排非中奖 合买满员方案； 注：（按方案发起时间越早的排在越后面） 
        再排所有撤单方案（含合买撤单方案）；  显示排序  -100
     * @param type $iLotteryId
     * @param type $aConditions
     * @param type $iPageSize
     * @param type $aColumns
     * @return type
     */
    public static function getListByLotteryIdAndStatus($iLotteryId = 0, $aConditions = [], $iPageSize = 30, $aColumns = ['*']){
        $oQuery = self::doWhere($aConditions)->where('lottery_id', $iLotteryId);
//        $oQuery->where('is_tester',0);
        $iUserId = \Session::get('user_id');
        $oUser = \User::find($iUserId);
        $sAllow = $oUser->parent_id ? $oUser->parent_id.','.$iUserId : $iUserId;
        $oQuery->whereRaw('(allow_type = ? or (allow_type = ? and user_id in (?)))',[self::ALLOW_TYPE_ALL,self::ALLOW_TYPE_CHILD,$sAllow]);

        $oQuery->orderby('sequence', 'desc');
        if ($sOorderColumn = \Input::get('sort_up', \Input::get('sort_down'))) {
            $sDirection = \Input::get('sort_up') ? 'asc' : 'desc';
            $oQuery->orderby($sOorderColumn, $sDirection);
        }else{
            \Request::merge(['sort_down' => 'progress']);
            $oQuery->orderby('progress', 'desc');
        }
        $oQuery->orderby('prize', 'desc');
        $oQuery->orderby('created_at', 'desc');
        $oQuery->with('userBet');
        return $oQuery->paginate($iPageSize, $aColumns);
    }
    
    /**
     * 合买大厅列表
        先排置顶方案； 注：（按进度从大到小排，满员后自动取消置顶）显示排序 >100
        再排未满员 合买方案； 注：（按进度从大到小排）显示排序  0-100
        先排中奖方案； 注：（按中奖金额从大到小排） 
        再排非中奖 合买满员方案； 注：（按方案发起时间越早的排在越后面） 
        再排所有撤单方案（含合买撤单方案）；  显示排序  -100
     * @param type $iLotteryId
     * @param type $aConditions
     * @param type $iPageSize
     * @param type $aColumns
     * @return type
     */
    public static function getForIndexByLotteryId($iLotteryId = 0, $aConditions = [], $iPageSize = 3, $aColumns = ['*']){
        $oQuery = self::doWhere($aConditions)->where('lottery_id', $iLotteryId);
        $oQuery->where('is_tester',0);
        $iUserId = \Session::get('user_id');
        $oUser = \User::find($iUserId);
        $oQuery->where('status', self::STATUS_NORMAL);
       if ($oUser){
            $sAllow = $oUser->parent_id ? $oUser->parent_id.','.$iUserId : $iUserId;
            $oQuery->whereRaw('(allow_type = ? or (allow_type = ? and user_id in (?)))',[self::ALLOW_TYPE_ALL,self::ALLOW_TYPE_CHILD,$sAllow]);
        }else{
            $oQuery->where('allow_type',self::ALLOW_TYPE_ALL);
        }

        $oQuery->limit($iPageSize);
        $oQuery->orderby('progress', 'desc');
        $oQuery->orderby('created_at', 'desc');
        return $oQuery->get($aColumns);
    }
    
    public function userBet(){
        return $this->belongsTo('\JcModel\JcUserBet', 'bet_id');
    }
    
    public function addGroupBuy(){
        if ($this->amount <= 0){
            return ERRNO_SAVE_FAILED;
        }
        $this->serial_number = self::makeSerialNumber($this->user_id);
        $fBuyAmount = $this->buy_amount;
        if ($fBuyAmount <= 0){
            return self::ERRNO_BUY_AMOUNT;
        }
        $fMinBuyRate = \SysConfig::readValue('jc_group_buy_min_buy_rate');
        if ($this->buy_amount / $this->amount < $fMinBuyRate){
            return self::ERRNO_BUY_AMOUNT;
        }
        if ($this->buy_amount > $this->amount){
            return self::ERRNO_BUY_AMOUNT;
        }
        $fGuaranteeAmount = $this->guarantee_amount;
        if ($fGuaranteeAmount > $this->amount){
            return self::ERRNO_GUARANTEE_AMOUNT;
        }
        if ($this->Account->available < $fBuyAmount + $fGuaranteeAmount){
            return JcUserBet::ERRNO_BET_ERROR_LOW_BALANCE;
        }
        $fMaxFeeRate = \SysConfig::readValue('jc_group_buy_max_fee_rate');
        if ($this->fee_rate < 0 || $this->fee_rate > $fMaxFeeRate){
            return self::ERRNO_SAVE_FAILED;
        }
        $this->progress =  $this->buy_amount / $this->amount * 100;
        if ($this->buy_amount >= $this->amount){
            //一开始就买满方案的情况，直接置为已满员
            $this->status = self::STATUS_AVAILABLE;
            $this->sequence = self::SEQUENCE_AVAILABLE;
        }
        $bSucc = $this->save();
//        var_dump($this->errors()->getMessages());die;
        if ($bSucc){
            $oProject = new \JcModel\JcProject($this->getAttributes());
            $oProject->setUser($this->User);
            $oProject->setAccount($this->Account);
            $oProject->group_id = $this->id;
            $oProject->amount = $this->buy_amount;
            $oProject->type = \JcModel\JcUserProject::TYPE_GROUP_BUY;
            $bSucc = $oProject->addProject() == \JcModel\JcProject::ERRNO_SUCCESSFUL;
//        var_dump($oProject->errors()->getMessages());die;
            if ($bSucc){
                $this->project_id = $oProject->id;
                $bSucc = $this->save();
                if ($bSucc && $this->guarantee_amount > 0){
                    $bSucc = $oProject->freezeForGuarantee(['amount' => $this->guarantee_amount]);
                }
            }
        }
        if ($bSucc){
            return self::ERRNO_SUCCESSED;
        }
        return self::ERRNO_SAVE_FAILED;
    }
    
    public function appendGuarantee($fGuaranteeAmount){
        $this->guarantee_count++;
        $this->guarantee_amount += $fGuaranteeAmount;
        return $this->save();
    }
    
    public function checkDisplayBet(){
        $iUserId = \Session::get('user_id');
        if ($this->user_id == $iUserId){
            return true;
        }
        if ($this->show_type == \JcModel\JcUserGroupBuy::SHOW_TYPE_PUBLIC_CODE){
            return true;
        }
        if ($this->show_type == \JcModel\JcUserGroupBuy::SHOW_TYPE_AFTER_FOLLOW_CODE){
            $bFollowed = \JcModel\JcUserProject::checkIsFollowed($this->id, $iUserId);
            if ($bFollowed){
                return true;
            }
        }else if ($this->show_type == \JcModel\JcUserGroupBuy::SHOW_TYPE_AFTER_END_CODE){
            if (date('Y-m-d H:i:s') > $this->end_time){
                return true;
            }
        }
        return false;
    }
    
    public function checkDrop(){
        $fRate = \SysConfig::readValue('jc_group_buy_limit_drop');
        $iUserId = \Session::get('user_id');
        if ($iUserId == $this->user_id && $this->status == \JcModel\JcUserGroupBuy::STATUS_NORMAL && $this->buy_amount < $this->amount * $fRate){
            return true;
        }
        return false;
    }
}
