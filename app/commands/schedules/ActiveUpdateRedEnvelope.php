<?php

/**
 * 更新盈亏程序
 *
 * @author frank
 */
class ActiveUpdateRedEnvelope extends BaseTask {

    protected function doCommand() {
        
        return BaseTask::addTask('StatUpdateProfit', $aTaskData, 'stat');
//pr($datas);exit;
        extract($datas);
        if (!$user_id || !$username || !$turnover || !$lottery_id || !$way_id) {
            return "ERROR: Invalid Data, Exiting";
//            $this->log = "ERROR: Invalid Data, Exiting";
            return false;
        }
        //检查游戏玩法是不是在活动游戏里
        $ways = ActiveRedEnvelopeWay::isValidateWay($lottery_id, $way_id);
        if (!is_object($ways)) {
            return "玩法不在活动";
//            echo 3;
            return true;
        }
        //获取红包期id
//        $ActiveRedEnvelope=new ActiveRedEnvelope();
        $currentActive = ActiveRedEnvelope::getCurrentRedEnvelope();
        if (!is_object($currentActive)) {
            return "当前没有活动id";
//            echo 4;
//            $log = "ERROR: Invalid activeRedEnvelop, Exiting";
            return false;
        }
        //统计入库
        $oUser = User::find($user_id);
        // 更新用户盈亏数据
        if (!$bSucc = ActiveRedEnvelopeUser::updateUserActiveProfitData($currentActive, $oUser, $turnover)) {
            return "baocun 失败";
            return false;
        }
        // 更新用户盈亏数据
        if (!$bSucc = ActiveRedEnvelopeUser::updateUserActiveProfitData( $currentActive, $oUser, $turnover)) {
            $DB->rollback();
            $this->log = "User Profit Update Failed";
            return self::TASK_RESTORE;
        }

    }

}
