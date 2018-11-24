<?php

class MoneyBaseTask extends BaseTask {

    /**
     * 向任务队列追加销售额统计任务
     * @param string $sType
     * @param date $sDate
     * @param int $iUserId
     * @param float $fAmount
     * @param int $iLotteryId
     * @param string $sIssue
     * @return bool
     */
    protected function addProfitTask($sType, $sDate, $iUserId, $fAmount, $iLotteryId, $sIssue = null) {
        $aTaskData = [
            'type' => $sType,
            'user_id' => $iUserId,
            'amount' => $fAmount,
            'date' => substr($sDate, 0, 10),
            'lottery_id' => $iLotteryId,
            'issue' => $sIssue,
        ];
        return BaseTask::addTask('Stat' . ucfirst($sType) . 'UpdateProfit', $aTaskData, 'stat' . ucfirst($sType));
    }

}

?>