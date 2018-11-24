<?php

/**
 * 首充送报名队列处理
 *
 */
class SignTaskQueue extends BaseTask {

    /**
     * 处理任务
     * @return int
     */
    public function doCommand() {
        $data = $this->data;
        $aCondition = [
            'user_id' => $data['user_id'],
            'task_id' => $data['task_id'],
            'activity_id' => $data['activity_id'],
        ];
        $oUserTask = ActivityUserTask::getObjectByParams($aCondition);
        if (is_object($oUserTask)) {
            return self::TASK_SUCCESS;
        }
        // 充值金额大于100，报名成功
        if ($data['amount'] < 100) {
            return self::TASK_SUCCESS;
        }
        $oUserTask = new ActivityUserTask();
        $oUserTask->user_id = $data['user_id'];
        $oUserTask->task_id = $data['task_id'];
        $oUserTask->activity_id = $data['activity_id'];
        $oUserTask->signed_time = date('Y-m-d H:i:s');
        $oUserTask->is_signed = 1;
        $oUserTask->status = 0;
        if ($oUserTask->save()) {
            return self::TASK_SUCCESS;
        } else {
            return self::TASK_RESTORE;
        }
    }

}
