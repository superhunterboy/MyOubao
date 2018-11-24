<?php

/**
 * Class EventPrizeQueue - 礼物发放
 *
 * @author Johnny <Johnny@anvo.com>
 */
class EventPrizeQueue extends BaseTask {

    /**
     * 处理任务
     *
     * @return int
     */
    public function doCommand(& $sMsg = null) {

        $data = $this->data;
        $userPrize = ActivityUserPrize::find($data['id']);

        if (!is_object($userPrize)) {
            $this->log .= 'missing userprize ';
            return self::TASK_SUCCESS;
        }
        if ($userPrize->status == ActivityUserPrize::STATUS_SENT) {
            $this->log .= 'userprize sent already';
            return self::TASK_SUCCESS;
        }


        //创建处理类
        try {
            $object = FactoryClass::Factory($userPrize->prize()->first());
        } catch (Exception $e) {
            $this->log .= 'Create Class error: ' . $e->getMessage();
            return self::TASK_SUCCESS;
        }

        try {
            //载入需要处理的类
            $object->load($userPrize);
        } catch (Exception $e) {
            $this->log .= 'Load exception error: ' . $e->getMessage();
            return self::TASK_RESTORE;
        }

        DB::connection()->beginTransaction();
        try {
            if (!$object->process()) {
                $this->log .= json_encode($object->messages()->getMessages());
                throw new Exception("User prize [id: {$userPrize->id}] send failed");
            }
            $userPrize->status = ActivityUserPrize::STATUS_SENT;
            $bSucc = $userPrize->save();
            if (!$bSucc) {
                $this->log .= 'save user prize error';
                throw new Exception("User prize [id: {$userPrize->id}] send failed");
            }
//            if (get_class($object) == 'ActivityPrizeMoney') {
//                $aData = json_decode($userPrize->data, true);
//                $fAmount = is_null($aData) ? $userPrize->prize()->first()->value : array_get($aData, $object->data->get('amount_column'));
//                $this->addProfitTask('bonus', date('Y-m-d'), $userPrize->user_id, $fAmount);
//            }
            DB::connection()->commit();
            $userPrize->addPromoBonusTask();
            return self::TASK_SUCCESS;
        } catch (Exception $e) {
            DB::connection()->rollBack();
            $this->log .= 'Exception abnormal: ' . $e->getMessage();
            return self::TASK_RESTORE;
        }
    }

}
