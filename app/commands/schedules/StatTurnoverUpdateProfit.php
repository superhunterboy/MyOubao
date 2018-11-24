<?php

/**
 * 更新盈亏程序
 *
 */
class StatTurnoverUpdateProfit extends BaseTask {

    protected function doCommand() {
//        pr($this->job->id);
        extract($this->data);
//        pr($this->data);
//        exit;
//        !empty($date) or $date = date('Y-m-d');
        $date = substr($date, 0, 10);

        if (!$type || !$user_id || !$amount || !$date) {
            $this->log = "ERROR: Invalid Data, Exiting";
            return self::TASK_SUCCESS;
        }
        $DB = DB::connection();
        $oUser = User::find($user_id);
//        pr($oUser->toArray());
        $DB->beginTransaction();
//        $type  = $this->data['type'];

        // 更新用户盈亏数据
        if (!$bSucc = UserProfit::updateProfitData($type, $date, $oUser, $amount)) {
            $DB->rollback();
            $this->log = "User Profit Update Failed";
            return self::TASK_RESTORE;
        }

        // 更新代理盈亏数据
        if (!$bSucc = TeamProfit::updateProfitData($type, $date, $oUser, $amount)) {
            $DB->rollback();
            $this->log = "Team Profit Update Failed";
            return self::TASK_RESTORE;
        }

        // 更新日盈亏数据
        if (!$bSucc = Profit::updateProfitData($type, $date, $amount, $oUser)) {
            $DB->rollback();
            $this->log = "Profit Update Failed";
            return self::TASK_RESTORE;
        }

        if (!isset($lottery_id)) {
            $DB->commit();
            return self::TASK_SUCCESS;
        }

        // 更新与奖期关联的销售额
        if ($type == 'turnover') {
            if (!$bSucc = UserTurnover::updateTurnoverData($lottery_id, $issue, $user_id, $amount)) {
                $DB->rollback();
                $this->log = "User Turnover Update Failed";
                return self::TASK_RESTORE;
            }
        }

        // 更新彩种数据 $sType, $sDate, $iLotteryId, $oUser, $fAmount
        if (!$bSucc = LotteryProfit::updateProfitData($type, $date, $lottery_id, $oUser, $amount)) {
            $DB->rollback();
            $this->log = "ManLottery Profit Update Failed";
            return self::TASK_RESTORE;
        }

        // 更新奖期数据
        if ($lottery_id != 18 && !$bSucc = IssueProfit::updateProfitData($type, $lottery_id, $issue, $oUser, $amount)) {
            $DB->rollback();
            $this->log = "Issue Profit Update Failed";
            return self::TASK_RESTORE;
        } else {
            $this->log = "Successful";
            $DB->commit();
            return self::TASK_SUCCESS;
        }
    }

}
