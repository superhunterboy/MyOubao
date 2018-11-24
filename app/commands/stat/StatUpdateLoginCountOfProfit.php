<?php
/**
 * 更新日度盈亏中的登录用户数程序
 *
 */
class StatUpdateLoginCountOfProfit extends BaseTask {

    protected function doCommand() {
//        pr($this->job->id);
        extract($this->data);
//        pr($this->data);
//        exit;
        $date = substr($date, 0, 10);

        if (!$user_id || !$date) {
            $this->log = "ERROR: Invalid Data, Exiting";
            return self::TASK_SUCCESS;
        }
        $DB = DB::connection();
        $DB->beginTransaction();

        // 更新日盈亏数据
        if (!$bSucc = Profit::updateSignedCount($date, $user_id)) {
            $DB->rollback();
            $this->log = "Signed Count Update Failed";
            return self::TASK_RESTORE;
        }
        $DB->commit();
        $this->log = "Signed Count Update Success";
        return self::TASK_SUCCESS;
    }

}
