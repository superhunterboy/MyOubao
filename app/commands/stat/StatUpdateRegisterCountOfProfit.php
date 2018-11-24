<?php
/**
 * 更新日度盈亏中的注册用户数程序
 *
 * @author white
 */
class StatUpdateRegisterCountOfProfit extends BaseTask {

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
        $oUser = User::find($user_id);
        $bTopAgent = empty($oUser->parent_id);
        $DB->beginTransaction();

        // 更新
        if (!$bSucc = Profit::updateRegisterCount($date, $bTopAgent)) {
            $DB->rollback();
            $this->log = "Registered Count Update Failed";
            return self::TASK_RESTORE;
        }
        $DB->commit();
        $this->log = "Registered Count Update Success";
        return self::TASK_SUCCESS;
    }

}
