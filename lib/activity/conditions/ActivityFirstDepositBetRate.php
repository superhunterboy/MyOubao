<?php

class ActivityFirstDepositBetRate extends BaseActivityCondition {

    /**
     * 条件是否满足
     *
     * @return bool
     */
    public function complete($userCondition) {
        $userTask = $userCondition->userTask()->first();

        if ($userTask) {
            $aData = json_decode($userTask->data, true);
            $currentDateTime = $aData['begin_time'];
            $fTurnover = Project::getCurrentDayTurnover($userCondition->user_id, $currentDateTime);
            if ($aData['turnover'] <= $fTurnover)
                $userCondition->data = json_encode(['turnover' => $fTurnover]);
            return true;
        }

        return false;
    }

}
