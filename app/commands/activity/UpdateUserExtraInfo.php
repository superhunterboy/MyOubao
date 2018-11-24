<?php

/**
 * Class EventTaskQueue - 任务事件处理队列
 *
 * @author Johnny <Johnny@anvo.com>
 */
class UpdateUserExtraInfo extends BaseTask {

    /**
     * 处理任务
     *
     * @return int
     */
    public function doCommand() {
        extract($this->data, EXTR_PREFIX_ALL, 'user');

        if (!$user_id)
            return self::TASK_SUCCESS;

        isset($user_buy_prize) or $user_buy_prize = 0;

        $oUser = User::find($user_id);
        $oUserExtraInfo = UserExtraInfo::findByUser($oUser);
        $bReturn = $oUserExtraInfo->increaseContribution($user_buy_prize);
        $this->log = $bReturn ? 'true' : 'false';
        $this->log .= ', username=' . $oUser->username . ", project price=" . $user_buy_prize;
        return $bReturn ? self::TASK_SUCCESS : self::TASK_RESTORE;
    }

}
